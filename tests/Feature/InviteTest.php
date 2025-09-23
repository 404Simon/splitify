<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Group;
use App\Models\Invite;
use App\Models\User;

test('admin can create invite with valid data', function () {
    $admin = User::factory()->create();
    $group = Group::factory()->createdBy($admin)->create();

    $this->actingAs($admin);

    $response = $this->post("/groups/{$group->id}/invites", [
        'duration_days' => 7,
        'name' => 'Test Invite',
        'is_reusable' => true,
    ]);

    $response->assertRedirect("/groups/{$group->id}/invites")
        ->assertSessionHas('success', 'Invite created successfully!');

    expect(Invite::where('name', 'Test Invite')->exists())->toBeTrue();
    $invite = Invite::where('name', 'Test Invite')->first();
    expect($invite->duration_days)->toBe(7);
    expect($invite->is_reusable)->toBeTrue();
    expect($invite->group_id)->toBe($group->id);
});

test('admin can create single-use invite', function () {
    $admin = User::factory()->create();
    $group = Group::factory()->createdBy($admin)->create();

    $this->actingAs($admin);

    $response = $this->post("/groups/{$group->id}/invites", [
        'duration_days' => 3,
        'name' => 'Single Use Invite',
        'is_reusable' => false,
    ]);

    $response->assertRedirect("/groups/{$group->id}/invites");

    $invite = Invite::where('name', 'Single Use Invite')->first();
    expect($invite->is_reusable)->toBeFalse();
});

test('non-admin cannot create invites', function () {
    $admin = User::factory()->create();
    $member = User::factory()->create();
    $group = Group::factory()->createdBy($admin)->create();
    $group->users()->attach($member);

    $this->actingAs($member);

    $response = $this->post("/groups/{$group->id}/invites", [
        'duration_days' => 7,
        'name' => 'Unauthorized Invite',
    ]);

    $response->assertStatus(403);
    expect(Invite::where('name', 'Unauthorized Invite')->exists())->toBeFalse();
});

test('non-group-member cannot create invites', function () {
    $admin = User::factory()->create();
    $outsider = User::factory()->create();
    $group = Group::factory()->createdBy($admin)->create();

    $this->actingAs($outsider);

    $response = $this->post("/groups/{$group->id}/invites", [
        'duration_days' => 7,
        'name' => 'Outsider Invite',
    ]);

    $response->assertStatus(403);
    expect(Invite::where('name', 'Outsider Invite')->exists())->toBeFalse();
});

test('invite creation validation requires duration_days', function () {
    $admin = User::factory()->create();
    $group = Group::factory()->createdBy($admin)->create();

    $this->actingAs($admin);

    $response = $this->post("/groups/{$group->id}/invites", [
        'name' => 'Invalid Invite',
    ]);

    $response->assertRedirect()
        ->assertSessionHasErrors(['duration_days']);
});

test('invite creation validation enforces duration limits', function () {
    $admin = User::factory()->create();
    $group = Group::factory()->createdBy($admin)->create();

    $this->actingAs($admin);

    // Test minimum
    $response = $this->post("/groups/{$group->id}/invites", [
        'duration_days' => 0,
        'name' => 'Too Short',
    ]);
    $response->assertRedirect()
        ->assertSessionHasErrors(['duration_days']);

    // Test maximum
    $response = $this->post("/groups/{$group->id}/invites", [
        'duration_days' => 31,
        'name' => 'Too Long',
    ]);
    $response->assertRedirect()
        ->assertSessionHasErrors(['duration_days']);
});

test('invite creation validation enforces name length', function () {
    $admin = User::factory()->create();
    $group = Group::factory()->createdBy($admin)->create();

    $this->actingAs($admin);

    $response = $this->post("/groups/{$group->id}/invites", [
        'duration_days' => 7,
        'name' => str_repeat('a', 129), // Over 128 character limit
    ]);

    $response->assertRedirect()
        ->assertSessionHasErrors(['name']);
});

