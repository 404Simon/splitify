<?php

declare(strict_types=1);

namespace Tests\Browser;

use App\Models\Group;
use App\Models\RecurringSharedDebt;
use App\Models\SharedDebt;
use App\Models\User;

test('user can view recurring shared debts list in group', function () {
    $user = User::factory()->create();
    $group = Group::factory()->createdBy($user)->withUsers(2)->create(['name' => 'Test Group']);
    $recurringDebt = RecurringSharedDebt::factory()->forGroup($group)->createdBy($user)->monthly()->create([
        'name' => 'Monthly Rent',
        'amount' => 1200.00,
    ]);
    $recurringDebt->users()->attach([$user->id]);

    $this->actingAs($user);

    visit("/groups/{$group->id}")
        ->assertNoSmoke()
        ->assertSee('Test Group')
        ->assertSee('Active Recurring Debts')
        ->assertSee('Monthly Rent')
        ->assertSee('€1,200.00')
        ->assertSee('Monthly')
        ->click('View All')
        ->assertPathIs("/groups/{$group->id}/recurring-debts")
        ->assertSee('Monthly Rent');
});

test('user can create a new recurring shared debt', function () {
    $user = User::factory()->create(['name' => 'John Doe']);
    $member1 = User::factory()->create(['name' => 'Jane Smith']);
    $member2 = User::factory()->create(['name' => 'Bob Wilson']);
    $group = Group::factory()->createdBy($user)->create(['name' => 'Test Group']);
    $group->users()->attach([$member1->id, $member2->id]);

    $this->actingAs($user);

    visit("/groups/{$group->id}/recurring-debts")
        ->assertNoSmoke()
        ->click('Create Your First Recurring Debt')
        ->assertPathIs("/groups/{$group->id}/recurring-debts/create")
        ->assertNoSmoke()
        ->assertSee('Create Recurring Debt for Test Group')
        ->assertSee('Name')
        ->assertSee('Amount (€)')
        ->assertSee('Frequency')
        ->assertSee('Start Date')
        ->assertSee('Split Between')
        ->fill('name', 'Internet Bill')
        ->fill('amount', '45.50')
        ->select('frequency', 'monthly')
        ->fill('start_date', now()->addDay()->format('Y-m-d'))
        ->uncheck("user-{$member2->id}")
        ->click('Create Recurring Debt')
        ->assertPathIs("/groups/{$group->id}/recurring-debts")
        ->assertNoSmoke()
        ->assertSee('Recurring shared debt created successfully!')
        ->assertSee('Internet Bill')
        ->assertSee('€45.50')
        ->assertSee('Monthly');
});

test('user can edit an existing recurring shared debt', function () {
    $user = User::factory()->create();
    $member1 = User::factory()->create(['name' => 'Alice Cooper']);
    $group = Group::factory()->createdBy($user)->create(['name' => 'Test Group']);
    $group->users()->attach($member1);

    $recurringDebt = RecurringSharedDebt::factory()->forGroup($group)->createdBy($user)->monthly()->create([
        'name' => 'Original Bill',
        'amount' => 50.00,
    ]);
    $recurringDebt->users()->attach([$user->id, $member1->id]);

    $this->actingAs($user);

    visit("/groups/{$group->id}/recurring-debts")
        ->assertNoSmoke()
        ->assertSee('Original Bill')
        ->assertSee('€50.00')
        ->click('Edit')
        ->assertPathIs("/groups/{$group->id}/recurring-debts/{$recurringDebt->id}/edit")
        ->assertNoSmoke()
        ->assertSee('Edit Recurring Debt: Original Bill')
        ->assertValue('name', 'Original Bill')
        ->assertValue('amount', '50.00')
        ->clear('name')
        ->fill('name', 'Updated Bill')
        ->clear('amount')
        ->fill('amount', '75.25')
        ->select('frequency', 'weekly')
        ->click('Update Recurring Debt')
        ->assertPathIs("/groups/{$group->id}/recurring-debts/{$recurringDebt->id}")
        ->assertNoSmoke()
        ->assertSee('Recurring shared debt updated successfully!')
        ->assertSee('Updated Bill')
        ->assertSee('€75.25')
        ->assertSee('Weekly')
        ->assertDontSee('Original Bill')
        ->assertDontSee('€50.00');
});

