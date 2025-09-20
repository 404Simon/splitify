<?php

namespace Tests\Browser;

use App\Models\Group;
use App\Models\User;

test('user can create a group', function () {
    $user = User::factory()->create();

    $this->actingAs($user);

    visit('/')
        ->assertPathIs('/groups')
        ->assertNoSmoke()
        ->assertSee('Your Groups')
        ->assertSee('Manage your expense groups')
        ->assertSee('No groups yet')
        ->click('Create Your First Group')
        ->assertSee('New Group')
        ->assertPathIs('/groups/create')
        ->assertNoSmoke()
        ->fill('Group Name', 'Norway')
        ->click('Create Group')
        ->assertPathIs('/groups')
        ->assertDontSee('No groups yet')
        ->assertSee('Norway')
        ->assertSee('1 member');
});

test('user can delete his group', function () {
    $user = User::factory()->create();
    $group = Group::factory()->createdBy($user)->withUsers()->create();
    $userCount = $group->users->count();

    $this->actingAs($user);

    visit('/groups')
        ->assertNoSmoke()
        ->assertSee($group->name)
        ->assertSee("$userCount members")
        ->click('Edit')
        ->assertPathIs("/groups/$group->id/edit")
        ->assertNoSmoke()
        ->assertSee("Edit $group->name")
        ->click('Delete Group')
        ->click('Delete')
        ->assertPathIs('/groups')
        ->assertNoSmoke()
        ->assertSee('No groups yet');
});

test('user cannot edit group he doesnt have admin privileges in', function () {
    $user = User::factory()->create();
    $group = Group::factory()->withUsers()->create();
    $group->users()->attach($user);

    $this->actingAs($user);

    visit('/groups')
        ->assertNoSmoke()
        ->assertSee($group->name)
        ->assertDontSee('Edit')
        ->navigate("/groups/$group->id/edit")
        ->assertNoSmoke()
        ->assertDontSee('Delete Group')
        ->assertDontSee("Edit $group->name")
        ->assertSee('This action is Unauthorized');
});

test('user can edit group name', function () {
    $user = User::factory()->create();
    $group = Group::factory()->createdBy($user)->withUsers(2)->create(['name' => 'Original Group']);

    $this->actingAs($user);

    visit('/groups')
        ->assertNoSmoke()
        ->assertSee('Original Group')
        ->click('Edit')
        ->assertPathIs("/groups/$group->id/edit")
        ->assertNoSmoke()
        ->assertSee('Edit Original Group')
        ->assertSee('Group Name')
        ->clear('Group Name')
        ->fill('Group Name', 'Updated Group Name')
        ->click('Save Changes')
        ->assertPathIs('/groups')
        ->assertNoSmoke()
        ->assertSee('Updated Group Name')
        ->assertDontSee('Original Group');
});

test('user can view group members in edit form', function () {
    $user = User::factory()->create();
    $additionalUsers = User::factory()->count(3)->create();
    $group = Group::factory()->createdBy($user)->create(['name' => 'Test Group']);

    $group->users()->attach($additionalUsers->pluck('id'));

    $this->actingAs($user);

    $page = visit("/groups/$group->id/edit")
        ->assertNoSmoke()
        ->assertSee('Edit Test Group')
        ->assertSee('Group Members')
        ->assertSee($user->name);

    foreach ($additionalUsers as $additionalUser) {
        $page->assertSee($additionalUser->name);
    }
});

test('user can edit group members', function () {
    $user = User::factory()->create();
    $existingUser = User::factory()->create(['name' => 'Existing Member']);
    $stayingUser = User::factory()->create(['name' => 'Staying Member']);
    $group = Group::factory()->createdBy($user)->create(['name' => 'Test Group']);

    $group->users()->attach($existingUser);
    $group->users()->attach($stayingUser);

    $this->actingAs($user);

    $page = visit("/groups/$group->id/edit")
        ->assertNoSmoke()
        ->assertSee('Edit Test Group')
        ->assertSee('Group Members')
        ->assertSee('Current Members (3)')
        ->assertSee('Existing Member')
        ->assertSee('Staying Member')
        ->click("@remove-user-$existingUser->id")
        ->click('Save Changes')
        ->assertPathIs('/groups')
        ->assertNoSmoke();

    $this->assertFalse($group->fresh()->users->contains($existingUser->id));
    $this->assertTrue($group->fresh()->users->contains($stayingUser->id));

    $page->navigate("/groups/$group->id/edit")
        ->assertNoSmoke()
        ->assertSee('Current Members (2)')
        ->assertDontSee('Existing Member')
        ->assertSee('Staying Member');
});

