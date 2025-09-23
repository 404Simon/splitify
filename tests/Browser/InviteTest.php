<?php

namespace Tests\Browser;

use App\Models\Group;
use App\Models\Invite;
use App\Models\User;

test('admin can create invite', function () {
    $admin = User::factory()->create();
    $group = Group::factory()->createdBy($admin)->create(['name' => 'Test Group']);

    $this->actingAs($admin);

    visit("/groups/{$group->id}/invites")
        ->assertNoSmoke()
        ->click('Add Invite')
        ->assertPathIs("/groups/{$group->id}/invites/create")
        ->assertNoSmoke()
        ->assertSee('New Invite')
        ->assertSee('Duration Days')
        ->assertSee('Note')
        ->assertSee('Reusable')
        ->fill('duration_days', '7')
        ->fill('name', 'Browser Test Invite')
        ->check('is_reusable')
        ->click('Create Invite')
        ->assertPathIs("/groups/{$group->id}/invites")
        ->assertNoSmoke()
        ->assertSee('Invite created successfully!');

    expect(Invite::where('name', 'Browser Test Invite')->exists())->toBeTrue();
});

test('admin can view invites list', function () {
    $admin = User::factory()->create();
    $group = Group::factory()->createdBy($admin)->create(['name' => 'Test Group']);
    $invite1 = Invite::factory()->forGroup($group)->create(['name' => 'First Invite']);
    $invite2 = Invite::factory()->forGroup($group)->reusable()->create(['name' => 'Reusable Invite']);

    $this->actingAs($admin);

    visit("/groups/{$group->id}/invites")
        ->assertNoSmoke()
        ->assertSee('Manage Invites for Test Group')
        ->assertSee('First Invite')
        ->assertSee('Reusable Invite')
        ->assertSee('Add Invite');
});

test('admin can delete invite from list', function () {
    $admin = User::factory()->create();
    $group = Group::factory()->createdBy($admin)->create(['name' => 'Test Group']);
    $invite = Invite::factory()->forGroup($group)->create(['name' => 'To Delete']);

    $this->actingAs($admin);

    visit("/groups/{$group->id}/invites")
        ->assertNoSmoke()
        ->assertSee('To Delete')
        ->click('button[title="Delete invite"]') // Click the delete button by CSS selector
        ->assertNoSmoke()
        ->assertSee('Invite deleted successfully!')
        ->assertDontSee('To Delete');

    expect(Invite::where('name', 'To Delete')->exists())->toBeFalse();
});

test('user can accept invite through browser', function () {
    $admin = User::factory()->create();
    $user = User::factory()->create(['name' => 'John Doe']);
    $group = Group::factory()->createdBy($admin)->create(['name' => 'Awesome Group']);
    $invite = Invite::factory()->forGroup($group)->singleUse()->validFor(7)->create();

    $this->actingAs($user);

    visit("/invite/{$invite->uuid}")
        ->assertNoSmoke()
        ->assertSee("You've been invited to join Awesome Group")
        ->assertSee('Accept Invitation')
        ->assertSee('Decline Invitation')
        ->click('Accept Invitation')
        ->assertPathIs("/groups/{$group->id}")
        ->assertNoSmoke()
        ->assertSee('You have successfully joined the group!')
        ->assertSee('Awesome Group');

    expect($group->users()->find($user)->exists())->toBeTrue();
});

test('user can decline invite through browser', function () {
    $admin = User::factory()->create();
    $user = User::factory()->create();
    $group = Group::factory()->createdBy($admin)->create(['name' => 'Group to Decline']);
    $invite = Invite::factory()->forGroup($group)->singleUse()->validFor(7)->create();

    $this->actingAs($user);

    visit("/invite/{$invite->uuid}")
        ->assertNoSmoke()
        ->assertSee("You've been invited to join Group to Decline")
        ->click('Decline Invitation')
        ->assertPathIs('/groups')
        ->assertNoSmoke()
        ->assertSee('You did not join the group.');

    expect($group->users()->where('user_id', $user->id)->exists())->toBeFalse();
});

