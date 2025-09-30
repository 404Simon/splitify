<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Group;
use App\Models\RecurringSharedDebt;
use App\Models\SharedDebt;
use App\Models\User;
use Carbon\Carbon;

test('user can successfully create recurring shared debt with valid data', function () {
    $user = User::factory()->create();
    $member = User::factory()->create();
    $group = Group::factory()->createdBy($user)->create();
    $group->users()->attach($member);

    $this->actingAs($user);

    $response = $this->post("/groups/{$group->id}/recurring-debts", [
        'name' => 'Monthly Rent',
        'amount' => 500.00,
        'frequency' => 'monthly',
        'start_date' => now()->addDay()->format('Y-m-d'),
        'description' => 'Monthly rent payment',
        'members' => [$user->id, $member->id],
    ]);

    $response->assertRedirect("/groups/{$group->id}/recurring-debts")
        ->assertSessionHas('success', 'Recurring shared debt created successfully!');

    expect(RecurringSharedDebt::where('name', 'Monthly Rent')->exists())->toBeTrue();
    expect(RecurringSharedDebt::where('amount', 500.00)->first()->users()->count())->toBe(2);
});

test('recurring shared debt validation requires name field', function () {
    $user = User::factory()->create();
    $group = Group::factory()->createdBy($user)->withUsers(1)->create();

    $this->actingAs($user);

    $response = $this->post("/groups/{$group->id}/recurring-debts", [
        'amount' => 100.00,
        'frequency' => 'weekly',
        'start_date' => now()->addDay()->format('Y-m-d'),
        'members' => [$user->id],
    ]);

    $response->assertRedirect()
        ->assertSessionHasErrors(['name']);
});

test('recurring shared debt validation requires positive amount', function () {
    $user = User::factory()->create();
    $group = Group::factory()->createdBy($user)->withUsers(1)->create();

    $this->actingAs($user);

    $response = $this->post("/groups/{$group->id}/recurring-debts", [
        'name' => 'Test Debt',
        'amount' => -50.00,
        'frequency' => 'monthly',
        'start_date' => now()->addDay()->format('Y-m-d'),
        'members' => [$user->id],
    ]);

    $response->assertRedirect()
        ->assertSessionHasErrors(['amount']);
});

test('recurring shared debt validation requires valid frequency', function () {
    $user = User::factory()->create();
    $group = Group::factory()->createdBy($user)->withUsers(1)->create();

    $this->actingAs($user);

    $response = $this->post("/groups/{$group->id}/recurring-debts", [
        'name' => 'Test Debt',
        'amount' => 100.00,
        'frequency' => 'invalid_frequency',
        'start_date' => now()->addDay()->format('Y-m-d'),
        'members' => [$user->id],
    ]);

    $response->assertRedirect()
        ->assertSessionHasErrors(['frequency']);
});

test('recurring shared debt validation requires start date not in past', function () {
    $user = User::factory()->create();
    $group = Group::factory()->createdBy($user)->withUsers(1)->create();

    $this->actingAs($user);

    $response = $this->post("/groups/{$group->id}/recurring-debts", [
        'name' => 'Test Debt',
        'amount' => 100.00,
        'frequency' => 'weekly',
        'start_date' => now()->subDay()->format('Y-m-d'),
        'members' => [$user->id],
    ]);

    $response->assertRedirect()
        ->assertSessionHasErrors(['start_date']);
});

test('recurring shared debt validation requires at least one member', function () {
    $user = User::factory()->create();
    $group = Group::factory()->createdBy($user)->withUsers(1)->create();

    $this->actingAs($user);

    $response = $this->post("/groups/{$group->id}/recurring-debts", [
        'name' => 'Test Debt',
        'amount' => 100.00,
        'frequency' => 'monthly',
        'start_date' => now()->addDay()->format('Y-m-d'),
        'members' => [],
    ]);

    $response->assertRedirect()
        ->assertSessionHasErrors(['members']);
});

test('user cannot create recurring debt with members not in group', function () {
    $user = User::factory()->create();
    $outsideUser = User::factory()->create(['name' => 'Outside User']);
    $group = Group::factory()->createdBy($user)->withUsers(1)->create();

    $this->actingAs($user);

    $response = $this->post("/groups/{$group->id}/recurring-debts", [
        'name' => 'Invalid Debt',
        'amount' => 50.00,
        'frequency' => 'weekly',
        'start_date' => now()->addDay()->format('Y-m-d'),
        'members' => [$user->id, $outsideUser->id],
    ]);

    $response->assertRedirect()
        ->assertSessionHasErrors(['members']);
});