test('user can delete a recurring shared debt', function () {
    $user = User::factory()->create();
    $group = Group::factory()->createdBy($user)->withUsers(1)->create(['name' => 'Test Group']);

    $recurringDebt = RecurringSharedDebt::factory()->forGroup($group)->createdBy($user)->create([
        'name' => 'Debt to Delete',
        'amount' => 25.00,
    ]);
    $recurringDebt->users()->attach($user->id);

    $this->actingAs($user);

    visit("/groups/{$group->id}/recurring-debts/{$recurringDebt->id}")
        ->assertNoSmoke()
        ->assertSee('Debt to Delete')
        ->assertSee('€25.00')
        ->click('Delete Recurring Debt')
        ->click('Delete')
        ->assertPathIs("/groups/{$group->id}/recurring-debts")
        ->assertNoSmoke()
        ->assertSee('Recurring shared debt deleted successfully!')
        ->assertDontSee('Debt to Delete')
        ->assertDontSee('€25.00');
});

test('user can toggle recurring debt active status', function () {
    $user = User::factory()->create();
    $group = Group::factory()->createdBy($user)->withUsers(1)->create(['name' => 'Test Group']);

    $recurringDebt = RecurringSharedDebt::factory()->forGroup($group)->createdBy($user)->active()->create([
        'name' => 'Toggle Test',
        'amount' => 100.00,
    ]);
    $recurringDebt->users()->attach($user->id);

    $this->actingAs($user);

    visit("/groups/{$group->id}/recurring-debts")
        ->assertNoSmoke()
        ->assertSee('Toggle Test')
        ->assertSee('Active')
        ->click('Pause')
        ->assertPathIs("/groups/{$group->id}/recurring-debts")
        ->assertNoSmoke()
        ->assertSee('Recurring shared debt deactivated successfully!')
        ->assertSee('Inactive')
        ->assertSee('Resume')
        ->click('Resume')
        ->assertNoSmoke()
        ->assertSee('Recurring shared debt activated successfully!')
        ->assertSee('Active')
        ->assertSee('Pause');
});

test('user can manually generate debt from recurring debt', function () {
    $user = User::factory()->create();
    $member1 = User::factory()->create(['name' => 'Alice Cooper']);
    $group = Group::factory()->createdBy($user)->create(['name' => 'Test Group']);
    $group->users()->attach($member1);

    $recurringDebt = RecurringSharedDebt::factory()->forGroup($group)->createdBy($user)->active()->create([
        'name' => 'Manual Generation Test',
        'amount' => 120.00,
    ]);
    $recurringDebt->users()->attach([$user->id, $member1->id]);

    $this->actingAs($user);

    visit("/groups/{$group->id}/recurring-debts")
        ->assertNoSmoke()
        ->assertSee('Manual Generation Test')
        ->assertSee('€120.00')
        ->click('Generate')
        ->assertPathIs("/groups/{$group->id}")
        ->assertNoSmoke()
        ->assertSee('Shared debt generated successfully!')
        ->assertSee('Manual Generation Test');

    // Verify the shared debt was created
    $this->assertDatabaseHas('shared_debts', [
        'name' => 'Manual Generation Test',
        'amount' => 120.00,
        'recurring_shared_debt_id' => $recurringDebt->id,
    ]);
});

test('recurring debt form validation prevents empty submission', function () {
    $user = User::factory()->create();
    $group = Group::factory()->createdBy($user)->withUsers(1)->create();

    $this->actingAs($user);

    visit("/groups/{$group->id}/recurring-debts/create")
        ->assertNoSmoke()
        ->assertSee('Create Recurring Debt for')
        ->click('Create Recurring Debt')
        ->assertPathIs("/groups/{$group->id}/recurring-debts/create")
        ->assertNoSmoke();
});

test('recurring debt form validation requires valid amount', function () {
    $user = User::factory()->create();
    $group = Group::factory()->createdBy($user)->withUsers(1)->create();

    $this->actingAs($user);

    visit("/groups/{$group->id}/recurring-debts/create")
        ->assertNoSmoke()
        ->fill('name', 'Test Debt')
        ->fill('amount', '-10')
        ->select('frequency', 'monthly')
        ->fill('start_date', now()->format('Y-m-d'))
        ->click('Create Recurring Debt')
        ->assertPathIs("/groups/{$group->id}/recurring-debts/create")
        ->assertNoSmoke();
});

