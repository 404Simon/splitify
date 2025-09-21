<?php

namespace Tests\Browser;

use App\Models\Group;
use App\Models\Transaction;
use App\Models\User;

test('user can view transactions list in group', function () {
    $user = User::factory()->create(['name' => 'John Doe']);
    $recipient = User::factory()->create(['name' => 'Jane Smith']);
    $group = Group::factory()->createdBy($user)->create(['name' => 'Test Group']);
    $group->users()->attach($recipient);

    $transaction = Transaction::factory()->forGroup($group)->between($user, $recipient)->create([
        'amount' => 50.25,
        'description' => 'PayPal payment',
    ]);

    $this->actingAs($user);

    visit("/groups/{$group->id}")
        ->assertNoSmoke()
        ->assertSee('Test Group')
        ->assertSee('Recent Transactions')
        ->assertSee('John Doe')
        ->assertSee('Jane Smith')
        ->assertSee('€50.25')
        ->assertSee('PayPal payment');
});

test('user can create a new transaction', function () {
    $user = User::factory()->create(['name' => 'Alice Cooper']);
    $recipient = User::factory()->create(['name' => 'Bob Wilson']);
    $group = Group::factory()->createdBy($user)->create(['name' => 'Test Group']);
    $group->users()->attach($recipient);

    $this->actingAs($user);

    visit("/groups/{$group->id}")
        ->assertNoSmoke()
        ->assertSee('Test Group')
        ->click('Add Transaction')
        ->assertPathIs("/groups/{$group->id}/transactions/create")
        ->assertNoSmoke()
        ->assertSee('Add Transaction to Test Group')
        ->assertSee('Recipient')
        ->assertSee('Amount (€)')
        ->assertSee('Description')
        ->select('recipient_id', $recipient->id)
        ->fill('amount', '75.50')
        ->fill('description', 'Cash payment')
        ->click('Add Transaction')
        ->assertPathIs("/groups/{$group->id}")
        ->assertNoSmoke()
        ->assertSee('Transaction added successfully!')
        ->assertSee('Alice Cooper')
        ->assertSee('Bob Wilson')
        ->assertSee('€75.50')
        ->assertSee('Cash payment');
});

test('user can edit an existing transaction', function () {
    $user = User::factory()->create(['name' => 'Charlie Brown']);
    $recipient = User::factory()->create(['name' => 'David Lee']);
    $group = Group::factory()->createdBy($user)->create(['name' => 'Test Group']);
    $group->users()->attach($recipient);

    $transaction = Transaction::factory()->forGroup($group)->between($user, $recipient)->create([
        'amount' => 30.00,
        'description' => 'Original payment',
    ]);

    $this->actingAs($user);

    visit("/groups/{$group->id}")
        ->assertNoSmoke()
        ->assertSee('Original payment')
        ->assertSee('€30.00')
        ->click('Edit')
        ->assertPathIs("/groups/{$group->id}/transactions/{$transaction->id}/edit")
        ->assertNoSmoke()
        ->assertSee('Edit Transaction for Test Group')
        ->assertValue('amount', '30')
        ->assertValue('description', 'Original payment')
        ->clear('amount')
        ->fill('amount', '45.75')
        ->clear('description')
        ->fill('description', 'Updated payment')
        ->click('Update Transaction')
        ->assertPathIs("/groups/{$group->id}")
        ->assertNoSmoke()
        ->assertSee('Transaction updated successfully!')
        ->assertSee('€45.75')
        ->assertSee('Updated payment')
        ->assertDontSee('€30.00')
        ->assertDontSee('Original payment');
});

test('user can delete a transaction', function () {
    $user = User::factory()->create(['name' => 'Emma Watson']);
    $recipient = User::factory()->create(['name' => 'Frank Miller']);
    $group = Group::factory()->createdBy($user)->create(['name' => 'Test Group']);
    $group->users()->attach($recipient);

    $transaction = Transaction::factory()->forGroup($group)->between($user, $recipient)->create([
        'amount' => 20.00,
        'description' => 'Transaction to delete',
    ]);

    $this->actingAs($user);

    visit("/groups/{$group->id}")
        ->assertNoSmoke()
        ->assertSee('Transaction to delete')
        ->assertSee('€20.00')
        ->click('Delete') // Opens modal
        ->click('Confirm Delete') // Click confirm in modal
        ->assertPathIs("/groups/{$group->id}")
        ->assertNoSmoke()
        ->assertSee('Transaction deleted successfully!')
        ->assertDontSee('Transaction to delete');
});