test('user can accept valid invite', function () {
    $admin = User::factory()->create();
    $user = User::factory()->create();
    $group = Group::factory()->createdBy($admin)->create();
    $invite = Invite::factory()->forGroup($group)->singleUse()->validFor(7)->create();

    $this->actingAs($user);

    $response = $this->post("/invites/{$invite->uuid}/accept");

    $response->assertRedirect("/groups/{$group->id}")
        ->assertSessionHas('success', 'You have successfully joined the group!');

    expect($group->users()->where('user_id', $user->id)->exists())->toBeTrue();
    expect(Invite::find($invite->uuid))->toBeNull(); // Single-use invite should be deleted
});

test('user can accept reusable invite', function () {
    $admin = User::factory()->create();
    $user = User::factory()->create();
    $group = Group::factory()->createdBy($admin)->create();
    $invite = Invite::factory()->forGroup($group)->reusable()->validFor(7)->create();

    $this->actingAs($user);

    $response = $this->post("/invites/{$invite->uuid}/accept");

    $response->assertRedirect("/groups/{$group->id}")
        ->assertSessionHas('success', 'You have successfully joined the group!');

    expect($group->users()->where('user_id', $user->id)->exists())->toBeTrue();
    expect(Invite::find($invite->uuid))->not->toBeNull(); // Reusable invite should remain
});

test('user cannot accept expired invite', function () {
    $admin = User::factory()->create();
    $user = User::factory()->create();
    $group = Group::factory()->createdBy($admin)->create();
    $invite = Invite::factory()->forGroup($group)->expired()->create();

    $this->actingAs($user);

    $response = $this->post("/invites/{$invite->uuid}/accept");

    $response->assertRedirect()
        ->assertSessionHas('error', 'The invite is not valid or has expired.');

    expect($group->users()->where('user_id', $user->id)->exists())->toBeFalse();
});

test('user cannot accept invite if already group member', function () {
    $admin = User::factory()->create();
    $user = User::factory()->create();
    $group = Group::factory()->createdBy($admin)->create();
    $group->users()->attach($user);
    $invite = Invite::factory()->forGroup($group)->validFor(7)->create();

    $this->actingAs($user);

    $response = $this->post("/invites/{$invite->uuid}/accept");

    $response->assertRedirect()
        ->assertSessionHas('error', 'You are already a member of this group.');
});

test('user can deny single-use invite', function () {
    $admin = User::factory()->create();
    $user = User::factory()->create();
    $group = Group::factory()->createdBy($admin)->create();
    $invite = Invite::factory()->forGroup($group)->singleUse()->validFor(7)->create();

    $this->actingAs($user);

    $response = $this->post("/invites/{$invite->uuid}/deny");

    $response->assertRedirect('/groups')
        ->assertSessionHas('error', 'You did not join the group.');

    expect($group->users()->where('user_id', $user->id)->exists())->toBeFalse();
    expect(Invite::find($invite->uuid))->toBeNull(); // Single-use invite should be deleted
});

test('user can deny reusable invite', function () {
    $admin = User::factory()->create();
    $user = User::factory()->create();
    $group = Group::factory()->createdBy($admin)->create();
    $invite = Invite::factory()->forGroup($group)->reusable()->validFor(7)->create();

    $this->actingAs($user);

    $response = $this->post("/invites/{$invite->uuid}/deny");

    $response->assertRedirect('/groups')
        ->assertSessionHas('error', 'You did not join the group.');

    expect($group->users()->where('user_id', $user->id)->exists())->toBeFalse();
    expect(Invite::find($invite->uuid))->not->toBeNull(); // Reusable invite should remain
});

test('guest is redirected to login when viewing invite', function () {
    $admin = User::factory()->create();
    $group = Group::factory()->createdBy($admin)->create();
    $invite = Invite::factory()->forGroup($group)->validFor(7)->create();

    $response = $this->get("/invite/{$invite->uuid}");

    $response->assertRedirect('/login')
        ->assertSessionHas('message', 'You were invited to join a group in Splitify. Please log in or create an Account to accept it.');
});