test('recurring debt form validation requires at least one member', function () {
    $user = User::factory()->create();
    $member1 = User::factory()->create();
    $group = Group::factory()->createdBy($user)->create();
    $group->users()->attach($member1);

    $this->actingAs($user);

    visit("/groups/{$group->id}/recurring-debts/create")
        ->assertNoSmoke()
        ->fill('name', 'Test Debt')
        ->fill('amount', '50.00')
        ->select('frequency', 'monthly')
        ->fill('start_date', now()->format('Y-m-d'))
        ->uncheck("user-{$user->id}")
        ->uncheck("user-{$member1->id}")
        ->click('Create Recurring Debt')
        ->assertPathIs("/groups/{$group->id}/recurring-debts/create")
        ->assertNoSmoke();
});

test('recurring debt form validation requires future start date', function () {
    $user = User::factory()->create();
    $group = Group::factory()->createdBy($user)->withUsers(1)->create();

    $this->actingAs($user);

    visit("/groups/{$group->id}/recurring-debts/create")
        ->assertNoSmoke()
        ->fill('name', 'Test Debt')
        ->fill('amount', '50.00')
        ->select('frequency', 'monthly')
        ->fill('start_date', now()->subDay()->format('Y-m-d'))
        ->click('Create Recurring Debt')
        ->assertPathIs("/groups/{$group->id}/recurring-debts/create")
        ->assertNoSmoke();
});

test('user can set end date for recurring debt', function () {
    $user = User::factory()->create();
    $group = Group::factory()->createdBy($user)->withUsers(1)->create(['name' => 'Test Group']);

    $this->actingAs($user);

    visit("/groups/{$group->id}/recurring-debts/create")
        ->assertNoSmoke()
        ->assertSee('Create Recurring Debt for Test Group')
        ->fill('name', 'Limited Time Bill')
        ->fill('amount', '100.00')
        ->select('frequency', 'monthly')
        ->fill('start_date', now()->addDay()->format('Y-m-d'))
        ->fill('end_date', now()->addMonths(6)->format('Y-m-d'))
        ->click('Create Recurring Debt')
        ->assertPathIs("/groups/{$group->id}/recurring-debts")
        ->assertNoSmoke()
        ->assertSee('Recurring shared debt created successfully!')
        ->assertSee('Limited Time Bill');
});

test('user can view recurring debt details and member breakdown', function () {
    $user = User::factory()->create(['name' => 'John Doe']);
    $member1 = User::factory()->create(['name' => 'Jane Smith']);
    $member2 = User::factory()->create(['name' => 'Bob Wilson']);
    $group = Group::factory()->createdBy($user)->create(['name' => 'Test Group']);
    $group->users()->attach([$member1->id, $member2->id]);

    $recurringDebt = RecurringSharedDebt::factory()->forGroup($group)->createdBy($user)->monthly()->create([
        'name' => 'Netflix Subscription',
        'amount' => 15.99,
    ]);
    $recurringDebt->users()->attach([$user->id, $member1->id, $member2->id]);

    $this->actingAs($user);

    visit("/groups/{$group->id}/recurring-debts")
        ->assertNoSmoke()
        ->assertSee('Netflix Subscription')
        ->assertSee('€15.99')
        ->assertSee('John Doe')
        ->assertSee('Jane Smith')
        ->assertSee('Bob Wilson')
        ->assertSee('Split between 3 members');
});

test('user can cancel recurring debt creation', function () {
    $user = User::factory()->create();
    $group = Group::factory()->createdBy($user)->withUsers(1)->create();

    $this->actingAs($user);

    visit("/groups/{$group->id}/recurring-debts/create")
        ->assertNoSmoke()
        ->assertSee('Create Recurring Debt for')
        ->fill('name', 'Test Debt')
        ->fill('amount', '25.00')
        ->navigate("/groups/{$group->id}")
        ->assertPathIs("/groups/{$group->id}")
        ->assertNoSmoke();
});