test('non-group-member cannot access recurring debt pages', function () {
    $user = User::factory()->create();
    $outsideUser = User::factory()->create();
    $group = Group::factory()->createdBy($user)->withUsers(1)->create();

    $this->actingAs($outsideUser);

    $response = $this->get("/groups/{$group->id}/recurring-debts/create");
    $response->assertForbidden();

    $response = $this->get("/groups/{$group->id}/recurring-debts");
    $response->assertForbidden();
});

test('user can update existing recurring shared debt', function () {
    $user = User::factory()->create();
    $member = User::factory()->create();
    $group = Group::factory()->createdBy($user)->create();
    $group->users()->attach($member);

    $recurringDebt = RecurringSharedDebt::factory()->forGroup($group)->createdBy($user)->create([
        'name' => 'Original Name',
        'amount' => 100.00,
        'frequency' => 'weekly',
    ]);
    $recurringDebt->users()->attach($user);

    $this->actingAs($user);

    $response = $this->put("/groups/{$group->id}/recurring-debts/{$recurringDebt->id}", [
        'name' => 'Updated Name',
        'amount' => 150.00,
        'frequency' => 'monthly',
        'start_date' => now()->addDay(),
        'end_date' => now()->addYear()->format('Y-m-d'),
        'description' => 'Updated description',
        'is_active' => true,
        'members' => [$user->id, $member->id],
    ]);

    $response->assertRedirect("/groups/{$group->id}/recurring-debts/{$recurringDebt->id}")
        ->assertSessionHas('success', 'Recurring shared debt updated successfully!');

    $recurringDebt->refresh();
    expect($recurringDebt->name)->toBe('Updated Name');
    expect($recurringDebt->amount)->toBe('150.00');
    expect($recurringDebt->frequency)->toBe('monthly');
    expect($recurringDebt->users()->count())->toBe(2);
});

test('user can delete recurring debt they created', function () {
    $user = User::factory()->create();
    $group = Group::factory()->createdBy($user)->withUsers(1)->create();
    $recurringDebt = RecurringSharedDebt::factory()->forGroup($group)->createdBy($user)->create();
    $recurringDebt->users()->attach($user);

    $this->actingAs($user);

    $response = $this->delete("/groups/{$group->id}/recurring-debts/{$recurringDebt->id}");

    $response->assertRedirect("/groups/{$group->id}/recurring-debts")
        ->assertSessionHas('success', 'Recurring shared debt deleted successfully!');

    expect(RecurringSharedDebt::find($recurringDebt->id))->toBeNull();
});

test('user cannot delete recurring debt they did not create', function () {
    $creator = User::factory()->create();
    $otherUser = User::factory()->create();
    $group = Group::factory()->createdBy($creator)->create();
    $group->users()->attach($otherUser);

    $recurringDebt = RecurringSharedDebt::factory()->forGroup($group)->createdBy($creator)->create();
    $recurringDebt->users()->attach([$creator, $otherUser]);

    $this->actingAs($otherUser);

    $response = $this->delete("/groups/{$group->id}/recurring-debts/{$recurringDebt->id}");

    $response->assertForbidden();
    expect(RecurringSharedDebt::find($recurringDebt->id))->not->toBeNull();
});

test('user can toggle recurring debt active status', function () {
    $user = User::factory()->create();
    $group = Group::factory()->createdBy($user)->withUsers(1)->create();
    $recurringDebt = RecurringSharedDebt::factory()->forGroup($group)->createdBy($user)->create([
        'is_active' => true,
    ]);

    $this->actingAs($user);

    $response = $this->patch("/groups/{$group->id}/recurring-debts/{$recurringDebt->id}/toggle-active");

    $response->assertRedirect()
        ->assertSessionHas('success', 'Recurring shared debt deactivated successfully!');

    $recurringDebt->refresh();
    expect($recurringDebt->is_active)->toBeFalse();

    $response = $this->patch("/groups/{$group->id}/recurring-debts/{$recurringDebt->id}/toggle-active");

    $response->assertRedirect()
        ->assertSessionHas('success', 'Recurring shared debt activated successfully!');

    $recurringDebt->refresh();
    expect($recurringDebt->is_active)->toBeTruthy();
});