test('authenticated user can view valid invite', function () {
    $admin = User::factory()->create();
    $user = User::factory()->create();
    $group = Group::factory()->createdBy($admin)->create(['name' => 'Test Group']);
    $invite = Invite::factory()->forGroup($group)->validFor(7)->create();

    $this->actingAs($user);

    $response = $this->get("/invite/{$invite->uuid}");

    $response->assertSuccessful()
        ->assertSee('Test Group')
        ->assertSee('Accept Invitation')
        ->assertSee('Decline Invitation');
});

test('admin can view invites index', function () {
    $admin = User::factory()->create();
    $group = Group::factory()->createdBy($admin)->create();
    $invite1 = Invite::factory()->forGroup($group)->create(['name' => 'Invite 1']);
    $invite2 = Invite::factory()->forGroup($group)->create(['name' => 'Invite 2']);

    $this->actingAs($admin);

    $response = $this->get("/groups/{$group->id}/invites");

    $response->assertSuccessful();
});

test('admin can delete invite', function () {
    $admin = User::factory()->create();
    $group = Group::factory()->createdBy($admin)->create();
    $invite = Invite::factory()->forGroup($group)->create(['name' => 'To Delete']);

    $this->actingAs($admin);

    $response = $this->delete("/groups/{$group->id}/invites/{$invite->uuid}");

    $response->assertRedirect("/groups/{$group->id}/invites")
        ->assertSessionHas('success', 'Invite deleted successfully!');

    expect(Invite::find($invite->uuid))->toBeNull();
});

test('non-admin cannot delete invite', function () {
    $admin = User::factory()->create();
    $member = User::factory()->create();
    $group = Group::factory()->createdBy($admin)->create();
    $group->users()->attach($member);
    $invite = Invite::factory()->forGroup($group)->create(['name' => 'Protected']);

    $this->actingAs($member);

    $response = $this->delete("/groups/{$group->id}/invites/{$invite->uuid}");

    $response->assertStatus(403);
    expect(Invite::find($invite->uuid))->not->toBeNull();
});

test('invite isValid method works correctly', function () {
    $group = Group::factory()->create();

    // Valid invite
    $validInvite = Invite::factory()->forGroup($group)->validFor(7)->create();
    expect($validInvite->isValid())->toBeTrue();

    // Expired invite
    $expiredInvite = Invite::factory()->forGroup($group)->expired()->create();
    expect($expiredInvite->isValid())->toBeFalse();

    // Manually set invite with 0 duration (bypassing model protection)
    $zeroDurationInvite = Invite::factory()->forGroup($group)->create();
    $zeroDurationInvite->update(['duration_days' => 0]);
    expect($zeroDurationInvite->fresh()->isValid())->toBeFalse();
});

test('invite stores creator information correctly', function () {
    $admin = User::factory()->create();
    $group = Group::factory()->createdBy($admin)->create();

    $this->actingAs($admin);

    $this->post("/groups/{$group->id}/invites", [
        'duration_days' => 7,
        'name' => 'Creator Test',
        'is_reusable' => false,
    ]);

    $invite = Invite::where('name', 'Creator Test')->first();
    expect($invite->group_id)->toBe($group->id);
    expect($invite->group->created_by)->toBe($admin->id);
});

test('invite creation sets default values correctly', function () {
    $admin = User::factory()->create();
    $group = Group::factory()->createdBy($admin)->create();

    $this->actingAs($admin);

    $response = $this->post("/groups/{$group->id}/invites", [
        'duration_days' => 5,
        'name' => 'Default Test',
        // Not setting is_reusable
    ]);

    $response->assertRedirect();

    $invite = Invite::where('name', 'Default Test')->first();
    expect($invite->is_reusable)->toBeFalse(); // Should default to false
    expect($invite->duration_days)->toBe(5);
});

test('invite uuid generation works correctly', function () {
    $admin = User::factory()->create();
    $group = Group::factory()->createdBy($admin)->create();

    $this->actingAs($admin);

    $this->post("/groups/{$group->id}/invites", [
        'duration_days' => 7,
        'name' => 'UUID Test',
    ]);

    $invite = Invite::where('name', 'UUID Test')->first();
    expect($invite->uuid)->not->toBeNull();
    expect(mb_strlen($invite->uuid))->toBe(36); // Standard UUID length
});