test('user can cancel recurring debt editing', function () {
    $user = User::factory()->create();
    $group = Group::factory()->createdBy($user)->withUsers(1)->create();

    $recurringDebt = RecurringSharedDebt::factory()->forGroup($group)->createdBy($user)->active()->create([
        'name' => 'Original Name',
        'amount' => 100.00,
    ]);
    $recurringDebt->users()->attach($user);

    $this->actingAs($user);

    visit("/groups/{$group->id}/recurring-debts/{$recurringDebt->id}/edit")
        ->assertNoSmoke()
        ->assertSee('Edit Recurring Debt: Original Name')
        ->clear('name')
        ->fill('name', 'Changed Name')
        ->navigate("/groups/{$group->id}")
        ->assertPathIs("/groups/{$group->id}")
        ->assertNoSmoke()
        ->assertSee('Original Name')
        ->assertDontSee('Changed Name');
});

test('recurring debt displays correct frequency labels', function () {
    $user = User::factory()->create();
    $group = Group::factory()->createdBy($user)->withUsers(1)->create();

    $dailyDebt = RecurringSharedDebt::factory()->forGroup($group)->createdBy($user)->daily()->create(['name' => 'Daily Coffee']);
    $weeklyDebt = RecurringSharedDebt::factory()->forGroup($group)->createdBy($user)->weekly()->create(['name' => 'Weekly Groceries']);
    $monthlyDebt = RecurringSharedDebt::factory()->forGroup($group)->createdBy($user)->monthly()->create(['name' => 'Monthly Rent']);
    $yearlyDebt = RecurringSharedDebt::factory()->forGroup($group)->createdBy($user)->yearly()->create(['name' => 'Yearly Insurance']);

    foreach ([$dailyDebt, $weeklyDebt, $monthlyDebt, $yearlyDebt] as $debt) {
        $debt->users()->attach($user->id);
    }

    $this->actingAs($user);

    visit("/groups/{$group->id}/recurring-debts")
        ->assertNoSmoke()
        ->assertSee('Daily Coffee')
        ->assertSee('Daily')
        ->assertSee('Weekly Groceries')
        ->assertSee('Weekly')
        ->assertSee('Monthly Rent')
        ->assertSee('Monthly')
        ->assertSee('Yearly Insurance')
        ->assertSee('Yearly');
});

test('recurring debt shows correct status based on activity and expiration', function () {
    $user = User::factory()->create();
    $group = Group::factory()->createdBy($user)->withUsers(1)->create();

    $activeDebt = RecurringSharedDebt::factory()->forGroup($group)->createdBy($user)->active()->create(['name' => 'Active Debt']);
    $inactiveDebt = RecurringSharedDebt::factory()->forGroup($group)->createdBy($user)->inactive()->create(['name' => 'Inactive Debt']);
    $expiredDebt = RecurringSharedDebt::factory()->forGroup($group)->createdBy($user)->expired()->create(['name' => 'Expired Debt']);

    foreach ([$activeDebt, $inactiveDebt, $expiredDebt] as $debt) {
        $debt->users()->attach($user->id);
    }

    $this->actingAs($user);

    visit("/groups/{$group->id}/recurring-debts")
        ->assertNoSmoke()
        ->assertSee('Active Debt')
        ->waitForText('Active')
        ->assertSee('Inactive Debt')
        ->waitForText('Inactive')
        ->assertSee('Expired Debt')
        ->waitForText('Expired');
});

test('user can access recurring debts index page', function () {
    $user = User::factory()->create();
    $group = Group::factory()->createdBy($user)->withUsers(2)->create(['name' => 'Test Group']);
    $recurringDebt = RecurringSharedDebt::factory()->forGroup($group)->createdBy($user)->create([
        'name' => 'Test Recurring Debt',
        'amount' => 50.00,
    ]);
    $recurringDebt->users()->attach($user->id);

    $this->actingAs($user);

    visit("/groups/{$group->id}/recurring-debts")
        ->assertNoSmoke()
        ->assertSee('Test Recurring Debt')
        ->assertSee('€50.00');
});

