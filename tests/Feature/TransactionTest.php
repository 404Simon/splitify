<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Group;
use App\Models\Transaction;
use App\Models\User;

test('user can successfully create transaction with valid data', function () {
    $user = User::factory()->create();
    $recipient = User::factory()->create();
    $group = Group::factory()->createdBy($user)->create();
    $group->users()->attach($recipient);

    $this->actingAs($user);

    $response = $this->post("/groups/{$group->id}/transactions", [
        'recipient_id' => $recipient->id,
        'amount' => 75.50,
        'description' => 'PayPal payment',
    ]);

    $response->assertRedirect("/groups/{$group->id}")
        ->assertSessionHas('success', 'Transaction added successfully!');

    expect(Transaction::where('description', 'PayPal payment')->exists())->toBeTrue();
    expect(Transaction::where('amount', 75.50)->first()->payer_id)->toBe($user->id);
    expect(Transaction::where('amount', 75.50)->first()->recipient_id)->toBe($recipient->id);
});

test('transaction validation requires recipient selection', function () {
    $user = User::factory()->create();
    $group = Group::factory()->createdBy($user)->withUsers(1)->create();

    $this->actingAs($user);

    $response = $this->post("/groups/{$group->id}/transactions", [
        'amount' => 50.00,
        'description' => 'Test payment',
    ]);

    $response->assertRedirect()
        ->assertSessionHasErrors(['recipient_id']);
});

test('transaction validation requires positive amount', function () {
    $user = User::factory()->create();
    $recipient = User::factory()->create();
    $group = Group::factory()->createdBy($user)->create();
    $group->users()->attach($recipient);

    $this->actingAs($user);

    $response = $this->post("/groups/{$group->id}/transactions", [
        'recipient_id' => $recipient->id,
        'amount' => -25.00,
        'description' => 'Invalid amount',
    ]);

    $response->assertRedirect()
        ->assertSessionHasErrors(['amount']);
});

test('transaction validation requires valid recipient exists', function () {
    $user = User::factory()->create();
    $group = Group::factory()->createdBy($user)->withUsers(1)->create();

    $this->actingAs($user);

    $response = $this->post("/groups/{$group->id}/transactions", [
        'recipient_id' => 999999, // Non-existent user
        'amount' => 50.00,
        'description' => 'Test payment',
    ]);

    $response->assertRedirect()
        ->assertSessionHasErrors(['recipient_id']);
});

test('user cannot create transaction with recipient not in group', function () {
    $user = User::factory()->create();
    $outsideUser = User::factory()->create();
    $group = Group::factory()->createdBy($user)->withUsers(1)->create();

    $this->actingAs($user);

    $response = $this->post("/groups/{$group->id}/transactions", [
        'recipient_id' => $outsideUser->id,
        'amount' => 50.00,
        'description' => 'Invalid transaction',
    ]);

    $response->assertRedirect()
        ->assertSessionHasErrors(['recipient_id']);
});

test('user can update existing transaction', function () {
    $user = User::factory()->create();
    $recipient = User::factory()->create();
    $newRecipient = User::factory()->create();
    $group = Group::factory()->createdBy($user)->create();
    $group->users()->attach([$recipient, $newRecipient]);

    $transaction = Transaction::factory()->forGroup($group)->between($user, $recipient)->create([
        'amount' => 100.00,
        'description' => 'Original payment',
    ]);

    $this->actingAs($user);

    $response = $this->put("/groups/{$group->id}/transactions/{$transaction->id}", [
        'recipient_id' => $newRecipient->id,
        'amount' => 150.00,
        'description' => 'Updated payment',
    ]);

    $response->assertRedirect("/groups/{$group->id}")
        ->assertSessionHas('success', 'Transaction updated successfully!');

    $transaction->refresh();
    expect($transaction->recipient_id)->toBe($newRecipient->id);
    expect($transaction->amount)->toBe(150.0);
    expect($transaction->description)->toBe('Updated payment');
});

test('user can delete transaction they created', function () {
    $user = User::factory()->create();
    $recipient = User::factory()->create();
    $group = Group::factory()->createdBy($user)->create();
    $group->users()->attach($recipient);

    $transaction = Transaction::factory()->forGroup($group)->between($user, $recipient)->create();

    $this->actingAs($user);

    $response = $this->delete("/groups/{$group->id}/transactions/{$transaction->id}");

    $response->assertRedirect()
        ->assertSessionHas('success', 'Transaction deleted successfully!');

    expect(Transaction::find($transaction->id))->toBeNull();
});

test('user cannot delete transaction they did not create', function () {
    $creator = User::factory()->create();
    $otherUser = User::factory()->create();
    $recipient = User::factory()->create();
    $group = Group::factory()->createdBy($creator)->create();
    $group->users()->attach([$otherUser, $recipient]);

    $transaction = Transaction::factory()->forGroup($group)->between($creator, $recipient)->create();

    $this->actingAs($otherUser);

    $response = $this->delete("/groups/{$group->id}/transactions/{$transaction->id}");

    $response->assertForbidden();
    expect(Transaction::find($transaction->id))->not->toBeNull();
});