test('user can generate shared debt manually from recurring debt', function () {
    $user = User::factory()->create();
    $member = User::factory()->create();
    $group = Group::factory()->createdBy($user)->create();
    $group->users()->attach($member);

    $recurringDebt = RecurringSharedDebt::factory()->forGroup($group)->createdBy($user)->create([
        'name' => 'Weekly Groceries',
        'amount' => 75.00,
        'is_active' => true,
    ]);
    $recurringDebt->users()->attach([$user, $member]);

    $this->actingAs($user);

    $response = $this->post("/groups/{$group->id}/recurring-debts/{$recurringDebt->id}/generate-now");

    $response->assertRedirect("/groups/{$group->id}")
        ->assertSessionHas('success', 'Shared debt generated successfully!');

    expect(SharedDebt::where('name', 'Weekly Groceries')->exists())->toBeTrue();
    expect(SharedDebt::where('recurring_shared_debt_id', $recurringDebt->id)->exists())->toBeTrue();
});

test('cannot generate shared debt from inactive recurring debt', function () {
    $user = User::factory()->create();
    $group = Group::factory()->createdBy($user)->withUsers(1)->create();
    $recurringDebt = RecurringSharedDebt::factory()->forGroup($group)->createdBy($user)->create([
        'is_active' => false,
    ]);

    $this->actingAs($user);

    $response = $this->post("/groups/{$group->id}/recurring-debts/{$recurringDebt->id}/generate-now");

    $response->assertRedirect()
        ->assertSessionHasErrors(['recurring_debt' => 'Cannot generate debt from inactive recurring debt.']);
});

test('recurring debt index page shows only group debts', function () {
    $user = User::factory()->create();
    $otherUser = User::factory()->create();

    $userGroup = Group::factory()->createdBy($user)->create();
    $otherGroup = Group::factory()->createdBy($otherUser)->create();

    $userDebt = RecurringSharedDebt::factory()->forGroup($userGroup)->createdBy($user)->create(['name' => 'User Debt']);
    $userDebt->users()->attach($user);

    $otherDebt = RecurringSharedDebt::factory()->forGroup($otherGroup)->createdBy($otherUser)->create(['name' => 'Other Debt']);
    $otherDebt->users()->attach($otherUser);

    $this->actingAs($user);

    $response = $this->get("/groups/{$userGroup->id}/recurring-debts");

    $response->assertSee('User Debt')
        ->assertDontSee('Other Debt');
});

test('recurring debt creation stores creator information', function () {
    $user = User::factory()->create();
    $member = User::factory()->create();
    $group = Group::factory()->createdBy($user)->create();
    $group->users()->attach($member);

    $this->actingAs($user);

    $this->post("/groups/{$group->id}/recurring-debts", [
        'name' => 'Creator Test',
        'amount' => 80.00,
        'frequency' => 'daily',
        'start_date' => now()->addDay()->format('Y-m-d'),
        'members' => [$user->id, $member->id],
    ]);

    $recurringDebt = RecurringSharedDebt::where('name', 'Creator Test')->first();
    expect($recurringDebt->created_by)->toBe($user->id);
    expect($recurringDebt->group_id)->toBe($group->id);
});

test('recurring debt can be created with decimal amounts', function () {
    $user = User::factory()->create();
    $group = Group::factory()->createdBy($user)->withUsers(1)->create();

    $this->actingAs($user);

    $response = $this->post("/groups/{$group->id}/recurring-debts", [
        'name' => 'Decimal Amount',
        'amount' => 15.73,
        'frequency' => 'weekly',
        'start_date' => now()->addDay()->format('Y-m-d'),
        'members' => [$user->id],
    ]);

    $response->assertRedirect("/groups/{$group->id}/recurring-debts");
    $recurringDebt = RecurringSharedDebt::where('name', 'Decimal Amount')->first();
    expect($recurringDebt->amount)->toBe('15.73');
});

test('recurring debt calculates next generation date correctly for different frequencies', function () {
    $user = User::factory()->create();
    $group = Group::factory()->createdBy($user)->withUsers(1)->create();

    $startDate = Carbon::parse('2024-01-01');

    $frequencies = [
        'daily' => '2024-01-02',
        'weekly' => '2024-01-08',
        'monthly' => '2024-02-01',
        'yearly' => '2025-01-01',
    ];

    foreach ($frequencies as $frequency => $expectedDate) {
        $recurringDebt = RecurringSharedDebt::factory()->forGroup($group)->createdBy($user)->create([
            'frequency' => $frequency,
            'next_generation_date' => $startDate,
        ]);

        $nextDate = $recurringDebt->calculateNextGenerationDate();
        expect($nextDate->format('Y-m-d'))->toBe($expectedDate);
    }
});