test('recurring debt generation creates correct shared debt with users', function () {
    $user = User::factory()->create(['name' => 'Creator']);
    $member1 = User::factory()->create(['name' => 'Member 1']);
    $member2 = User::factory()->create(['name' => 'Member 2']);
    $group = Group::factory()->createdBy($user)->create(['name' => 'Test Group']);
    $group->users()->attach([$member1->id, $member2->id]);

    $recurringDebt = RecurringSharedDebt::factory()->forGroup($group)->createdBy($user)->active()->create([
        'name' => 'Shared Utility Bill',
        'amount' => 150.00,
    ]);
    $recurringDebt->users()->attach([$user->id, $member1->id]);

    $this->actingAs($user);

    visit("/groups/{$group->id}/recurring-debts")
        ->assertNoSmoke()
        ->click('Generate')
        ->assertPathIs("/groups/{$group->id}")
        ->assertNoSmoke()
        ->assertSee('Shared debt generated successfully!');

    // Verify the generated shared debt exists with correct data
    $generatedDebt = SharedDebt::where('recurring_shared_debt_id', $recurringDebt->id)->first();
    expect($generatedDebt)->not->toBeNull();
    expect($generatedDebt->name)->toBe('Shared Utility Bill');
    expect($generatedDebt->amount)->toBe(150.0);
    expect($generatedDebt->users)->toHaveCount(2);
    expect($generatedDebt->users->pluck('id')->toArray())->toContain($user->id, $member1->id);
});

test('inactive recurring debt cannot be manually generated', function () {
    $user = User::factory()->create();
    $group = Group::factory()->createdBy($user)->withUsers(1)->create(['name' => 'Test Group']);

    $recurringDebt = RecurringSharedDebt::factory()->forGroup($group)->createdBy($user)->inactive()->create([
        'name' => 'Inactive Debt',
        'amount' => 50.00,
    ]);
    $recurringDebt->users()->attach($user->id);

    $this->actingAs($user);

    visit("/groups/{$group->id}/recurring-debts")
        ->assertNoSmoke()
        ->assertSee('Inactive Debt')
        ->assertSee('Inactive')
        ->assertMissing('Generate'); // Button should not be present for inactive debts
});

test('expired recurring debt shows expired status', function () {
    $user = User::factory()->create();
    $group = Group::factory()->createdBy($user)->withUsers(1)->create(['name' => 'Test Group']);

    $recurringDebt = RecurringSharedDebt::factory()->forGroup($group)->createdBy($user)->expired()->create([
        'name' => 'Old Subscription',
        'amount' => 30.00,
        'end_date' => now()->subWeek(),
    ]);
    $recurringDebt->users()->attach($user->id);

    $this->actingAs($user);

    visit("/groups/{$group->id}/recurring-debts")
        ->assertNoSmoke()
        ->assertSee('Old Subscription')
        ->waitForText('Expired')
        ->assertMissing('Generate'); // Button should not be present for expired debts
});

test('recurring debt edit form displays current values correctly', function () {
    $user = User::factory()->create(['name' => 'Test User']);
    $member1 = User::factory()->create(['name' => 'Member One']);
    $group = Group::factory()->createdBy($user)->create(['name' => 'My Test Group']);
    $group->users()->attach($member1->id);

    $recurringDebt = RecurringSharedDebt::factory()->forGroup($group)->createdBy($user)->weekly()->create([
        'name' => 'Test Recurring Bill',
        'amount' => 75.50,
        'start_date' => now()->addDays(5),
        'end_date' => now()->addMonths(3),
        'description' => 'Test description',
    ]);
    $recurringDebt->users()->attach([$user->id, $member1->id]);

    $this->actingAs($user);

    visit("/groups/{$group->id}/recurring-debts/{$recurringDebt->id}/edit")
        ->assertNoSmoke()
        ->assertSee('Edit Recurring Debt: Test Recurring Bill')
        ->assertValue('name', 'Test Recurring Bill')
        ->assertValue('amount', '75.50')
        ->assertSelected('frequency', 'weekly')
        ->assertValue('start_date', now()->addDays(5)->format('Y-m-d'))
        ->assertValue('end_date', now()->addMonths(3)->format('Y-m-d'))
        ->assertValue('description', 'Test description')
        ->assertChecked("user-{$user->id}")
        ->assertChecked("user-{$member1->id}");
});
