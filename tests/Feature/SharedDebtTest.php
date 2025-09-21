<?php

namespace Tests\Feature;

use App\Models\Group;
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
