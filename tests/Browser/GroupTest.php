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
