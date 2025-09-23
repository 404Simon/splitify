<?php

declare(strict_types=1);

namespace Tests\Browser;

use App\Models\Group;
use App\Models\SharedDebt;
use App\Models\User;

test('user can view shared debts list in group', function () {
    $user = User::factory()->create();
    $group = Group::factory()->createdBy($user)->withUsers(2)->create(['name' => 'Test Group']);
    $sharedDebt = SharedDebt::factory()->forGroup($group)->createdBy($user)->splitBetweenAllMembers()->create([
        'name' => 'Groceries',
        'amount' => 100.50,
    ]);

    $this->actingAs($user);

    visit("/groups/{$group->id}")
        ->assertNoSmoke()
        ->assertSee('Test Group')
        ->assertSee('Recent Shared Debts')
        ->assertSee('Groceries')
        ->assertSee('€100.50');
});

test('user can create a new shared debt', function () {
    $user = User::factory()->create(['name' => 'John Doe']);
    $member1 = User::factory()->create(['name' => 'Jane Smith']);
    $member2 = User::factory()->create(['name' => 'Bob Wilson']);
    $group = Group::factory()->createdBy($user)->create(['name' => 'Test Group']);
    $group->users()->attach([$member1, $member2]);

    $this->actingAs($user);

    visit("/groups/{$group->id}")
        ->assertNoSmoke()
        ->assertSee('Test Group')
        ->click('Add Debt')
        ->assertPathIs("/groups/{$group->id}/sharedDebts/create")
        ->assertNoSmoke()
        ->assertSee('Add Debt to Test Group')
        ->assertSee('Name')
        ->assertSee('Amount (€)')
        ->assertSee('Split Between')
        ->fill('name', 'Restaurant Bill')
        ->fill('amount', '80.50')
        ->uncheck("user-{$member2->id}")
        ->click('Add Debt')
        ->assertPathIs("/groups/{$group->id}")
        ->assertNoSmoke()
        ->assertSee('Shared debt added successfully!')
        ->assertSee('Restaurant Bill')
        ->assertSee('€80.50')
        ->assertSee('€40.25'); // split between $user and $member1
});

test('user can edit an existing shared debt', function () {
    $user = User::factory()->create();
    $member1 = User::factory()->create(['name' => 'Alice Cooper']);
    $group = Group::factory()->createdBy($user)->create(['name' => 'Test Group']);
    $group->users()->attach($member1);

    $sharedDebt = SharedDebt::factory()->forGroup($group)->createdBy($user)->withUsers([$user, $member1])->create([
        'name' => 'Original Expense',
        'amount' => 50.00,
    ]);

    $this->actingAs($user);

    visit("/groups/{$group->id}")
        ->assertNoSmoke()
        ->assertSee('Original Expense')
        ->assertSee('€50.00')
        ->click('Edit')
        ->assertPathIs("/groups/{$group->id}/sharedDebts/{$sharedDebt->id}/edit")
        ->assertNoSmoke()
        ->assertSee('Edit Debt for Test Group')
        ->assertValue('name', 'Original Expense')
        ->assertValue('amount', '50')
        ->clear('name')
        ->fill('name', 'Updated Expense')
        ->clear('amount')
        ->fill('amount', '75.25')
        ->click('Update Debt')
        ->assertPathIs("/groups/{$group->id}")
        ->assertNoSmoke()
        ->assertSee('Shared debt updated successfully!')
        ->assertSee('Updated Expense')
        ->assertSee('€75.25')
        ->assertDontSee('Original Expense')
        ->assertDontSee('€50.00');
});

test('user can delete a shared debt', function () {
    $user = User::factory()->create();
    $group = Group::factory()->createdBy($user)->withUsers(1)->create(['name' => 'Test Group']);

    $sharedDebt = SharedDebt::factory()->forGroup($group)->createdBy($user)->splitBetweenAllMembers()->create([
        'name' => 'Debt to Delete',
        'amount' => 25.00,
    ]);

    $this->actingAs($user);

    visit("/groups/{$group->id}")
        ->assertNoSmoke()
        ->assertSee('Debt to Delete')
        ->assertSee('€25.00')
        ->click('Delete') // Opens modal
        ->click('Confirm Delete') // Click confirm in modal
        ->assertPathIs("/groups/{$group->id}")
        ->assertNoSmoke()
        ->assertSee('Shared debt deleted successfully!')
        ->assertDontSee('Debt to Delete')
        ->assertDontSee('€25.00');
});

test('shared debt form validation prevents empty submission', function () {
    $user = User::factory()->create();
    $group = Group::factory()->createdBy($user)->withUsers(1)->create();

    $this->actingAs($user);

    visit("/groups/{$group->id}/sharedDebts/create")
        ->assertNoSmoke()
        ->assertSee('Add Debt to')
        ->click('Add Debt')
        ->assertPathIs("/groups/{$group->id}/sharedDebts/create")
        ->assertNoSmoke();
});