test('non-group-member cannot access transaction pages', function () {
    $user = User::factory()->create();
    $outsideUser = User::factory()->create();
    $group = Group::factory()->createdBy($user)->withUsers(1)->create();

    $this->actingAs($outsideUser);

    $response = $this->get("/groups/{$group->id}/transactions/create");
    $response->assertForbidden();

    $response = $this->get("/groups/{$group->id}/transactions");
    $response->assertForbidden();
});

test('transaction can be created without description', function () {
    $user = User::factory()->create();
    $recipient = User::factory()->create();
    $group = Group::factory()->createdBy($user)->create();
    $group->users()->attach($recipient);

    $this->actingAs($user);

    $response = $this->post("/groups/{$group->id}/transactions", [
        'recipient_id' => $recipient->id,
        'amount' => 30.00,
    ]);

    $response->assertRedirect("/groups/{$group->id}")
        ->assertSessionHas('success', 'Transaction added successfully!');

    $transaction = Transaction::where('amount', 30.00)->first();
    expect($transaction->description)->toBeNull();
});

test('transaction index page shows only group transactions', function () {
    $user = User::factory()->create();
    $otherUser = User::factory()->create();
    $recipient = User::factory()->create();

    $userGroup = Group::factory()->createdBy($user)->create();
    $userGroup->users()->attach($recipient);
    $otherGroup = Group::factory()->createdBy($otherUser)->create();

    $userTransaction = Transaction::factory()->forGroup($userGroup)->between($user, $recipient)->create([
        'description' => 'User Transaction',
    ]);

    $otherTransaction = Transaction::factory()->forGroup($otherGroup)->create([
        'description' => 'Other Transaction',
    ]);

    $this->actingAs($user);

    $response = $this->get("/groups/{$userGroup->id}/transactions");

    $response->assertSee('User Transaction')
        ->assertDontSee('Other Transaction');
});

test('transaction creation stores payer information correctly', function () {
    $user = User::factory()->create();
    $recipient = User::factory()->create();
    $group = Group::factory()->createdBy($user)->create();
    $group->users()->attach($recipient);

    $this->actingAs($user);

    $this->post("/groups/{$group->id}/transactions", [
        'recipient_id' => $recipient->id,
        'amount' => 40.00,
        'description' => 'Payer test',
    ]);

    $transaction = Transaction::where('description', 'Payer test')->first();
    expect($transaction->payer_id)->toBe($user->id);
    expect($transaction->group_id)->toBe($group->id);
});

test('transaction with decimal amounts stores correctly', function () {
    $user = User::factory()->create();
    $recipient = User::factory()->create();
    $group = Group::factory()->createdBy($user)->create();
    $group->users()->attach($recipient);

    $this->actingAs($user);

    $response = $this->post("/groups/{$group->id}/transactions", [
        'recipient_id' => $recipient->id,
        'amount' => 123.45,
        'description' => 'Decimal amount',
    ]);

    $response->assertRedirect("/groups/{$group->id}");
    $transaction = Transaction::where('description', 'Decimal amount')->first();
    expect($transaction->amount)->toBe(123.45);
});

test('user cannot update transaction they did not create', function () {
    $creator = User::factory()->create();
    $otherUser = User::factory()->create();
    $recipient = User::factory()->create();
    $group = Group::factory()->createdBy($creator)->create();
    $group->users()->attach([$otherUser, $recipient]);

    $transaction = Transaction::factory()->forGroup($group)->between($creator, $recipient)->create([
        'amount' => 50.00,
        'description' => 'Original',
    ]);

    $this->actingAs($otherUser);

    $response = $this->put("/groups/{$group->id}/transactions/{$transaction->id}", [
        'recipient_id' => $recipient->id,
        'amount' => 100.00,
        'description' => 'Updated',
    ]);

    $response->assertForbidden();

    $transaction->refresh();
    expect($transaction->amount)->toBe(50.0);
    expect($transaction->description)->toBe('Original');
});

test('transaction validation requires minimum amount', function () {
    $user = User::factory()->create();
    $recipient = User::factory()->create();
    $group = Group::factory()->createdBy($user)->create();
    $group->users()->attach($recipient);

    $this->actingAs($user);

    $response = $this->post("/groups/{$group->id}/transactions", [
        'recipient_id' => $recipient->id,
        'amount' => 0.00,
        'description' => 'Zero amount',
    ]);

    $response->assertRedirect()
        ->assertSessionHasErrors(['amount']);
});

test('transaction relationships work correctly', function () {
    $payer = User::factory()->create(['name' => 'John Payer']);
    $recipient = User::factory()->create(['name' => 'Jane Recipient']);
    $group = Group::factory()->create(['name' => 'Test Group']);
    $group->users()->attach([$payer, $recipient]);

    $transaction = Transaction::factory()->forGroup($group)->between($payer, $recipient)->create([
        'amount' => 75.00,
        'description' => 'Test payment',
    ]);

    expect($transaction->payer->name)->toBe('John Payer');
    expect($transaction->recipient->name)->toBe('Jane Recipient');
    expect($transaction->group->name)->toBe('Test Group');
    expect($transaction->group->id)->toBe($group->id);
});
