<?php

namespace Tests\Feature;

use App\Models\Group;
use App\Models\SharedDebt;
use App\Models\User;

test('user cannot create shared debt with members not in group', function () {
    $user = User::factory()->create();
    $outsideUser = User::factory()->create(['name' => 'Outside User']);
    $group = Group::factory()->createdBy($user)->withUsers(1)->create();

    $this->actingAs($user);

    $response = $this->post("/groups/{$group->id}/sharedDebts", [
        'name' => 'Invalid Debt',
        'amount' => 50.00,
        'members' => [$user->id, $outsideUser->id],
    ]);

    $response->assertRedirect()
        ->assertSessionHasErrors(['members']);
});

test('non-group-member cannot access shared debt pages', function () {
    $user = User::factory()->create();
    $outsideUser = User::factory()->create();
    $group = Group::factory()->createdBy($user)->withUsers(1)->create();

    $this->actingAs($outsideUser);

    $response = $this->get("/groups/{$group->id}/sharedDebts/create");
    $response->assertStatus(403);
});

test('user can successfully create shared debt with valid data', function () {
    $user = User::factory()->create();
    $member = User::factory()->create();
    $group = Group::factory()->createdBy($user)->create();
    $group->users()->attach($member);

    $this->actingAs($user);

    $response = $this->post("/groups/{$group->id}/sharedDebts", [
        'name' => 'Restaurant Bill',
        'amount' => 60.00,
        'members' => [$user->id, $member->id],
    ]);

    $response->assertRedirect("/groups/{$group->id}")
        ->assertSessionHas('success', 'Shared debt added successfully!');

    expect(SharedDebt::where('name', 'Restaurant Bill')->exists())->toBeTrue();
    expect(SharedDebt::where('amount', 60.00)->first()->users()->count())->toBe(2);
});

test('shared debt validation requires name field', function () {
    $user = User::factory()->create();
    $group = Group::factory()->createdBy($user)->withUsers(1)->create();

    $this->actingAs($user);

    $response = $this->post("/groups/{$group->id}/sharedDebts", [
        'amount' => 50.00,
        'members' => [$user->id],
    ]);

    $response->assertRedirect()
        ->assertSessionHasErrors(['name']);
});

test('shared debt validation requires positive amount', function () {
    $user = User::factory()->create();
    $group = Group::factory()->createdBy($user)->withUsers(1)->create();

    $this->actingAs($user);

    $response = $this->post("/groups/{$group->id}/sharedDebts", [
        'name' => 'Test Debt',
        'amount' => -10.00,
        'members' => [$user->id],
    ]);

    $response->assertRedirect()
        ->assertSessionHasErrors(['amount']);
});

test('shared debt validation requires at least one member', function () {
    $user = User::factory()->create();
    $group = Group::factory()->createdBy($user)->withUsers(1)->create();

    $this->actingAs($user);

    $response = $this->post("/groups/{$group->id}/sharedDebts", [
        'name' => 'Test Debt',
        'amount' => 50.00,
        'members' => [],
    ]);

    $response->assertRedirect()
        ->assertSessionHasErrors(['members']);
});

test('user can update existing shared debt', function () {
    $user = User::factory()->create();
    $group = Group::factory()->createdBy($user)->withUsers(1)->create();
    $sharedDebt = SharedDebt::factory()->forGroup($group)->createdBy($user)->create([
        'name' => 'Original Name',
        'amount' => 100.00,
    ]);
    $sharedDebt->users()->attach($user);

    $this->actingAs($user);

    $response = $this->put("/groups/{$group->id}/sharedDebts/{$sharedDebt->id}", [
        'name' => 'Updated Name',
        'amount' => 150.00,
        'members' => [$user->id],
    ]);

    $response->assertRedirect("/groups/{$group->id}")
        ->assertSessionHas('success', 'Shared debt updated successfully!');

    $sharedDebt->refresh();
    expect($sharedDebt->name)->toBe('Updated Name');
    expect($sharedDebt->amount)->toBe(150.00);
});

test('user can delete shared debt they created', function () {
    $user = User::factory()->create();
    $group = Group::factory()->createdBy($user)->withUsers(1)->create();
    $sharedDebt = SharedDebt::factory()->forGroup($group)->createdBy($user)->create();
    $sharedDebt->users()->attach($user);

    $this->actingAs($user);

    $response = $this->delete("/groups/{$group->id}/sharedDebts/{$sharedDebt->id}");

    $response->assertRedirect()
        ->assertSessionHas('success', 'Shared debt deleted successfully!');

    expect(SharedDebt::find($sharedDebt->id))->toBeNull();
});

test('user cannot delete shared debt they did not create', function () {
    $creator = User::factory()->create();
    $otherUser = User::factory()->create();
    $group = Group::factory()->createdBy($creator)->create();
    $group->users()->attach($otherUser);

    $sharedDebt = SharedDebt::factory()->forGroup($group)->createdBy($creator)->create();
    $sharedDebt->users()->attach([$creator, $otherUser]);

    $this->actingAs($otherUser);

    $response = $this->delete("/groups/{$group->id}/sharedDebts/{$sharedDebt->id}");

    $response->assertForbidden();
    expect(SharedDebt::find($sharedDebt->id))->not->toBeNull();
});

test('shared debt index page shows only group debts', function () {
    $user = User::factory()->create();
    $otherUser = User::factory()->create();

    $userGroup = Group::factory()->createdBy($user)->create();
    $otherGroup = Group::factory()->createdBy($otherUser)->create();

    $userDebt = SharedDebt::factory()->forGroup($userGroup)->createdBy($user)->create(['name' => 'User Debt']);
    $userDebt->users()->attach($user);

    $otherDebt = SharedDebt::factory()->forGroup($otherGroup)->createdBy($otherUser)->create(['name' => 'Other Debt']);
    $otherDebt->users()->attach($otherUser);

    $this->actingAs($user);

    $response = $this->get("/groups/{$userGroup->id}/sharedDebts");

    $response->assertSee('User Debt')
        ->assertDontSee('Other Debt');
});

test('shared debt creation stores creator information', function () {
    $user = User::factory()->create();
    $member = User::factory()->create();
    $group = Group::factory()->createdBy($user)->create();
    $group->users()->attach($member);

    $this->actingAs($user);

    $this->post("/groups/{$group->id}/sharedDebts", [
        'name' => 'Creator Test',
        'amount' => 80.00,
        'members' => [$user->id, $member->id],
    ]);

    $sharedDebt = SharedDebt::where('name', 'Creator Test')->first();
    expect($sharedDebt->created_by)->toBe($user->id);
});

test('shared debt can be created with decimal amounts', function () {
    $user = User::factory()->create();
    $group = Group::factory()->createdBy($user)->withUsers(1)->create();

    $this->actingAs($user);

    $response = $this->post("/groups/{$group->id}/sharedDebts", [
        'name' => 'Decimal Amount',
        'amount' => 15.73,
        'members' => [$user->id],
    ]);

    $response->assertRedirect("/groups/{$group->id}");
    $sharedDebt = SharedDebt::where('name', 'Decimal Amount')->first();
    expect($sharedDebt->amount)->toBe(15.73);
});