test('shouldGenerate returns correct values based on conditions', function () {
    $user = User::factory()->create();
    $group = Group::factory()->createdBy($user)->withUsers(1)->create();

    // Active debt with future generation date
    $futureDebt = RecurringSharedDebt::factory()->forGroup($group)->createdBy($user)->create([
        'is_active' => true,
        'next_generation_date' => now()->addDay(),
        'end_date' => null,
    ]);
    expect($futureDebt->shouldGenerate())->toBeFalse();

    // Active debt with past generation date
    $pastDebt = RecurringSharedDebt::factory()->forGroup($group)->createdBy($user)->create([
        'is_active' => true,
        'next_generation_date' => now()->subDay(),
        'end_date' => null,
    ]);
    expect($pastDebt->shouldGenerate())->toBeTrue();

    // Inactive debt
    $inactiveDebt = RecurringSharedDebt::factory()->forGroup($group)->createdBy($user)->create([
        'is_active' => false,
        'next_generation_date' => now()->subDay(),
    ]);
    expect($inactiveDebt->shouldGenerate())->toBeFalse();

    // Active debt but past end date
    $expiredDebt = RecurringSharedDebt::factory()->forGroup($group)->createdBy($user)->create([
        'is_active' => true,
        'next_generation_date' => now()->subDay(),
        'end_date' => now()->subWeek(),
    ]);
    expect($expiredDebt->shouldGenerate())->toBeFalse();
});

test('generateSharedDebt creates debt and updates next generation date', function () {
    $user = User::factory()->create();
    $member = User::factory()->create();
    $group = Group::factory()->createdBy($user)->create();
    $group->users()->attach($member);

    $recurringDebt = RecurringSharedDebt::factory()->forGroup($group)->createdBy($user)->create([
        'name' => 'Test Generation',
        'amount' => 100.00,
        'frequency' => 'weekly',
        'next_generation_date' => now(),
    ]);
    $recurringDebt->users()->attach([$user, $member]);

    $originalNextDate = $recurringDebt->next_generation_date;
    $sharedDebt = $recurringDebt->generateSharedDebt();

    expect($sharedDebt->name)->toBe('Test Generation');
    expect($sharedDebt->amount)->toBe(100.00);
    expect($sharedDebt->group_id)->toBe($group->id);
    expect($sharedDebt->recurring_shared_debt_id)->toBe($recurringDebt->id);
    expect($sharedDebt->users()->count())->toBe(2);

    $recurringDebt->refresh();
    expect($recurringDebt->next_generation_date->gt($originalNextDate))->toBeTrue();
});

test('end date validation requires date after start date', function () {
    $user = User::factory()->create();
    $group = Group::factory()->createdBy($user)->withUsers(1)->create();

    $this->actingAs($user);

    $startDate = now()->addDay();
    $response = $this->post("/groups/{$group->id}/recurring-debts", [
        'name' => 'Test Debt',
        'amount' => 100.00,
        'frequency' => 'weekly',
        'start_date' => $startDate->format('Y-m-d'),
        'end_date' => $startDate->subDay()->format('Y-m-d'), // End before start
        'members' => [$user->id],
    ]);

    $response->assertRedirect()
        ->assertSessionHasErrors(['end_date']);
});

test('user cannot update recurring debt they did not create', function () {
    $creator = User::factory()->create();
    $otherUser = User::factory()->create();
    $group = Group::factory()->createdBy($creator)->create();
    $group->users()->attach($otherUser);

    $recurringDebt = RecurringSharedDebt::factory()->forGroup($group)->createdBy($creator)->create([
        'name' => 'Original',
        'amount' => 50.00,
    ]);

    $this->actingAs($otherUser);

    $response = $this->put("/groups/{$group->id}/recurring-debts/{$recurringDebt->id}", [
        'name' => 'Updated',
        'amount' => 100.00,
        'frequency' => 'monthly',
        'members' => [$otherUser->id],
    ]);

    $response->assertForbidden();

    $recurringDebt->refresh();
    expect($recurringDebt->name)->toBe('Original');
    expect($recurringDebt->amount)->toBe('50.00');
});