test('transaction form validation prevents empty submission', function () {
    $user = User::factory()->create();
    $recipient = User::factory()->create();
    $group = Group::factory()->createdBy($user)->create();
    $group->users()->attach($recipient);

    $this->actingAs($user);

    visit("/groups/{$group->id}/transactions/create")
        ->assertNoSmoke()
        ->assertSee('Add Transaction to')
        ->click('Add Transaction')
        ->assertPathIs("/groups/{$group->id}/transactions/create")
        ->assertNoSmoke();
});

test('transaction form validation requires valid amount', function () {
    $user = User::factory()->create();
    $recipient = User::factory()->create();
    $group = Group::factory()->createdBy($user)->create();
    $group->users()->attach($recipient);

    $this->actingAs($user);

    visit("/groups/{$group->id}/transactions/create")
        ->assertNoSmoke()
        ->select('recipient_id', $recipient->id)
        ->fill('amount', '-10')
        ->fill('description', 'Test transaction')
        ->click('Add Transaction')
        ->assertPathIs("/groups/{$group->id}/transactions/create")
        ->assertNoSmoke();
});

test('transaction form validation requires recipient selection', function () {
    $user = User::factory()->create();
    $recipient1 = User::factory()->create(['name' => 'First Recipient']);
    $recipient2 = User::factory()->create(['name' => 'Second Recipient']);
    $group = Group::factory()->createdBy($user)->create();
    $group->users()->attach([$recipient1->id, $recipient2->id]); // Add multiple recipients to prevent auto-preselection

    $this->actingAs($user);

    visit("/groups/{$group->id}/transactions/create")
        ->assertNoSmoke()
        ->fill('amount', '25.00')
        ->fill('description', 'Test transaction')
        ->click('Add Transaction')
        ->wait(1)
        ->assertPathIs("/groups/{$group->id}/transactions/create")
        ->assertNoSmoke()
        ->assertSee('The recipient id field is required.'); // Check for validation error
});

test('user can view transaction details with payer and recipient info', function () {
    $user = User::factory()->create(['name' => 'Grace Hopper']);
    $recipient = User::factory()->create(['name' => 'Alan Turing']);
    $group = Group::factory()->createdBy($user)->create(['name' => 'Test Group']);
    $group->users()->attach($recipient);

    $transaction = Transaction::factory()->forGroup($group)->between($user, $recipient)->create([
        'amount' => 150.00,
        'description' => 'Computer equipment',
    ]);

    $this->actingAs($user);

    visit("/groups/{$group->id}")
        ->assertNoSmoke()
        ->assertSee('Computer equipment')
        ->assertSee('€150.00')
        ->assertSee('Grace Hopper')
        ->assertSee('Alan Turing');
});

test('user can cancel transaction creation', function () {
    $user = User::factory()->create();
    $recipient = User::factory()->create();
    $group = Group::factory()->createdBy($user)->create();
    $group->users()->attach($recipient);

    $this->actingAs($user);

    visit("/groups/{$group->id}/transactions/create")
        ->assertNoSmoke()
        ->assertSee('Add Transaction to')
        ->select('recipient_id', $recipient->id)
        ->fill('amount', '25.00')
        ->fill('description', 'Test transaction')
        ->navigate("/groups/{$group->id}")
        ->assertPathIs("/groups/{$group->id}")
        ->assertNoSmoke();
});