test('guest visiting invite link is redirected to login and can accept after registration', function () {
    $admin = User::factory()->create();
    $group = Group::factory()->createdBy($admin)->create(['name' => 'Test Group']);
    $invite = Invite::factory()->forGroup($group)->validFor(7)->create();

    $this->actingAsGuest();

    visit("/invite/{$invite->uuid}")
        ->assertPathIs('/login')
        ->assertNoSmoke()
        ->assertSee('You were invited to join a group in Splitify')
        ->click('@register')
        ->fill('Name', 'Simon')
        ->fill('Email', 'leak@me.de')
        ->fill('Password', 'thisisnotsecure')
        ->fill('Confirm Password', 'thisisnotsecure')
        ->click('Register')
        ->assertPathIs("/invite/{$invite->uuid}")
        ->click('Accept Invitation')
        ->assertPathIs("/groups/{$group->id}")
        ->assertNoSmoke()
        ->assertSee('You have successfully joined the group!')
        ->assertSee('Test Group');

    $user = User::where('email', 'leak@me.de')->first();

    expect($group->users()->find($user)->exists())->toBeTrue();
});

test('guest visiting invite link is redirected to login and can accept after login', function () {
    $admin = User::factory()->create();
    $group = Group::factory()->createdBy($admin)->create(['name' => 'Test Group']);
    $invite = Invite::factory()->forGroup($group)->validFor(7)->create();

    $user = User::factory()->create();

    $this->actingAsGuest();

    visit("/invite/{$invite->uuid}")
        ->assertPathIs('/login')
        ->assertNoSmoke()
        ->assertSee('You were invited to join a group in Splitify')
        ->fill('Email', $user->email)
        ->fill('Password', 'password')
        ->click('Log in')
        ->assertPathIs("/invite/{$invite->uuid}")
        ->click('Accept Invitation')
        ->assertPathIs("/groups/{$group->id}")
        ->assertNoSmoke()
        ->assertSee('You have successfully joined the group!')
        ->assertSee('Test Group');

    expect($group->users()->find($user)->exists())->toBeTrue();
});

test('invite form validation prevents invalid submissions', function () {
    $admin = User::factory()->create();
    $group = Group::factory()->createdBy($admin)->create();

    $this->actingAs($admin);

    // duration too high
    visit("/groups/{$group->id}/invites/create")
        ->assertNoSmoke()
        ->fill('duration_days', '50')
        ->fill('name', 'Invalid Invite')
        ->click('Create Invite')
        ->assertPathIs("/groups/{$group->id}/invites/create")
        ->assertNoSmoke();

    // duration too low
    visit("/groups/{$group->id}/invites/create")
        ->assertNoSmoke()
        ->fill('duration_days', '0')
        ->fill('name', 'Invalid Invite')
        ->click('Create Invite')
        ->assertPathIs("/groups/{$group->id}/invites/create")
        ->assertNoSmoke();
});

test('user cannot access invite pages if not group member', function () {
    $admin = User::factory()->create();
    $group = Group::factory()->createdBy($admin)->create(['name' => 'Test Group']);

    $other = User::factory()->create();

    $this->actingAs($other);

    visit("/groups/{$group->id}/invites")
        ->assertNoSmoke()
        ->assertSee('403')
        ->assertSee('Unauthorized');
});

test('non-admin group member cannot access invite management', function () {
    $admin = User::factory()->create();
    $group = Group::factory()->createdBy($admin)->create(['name' => 'Test Group']);
    $other = User::factory()->create();
    $group->users()->attach($other);

    $this->actingAs($other);

    visit("/groups/{$group->id}/invites")
        ->assertNoSmoke()
        ->assertSee('403')
        ->assertSee('Unauthorized');
});

test('expired invite shows appropriate error message', function () {
    $admin = User::factory()->create();
    $user = User::factory()->create();
    $group = Group::factory()->createdBy($admin)->create(['name' => 'Test Group']);
    $invite = Invite::factory()->forGroup($group)->expired()->create();

    $this->actingAs($user);

    visit("/invite/{$invite->uuid}")
        ->assertNoSmoke()
        ->assertSee('The invite is not valid or has expired');
});

test('user already in group sees appropriate message', function () {
    $admin = User::factory()->create();
    $user = User::factory()->create();
    $group = Group::factory()->createdBy($admin)->create(['name' => 'Test Group']);
    $group->users()->attach($user);
    $invite = Invite::factory()->forGroup($group)->validFor(7)->create();

    $this->actingAs($user);

    visit("/invite/{$invite->uuid}")
        ->assertNoSmoke()
        ->assertSee('You are already a member of this group');
});

test('invite creation form displays correctly', function () {
    $admin = User::factory()->create();
    $group = Group::factory()->createdBy($admin)->create(['name' => 'Test Group']);

    $this->actingAs($admin);

    visit("/groups/{$group->id}/invites/create")
        ->assertNoSmoke()
        ->assertSee('New Invite')
        ->assertSee('Duration Days')
        ->assertValue('duration_days', '1') // Default value
        ->assertSee('Note')
        ->assertSee('Reusable')
        ->assertNotChecked('is_reusable')
        ->assertSee('Create Invite');
});