test('recurring debt relationships work correctly', function () {
    $creator = User::factory()->create(['name' => 'John Creator']);
    $member = User::factory()->create(['name' => 'Jane Member']);
    $group = Group::factory()->create(['name' => 'Test Group']);
    $group->users()->attach([$creator, $member]);

    $recurringDebt = RecurringSharedDebt::factory()->forGroup($group)->createdBy($creator)->create([
        'name' => 'Monthly Bill',
        'amount' => 200.00,
    ]);
    $recurringDebt->users()->attach([$creator, $member]);

    expect($recurringDebt->creator->name)->toBe('John Creator');
    expect($recurringDebt->group->name)->toBe('Test Group');
    expect($recurringDebt->users()->count())->toBe(2);
    expect($recurringDebt->users->contains('name', 'John Creator'))->toBeTrue();
    expect($recurringDebt->users->contains('name', 'Jane Member'))->toBeTrue();
});

test('recurring debt status attribute returns correct values', function () {
    $user = User::factory()->create();
    $group = Group::factory()->createdBy($user)->withUsers(1)->create();

    // Active debt
    $activeDebt = RecurringSharedDebt::factory()->forGroup($group)->createdBy($user)->create([
        'is_active' => true,
        'end_date' => null,
    ]);
    expect($activeDebt->status)->toBe('Active');

    // Inactive debt
    $inactiveDebt = RecurringSharedDebt::factory()->forGroup($group)->createdBy($user)->create([
        'is_active' => false,
    ]);
    expect($inactiveDebt->status)->toBe('Inactive');

    // Expired debt
    $expiredDebt = RecurringSharedDebt::factory()->forGroup($group)->createdBy($user)->create([
        'is_active' => true,
        'end_date' => now()->subDay(),
    ]);
    expect($expiredDebt->status)->toBe('Expired');
});

test('updating recurring debt preserves nullable fields when not provided', function () {
    $user = User::factory()->create();
    $member = User::factory()->create();
    $group = Group::factory()->createdBy($user)->create();
    $group->users()->attach($member);

    $recurringDebt = RecurringSharedDebt::factory()->forGroup($group)->createdBy($user)->create([
        'name' => 'Original Name',
        'amount' => 50.00,
        'frequency' => 'weekly',
        'end_date' => now()->addMonth(),
        'description' => 'Original description',
    ]);
    $recurringDebt->users()->attach($user);

    $this->actingAs($user);

    // Update only required fields, omitting nullable fields
    $response = $this->put("/groups/{$group->id}/recurring-debts/{$recurringDebt->id}", [
        'name' => 'Updated Name Only',
        'amount' => 75.00,
        'frequency' => 'monthly',
        'start_date' => $recurringDebt->start_date->subDay(),
        'is_active' => true,
        'members' => [$user->id],
    ]);

    $response->assertRedirect("/groups/{$group->id}/recurring-debts/{$recurringDebt->id}")
        ->assertSessionHas('success', 'Recurring shared debt updated successfully!');

    $recurringDebt->refresh();

    // Required fields should be updated
    expect($recurringDebt->name)->toBe('Updated Name Only');
    expect($recurringDebt->amount)->toBe('75.00');
    expect($recurringDebt->frequency)->toBe('monthly');

    // Nullable fields should be preserved
    expect($recurringDebt->end_date)->not->toBeNull();
    expect($recurringDebt->end_date->format('Y-m-d'))->toBe(now()->addMonth()->format('Y-m-d'));
    expect($recurringDebt->description)->toBe('Original description');
});

test('updating recurring debt allows clearing nullable fields when explicitly provided as null', function () {
    $user = User::factory()->create();
    $member = User::factory()->create();
    $group = Group::factory()->createdBy($user)->create();
    $group->users()->attach($member);

    $recurringDebt = RecurringSharedDebt::factory()->forGroup($group)->createdBy($user)->create([
        'name' => 'Test Name',
        'amount' => 100.00,
        'frequency' => 'monthly',
        'end_date' => now()->addMonth(),
        'description' => 'Test description',
    ]);
    $recurringDebt->users()->attach($user);

    $this->actingAs($user);

    $response = $this->put("/groups/{$group->id}/recurring-debts/{$recurringDebt->id}", [
        'name' => 'Updated Name',
        'amount' => 100.00,
        'frequency' => 'monthly',
        'start_date' => $recurringDebt->start_date,
        'end_date' => '', // Empty string should be converted to null
        'description' => '', // Empty string should be converted to null
        'is_active' => true,
        'members' => [$user->id],
    ]);

    $response->assertRedirect("/groups/{$group->id}/recurring-debts/{$recurringDebt->id}")
        ->assertSessionHas('success', 'Recurring shared debt updated successfully!');

    $recurringDebt->refresh();

    // Nullable fields should be cleared when explicitly provided as empty
    expect($recurringDebt->end_date)->toBeNull();
    expect($recurringDebt->description)->toBeNull();
});