test('user can cancel transaction editing', function () {
    $user = User::factory()->create();
    $recipient = User::factory()->create();
    $group = Group::factory()->createdBy($user)->create();
    $group->users()->attach($recipient);

    $transaction = Transaction::factory()->forGroup($group)->between($user, $recipient)->create([
        'amount' => 100.00,
        'description' => 'Original description',
    ]);

    $this->actingAs($user);

    visit("/groups/{$group->id}/transactions/{$transaction->id}/edit")
        ->assertNoSmoke()
        ->assertSee('Edit Transaction for')
        ->clear('description')
        ->fill('description', 'Changed description')
        ->navigate("/groups/{$group->id}")
        ->assertPathIs("/groups/{$group->id}")
        ->assertNoSmoke()
        ->assertSee('Original description')
        ->assertDontSee('Changed description');
});

test('user can access transactions index page', function () {
    $user = User::factory()->create();
    $recipient = User::factory()->create();
    $group = Group::factory()->createdBy($user)->create(['name' => 'Test Group']);
    $group->users()->attach($recipient);

    $transaction = Transaction::factory()->forGroup($group)->between($user, $recipient)->create([
        'amount' => 50.00,
        'description' => 'Test transaction',
    ]);

    $this->actingAs($user);

    visit("/groups/{$group->id}/transactions")
        ->assertNoSmoke()
        ->assertSee('Test transaction')
        ->assertSee('€50.00');
});

test('transaction displays correct amount formatting', function () {
    $user = User::factory()->create();
    $recipient = User::factory()->create();
    $group = Group::factory()->createdBy($user)->create();
    $group->users()->attach($recipient);

    $transaction = Transaction::factory()->forGroup($group)->between($user, $recipient)->create([
        'amount' => 123.45,
        'description' => 'Precise amount',
    ]);

    $this->actingAs($user);

    visit("/groups/{$group->id}")
        ->assertNoSmoke()
        ->assertSee('Precise amount')
        ->assertSee('€123.45');
});

test('user can create transaction with empty description', function () {
    $user = User::factory()->create(['name' => 'Helen Keller']);
    $recipient = User::factory()->create(['name' => 'Marie Curie']);
    $group = Group::factory()->createdBy($user)->create(['name' => 'Test Group']);
    $group->users()->attach($recipient);

    $this->actingAs($user);

    visit("/groups/{$group->id}/transactions/create")
        ->assertNoSmoke()
        ->select('recipient_id', $recipient->id)
        ->fill('amount', '35.00')
        ->click('Add Transaction')
        ->assertPathIs("/groups/{$group->id}")
        ->assertNoSmoke()
        ->assertSee('Transaction added successfully!')
        ->assertSee('€35.00')
        ->assertSee('Helen Keller')
        ->assertSee('Marie Curie');
});

test('multiple transactions display correctly in chronological order', function () {
    $user = User::factory()->create();
    $recipient = User::factory()->create();
    $group = Group::factory()->createdBy($user)->create();
    $group->users()->attach($recipient);

    // Create multiple transactions
    $transaction0 = Transaction::factory()->forGroup($group)->between($user, $recipient)->create([
        'amount' => 25.00,
        'description' => 'invisible transaction',
        'created_at' => now()->subHours(3),
    ]);

    $transaction1 = Transaction::factory()->forGroup($group)->between($user, $recipient)->create([
        'amount' => 25.00,
        'description' => 'First transaction',
        'created_at' => now()->subHours(2),
    ]);

    $transaction2 = Transaction::factory()->forGroup($group)->between($recipient, $user)->create([
        'amount' => 50.00,
        'description' => 'Second transaction',
        'created_at' => now()->subHour(),
    ]);

    $transaction3 = Transaction::factory()->forGroup($group)->between($user, $recipient)->create([
        'amount' => 75.00,
        'description' => 'Third transaction',
        'created_at' => now(),
    ]);

    $this->actingAs($user);

    visit("/groups/{$group->id}")
        ->assertNoSmoke()
        ->assertDontSee('invisible transaction') // group overview should only display newest 3 transactions
        ->assertSee('First transaction')
        ->assertSee('€25.00')
        ->assertSee('Second transaction')
        ->assertSee('€50.00')
        ->assertSee('Third transaction')
        ->assertSee('€75.00');
});