test('shared debt form validation requires valid amount', function () {
    $user = User::factory()->create();
    $group = Group::factory()->createdBy($user)->withUsers(1)->create();

    $this->actingAs($user);

    visit("/groups/{$group->id}/sharedDebts/create")
        ->assertNoSmoke()
        ->fill('name', 'Test Debt')
        ->fill('amount', '-10')
        ->click('Add Debt')
        ->assertPathIs("/groups/{$group->id}/sharedDebts/create")
        ->assertNoSmoke();
});

test('shared debt form validation requires at least one member', function () {
    $user = User::factory()->create();
    $member1 = User::factory()->create();
    $group = Group::factory()->createdBy($user)->create();
    $group->users()->attach($member1);

    $this->actingAs($user);

    visit("/groups/{$group->id}/sharedDebts/create")
        ->assertNoSmoke()
        ->fill('name', 'Test Debt')
        ->fill('amount', '50.00')
        ->uncheck("user-{$user->id}")
        ->uncheck("user-{$member1->id}")
        ->click('Add Debt')
        ->assertPathIs("/groups/{$group->id}/sharedDebts/create")
        ->assertNoSmoke();
});

test('user can view shared debt details and member breakdown', function () {
    $user = User::factory()->create(['name' => 'John Doe']);
    $member1 = User::factory()->create(['name' => 'Jane Smith']);
    $member2 = User::factory()->create(['name' => 'Bob Wilson']);
    $group = Group::factory()->createdBy($user)->create(['name' => 'Test Group']);
    $group->users()->attach([$member1, $member2]);

    $sharedDebt = SharedDebt::factory()->forGroup($group)->createdBy($user)->withUsers([$user, $member1, $member2])->create([
        'name' => 'Pizza Night',
        'amount' => 90.00,
    ]);

    $this->actingAs($user);

    visit("/groups/{$group->id}")
        ->assertNoSmoke()
        ->assertSee('Pizza Night')
        ->assertSee('€90.00')
        ->assertSee('John Doe')
        ->assertSee('Jane Smith')
        ->assertSee('Bob Wilson')
        ->assertSee('€30.00'); // 90/3 = 30 per person
});

test('user can cancel shared debt creation', function () {
    $user = User::factory()->create();
    $group = Group::factory()->createdBy($user)->withUsers(1)->create();

    $this->actingAs($user);

    visit("/groups/{$group->id}/sharedDebts/create")
        ->assertNoSmoke()
        ->assertSee('Add Debt to')
        ->fill('name', 'Test Debt')
        ->fill('amount', '25.00')
        ->navigate("/groups/{$group->id}")
        ->assertPathIs("/groups/{$group->id}")
        ->assertNoSmoke();
});

test('user can cancel shared debt editing', function () {
    $user = User::factory()->create();
    $group = Group::factory()->createdBy($user)->withUsers(1)->create();

    $sharedDebt = SharedDebt::factory()->forGroup($group)->createdBy($user)->create([
        'name' => 'Original Name',
        'amount' => 100.00,
    ]);
    $sharedDebt->users()->attach($user);

    $this->actingAs($user);

    visit("/groups/{$group->id}/sharedDebts/{$sharedDebt->id}/edit")
        ->assertNoSmoke()
        ->assertSee('Edit Debt for')
        ->clear('name')
        ->fill('name', 'Changed Name')
        ->navigate("/groups/{$group->id}")
        ->assertPathIs("/groups/{$group->id}")
        ->assertNoSmoke()
        ->assertSee('Original Name')
        ->assertDontSee('Changed Name');
});

test('shared debt displays correct member count and total amount', function () {
    $user = User::factory()->create();
    $members = User::factory()->count(4)->create();
    $group = Group::factory()->createdBy($user)->create();
    $group->users()->attach($members->pluck('id'));

    $sharedDebt = SharedDebt::factory()->forGroup($group)->createdBy($user)->splitBetweenAllMembers()->create([
        'name' => 'Large Group Expense',
        'amount' => 120.00,
    ]);

    $this->actingAs($user);

    visit("/groups/{$group->id}")
        ->assertNoSmoke()
        ->assertSee('Large Group Expense')
        ->assertSee('€120.00')
        ->assertSee('5 members')
        ->assertSee('€24.00'); // 120/5 = 24 per person
});

test('user can access shared debts index page', function () {
    $user = User::factory()->create();
    $group = Group::factory()->createdBy($user)->withUsers(2)->create(['name' => 'Test Group']);
    $sharedDebt = SharedDebt::factory()->forGroup($group)->createdBy($user)->splitBetweenAllMembers()->create([
        'name' => 'Test Debt',
        'amount' => 50.00,
    ]);

    $this->actingAs($user);

    visit("/groups/{$group->id}/sharedDebts")
        ->assertNoSmoke()
        ->assertSee('Test Debt')
        ->assertSee('€50.00');
});