test('invite creation with different duration values', function () {
    $admin = User::factory()->create();
    $group = Group::factory()->createdBy($admin)->create();

    $this->actingAs($admin);

    // Test various valid durations
    $durations = [1, 3, 7, 14, 30];

    foreach ($durations as $duration) {
        visit("/groups/{$group->id}/invites/create")
            ->assertNoSmoke()
            ->fill('duration_days', (string) $duration)
            ->fill('name', "Test Invite {$duration} days")
            ->click('Create Invite')
            ->assertPathIs("/groups/{$group->id}/invites")
            ->assertNoSmoke()
            ->assertSee('Invite created successfully!');

        $invite = Invite::where('name', "Test Invite {$duration} days")->first();
        expect($invite->duration_days)->toBe($duration);
    }
});

test('reusable invite can be used multiple times', function () {
    $admin = User::factory()->create();
    $user1 = User::factory()->create(['name' => 'User One']);
    $user2 = User::factory()->create(['name' => 'User Two']);
    $group = Group::factory()->createdBy($admin)->create(['name' => 'Multi-Use Group']);
    $invite = Invite::factory()->forGroup($group)->reusable()->validFor(7)->create();

    // First user accepts
    $this->actingAs($user1);
    visit("/invite/{$invite->uuid}")
        ->assertNoSmoke()
        ->click('Accept Invitation')
        ->assertPathIs("/groups/{$group->id}")
        ->assertNoSmoke();

    expect($group->users()->find($user1)->exists())->toBeTrue();
    expect(Invite::find($invite->uuid))->not->toBeNull();

    // Second user accepts same invite
    $this->actingAs($user2);
    visit("/invite/{$invite->uuid}")
        ->assertNoSmoke()
        ->click('Accept Invitation')
        ->assertPathIs("/groups/{$group->id}")
        ->assertNoSmoke();

    expect($group->users()->find($user2)->exists())->toBeTrue();
    expect(Invite::find($invite->uuid))->not->toBeNull();
});

test('single-use invite is deleted after acceptance', function () {
    $admin = User::factory()->create();
    $user = User::factory()->create();
    $group = Group::factory()->createdBy($admin)->create(['name' => 'Single Use Group']);
    $invite = Invite::factory()->forGroup($group)->singleUse()->validFor(7)->create();

    $this->actingAs($user);

    visit("/invite/{$invite->uuid}")
        ->assertNoSmoke()
        ->click('Accept Invitation')
        ->assertPathIs("/groups/{$group->id}")
        ->assertNoSmoke();

    expect($group->users()->find($user)->exists())->toBeTrue();
    expect(Invite::find($invite->uuid))->toBeNull();
});

test('single-use invite is deleted after declining', function () {
    $admin = User::factory()->create();
    $user = User::factory()->create();
    $group = Group::factory()->createdBy($admin)->create(['name' => 'Single Use Group']);
    $invite = Invite::factory()->forGroup($group)->singleUse()->validFor(7)->create();

    $this->actingAs($user);

    visit("/invite/{$invite->uuid}")
        ->assertNoSmoke()
        ->click('Decline Invitation')
        ->assertPathIs('/groups')
        ->assertNoSmoke();

    expect($group->users()->where('user_id', $user->id)->exists())->toBeFalse();
    expect(Invite::find($invite->uuid))->toBeNull();
});

test('invite page shows group information', function () {
    $admin = User::factory()->create(['name' => 'Admin User']);
    $user = User::factory()->create();
    $group = Group::factory()->createdBy($admin)->create(['name' => 'Amazing Travel Group']);
    $invite = Invite::factory()->forGroup($group)->validFor(7)->create(['name' => 'Join us for adventures!']);

    $this->actingAs($user);

    visit("/invite/{$invite->uuid}")
        ->assertNoSmoke()
        ->assertSee("You've been invited to join Amazing Travel Group")
        ->assertSee('Accept Invitation')
        ->assertSee('Decline Invitation');
});

test('invalid invite uuid shows 404', function () {
    $user = User::factory()->create();

    $this->actingAs($user);

    visit('/invite/waszurhoelle')
        ->assertNoSmoke()
        ->assertSee('404')
        ->assertSee('Not Found');
});