test('user cannot remove themselves from group', function () {
    $user = User::factory()->create(['name' => 'Admin User']);
    $otherUser = User::factory()->create(['name' => 'Other Member']);
    $group = Group::factory()->createdBy($user)->create(['name' => 'Test Group']);

    $group->users()->attach($otherUser->id);

    $this->actingAs($user);

    visit("/groups/$group->id/edit")
        ->assertNoSmoke()
        ->assertSee('Admin User')
        ->assertSee('Admin')
        ->assertSee('Other Member')
        ->assertMissing("@remove-user-$user->id") // Admin cannot have remove button
        ->assertPresent("@remove-user-$otherUser->id"); // Other users should have remove button
});

test('group name validation prevents empty submission', function () {
    $user = User::factory()->create();
    $group = Group::factory()->createdBy($user)->withUsers(1)->create(['name' => 'Original Name']);

    $this->actingAs($user);

    visit("/groups/$group->id/edit")
        ->assertNoSmoke()
        ->assertSee('Edit Original Name')
        ->clear('name')
        ->click('Save Changes')
        ->assertPathIs("/groups/$group->id/edit") // Should stay on edit page due to validation error
        ->assertNoSmoke();

    $this->assertEquals('Original Name', $group->fresh()->name);
});

test('user can cancel group edit', function () {
    $user = User::factory()->create();
    $group = Group::factory()->createdBy($user)->withUsers(2)->create(['name' => 'Test Group']);

    $this->actingAs($user);

    visit("/groups/$group->id/edit")
        ->assertNoSmoke()
        ->assertSee('Edit Test Group')
        ->fill('Group Name', 'Changed Name')
        ->click('Cancel')
        ->assertPathIs('/groups')
        ->assertNoSmoke()
        ->assertSee('Test Group')
        ->assertDontSee('Changed Name');
});

test('user can delete group with confirmation', function () {
    $user = User::factory()->create();
    $group = Group::factory()->createdBy($user)->withUsers(2)->create(['name' => 'Group To Delete']);

    $this->actingAs($user);

    visit("/groups/$group->id/edit")
        ->assertNoSmoke()
        ->assertSee('Edit Group To Delete')
        ->assertSee('Danger Zone')
        ->assertSee('Deleting this group will permanently remove all associated data')
        ->click('Delete Group')
        ->assertSee('Delete Group To Delete')
        ->assertSee('Are you sure you want to delete this group?')
        ->assertSee('All shared debts and their payment history')
        ->assertSee('All transactions between members')
        ->assertSee('All recurring debt schedules')
        ->assertSee('All pending invitations')
        ->assertSee('All map markers and locations')
        ->assertSee('All member associations')
        ->click('Delete')
        ->assertPathIs('/groups')
        ->assertNoSmoke()
        ->assertSee('No groups yet')
        ->assertDontSee('Group To Delete');
});

test('user can see group deletion modal', function () {
    $user = User::factory()->create();
    $group = Group::factory()->createdBy($user)->withUsers(2)->create(['name' => 'Group To Keep']);

    $this->actingAs($user);

    visit("/groups/$group->id/edit")
        ->assertNoSmoke()
        ->assertSee('Edit Group To Keep')
        ->assertSee('Danger Zone')
        ->assertSee('Deleting this group will permanently remove all associated data')
        ->click('Delete Group')
        ->assertSee('Delete Group To Keep')
        ->assertSee('Are you sure you want to delete this group?')
        ->assertSee('This will permanently delete:')
        ->assertSee('All shared debts and their payment history')
        ->assertSee('Cancel')
        ->assertSee('Delete');
});

test('edit form displays current group data correctly', function () {
    $user = User::factory()->create(['name' => 'Admin User']);
    $member1 = User::factory()->create(['name' => 'Member One']);
    $member2 = User::factory()->create(['name' => 'Member Two']);
    $group = Group::factory()->createdBy($user)->create(['name' => 'My Test Group']);

    $group->users()->attach([$member1->id, $member2->id]);

    $this->actingAs($user);

    visit("/groups/$group->id/edit")
        ->assertNoSmoke()
        ->assertSee('Edit My Test Group')
        ->assertValue('name', 'My Test Group')
        ->assertSee('Group Members')
        ->assertSee('Current Members (3)')
        ->assertSee('Admin User')
        ->assertSee('Member One')
        ->assertSee('Member Two')
        ->assertSee('Admin');
});
