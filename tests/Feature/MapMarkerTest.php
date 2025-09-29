<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Group;
use App\Models\MapMarker;
use App\Models\User;
use Illuminate\Support\Facades\Http;

test('user can create map marker with valid data', function () {
    Http::fake([
        '*' => Http::response([
            [
                'lat' => '49.4521',
                'lon' => '11.0767',
            ],
        ], 200),
    ]);

    $user = User::factory()->create();
    $group = Group::factory()->createdBy($user)->create();

    $this->actingAs($user);

    $response = $this->post("/groups/{$group->id}/mapMarkers", [
        'name' => 'Coffee Shop',
        'description' => 'Great coffee place',
        'address' => 'Main Street 123',
        'emoji' => '☕',
    ]);

    $response->assertRedirect("/groups/{$group->id}/mapMarkers")
        ->assertSessionHas('success', 'Map marker created successfully.');

    expect(MapMarker::where('name', 'Coffee Shop')->exists())->toBeTrue();
    $mapMarker = MapMarker::where('name', 'Coffee Shop')->first();
    expect($mapMarker->created_by)->toBe($user->id);
    expect($mapMarker->group_id)->toBe($group->id);
    expect($mapMarker->description)->toBe('Great coffee place');
    expect($mapMarker->address)->toBe('Main Street 123');
    expect($mapMarker->emoji)->toBe('☕');
    expect($mapMarker->lat)->toBe(49.4521);
    expect($mapMarker->lon)->toBe(11.0767);
});

test('map marker validation requires name field', function () {
    $user = User::factory()->create();
    $group = Group::factory()->createdBy($user)->create();

    $this->actingAs($user);

    $response = $this->post("/groups/{$group->id}/mapMarkers", [
        'address' => 'Test Address',
    ]);

    $response->assertRedirect()
        ->assertSessionHasErrors(['name']);
});

test('map marker validation requires address field', function () {
    $user = User::factory()->create();
    $group = Group::factory()->createdBy($user)->create();

    $this->actingAs($user);

    $response = $this->post("/groups/{$group->id}/mapMarkers", [
        'name' => 'Test Location',
    ]);

    $response->assertRedirect()
        ->assertSessionHasErrors(['address']);
});

test('map marker validation enforces name length limit', function () {
    $user = User::factory()->create();
    $group = Group::factory()->createdBy($user)->create();

    $this->actingAs($user);

    $response = $this->post("/groups/{$group->id}/mapMarkers", [
        'name' => str_repeat('a', 51), // Exceeds 50 character limit
        'address' => 'Test Address',
    ]);

    $response->assertRedirect()
        ->assertSessionHasErrors(['name']);
});

test('map marker validation enforces description length limit', function () {
    $user = User::factory()->create();
    $group = Group::factory()->createdBy($user)->create();

    $this->actingAs($user);

    $response = $this->post("/groups/{$group->id}/mapMarkers", [
        'name' => 'Test Location',
        'description' => str_repeat('a', 257), // Exceeds 256 character limit
        'address' => 'Test Address',
    ]);

    $response->assertRedirect()
        ->assertSessionHasErrors(['description']);
});

test('map marker creation fails when geolocation service returns null', function () {
    Http::fake([
        '*' => Http::response([], 200),
    ]);

    $user = User::factory()->create();
    $group = Group::factory()->createdBy($user)->create();

    $this->actingAs($user);

    $response = $this->post("/groups/{$group->id}/mapMarkers", [
        'name' => 'Invalid Location',
        'address' => 'Invalid Address',
    ]);

    $response->assertRedirect()
        ->assertSessionHas('error', 'Could not find address..');

    expect(MapMarker::where('name', 'Invalid Location')->exists())->toBeFalse();
});

test('user can update existing map marker', function () {
    Http::fake([
        '*' => Http::response([
            [
                'lat' => '49.4521',
                'lon' => '11.0767',
            ],
        ], 200),
    ]);

    $user = User::factory()->create();
    $group = Group::factory()->createdBy($user)->create();
    $mapMarker = MapMarker::factory()->forGroup($group)->createdBy($user)->create([
        'name' => 'Original Name',
        'description' => 'Original description',
        'address' => 'Original Address',
        'emoji' => '📍',
    ]);

    $this->actingAs($user);

    $response = $this->put("/groups/{$group->id}/mapMarkers/{$mapMarker->id}", [
        'name' => 'Updated Name',
        'description' => 'Updated description',
        'address' => 'Updated Address',
        'emoji' => '🏠',
    ]);

    $response->assertRedirect("/groups/{$group->id}/mapMarkers")
        ->assertSessionHas('success', 'Map marker updated successfully.');

    $mapMarker->refresh();
    expect($mapMarker->name)->toBe('Updated Name');
    expect($mapMarker->description)->toBe('Updated description');
    expect($mapMarker->address)->toBe('Updated Address');
    expect($mapMarker->emoji)->toBe('🏠');
});

test('user cannot update map marker they did not create and are not group admin', function () {
    $creator = User::factory()->create();
    $otherUser = User::factory()->create();
    $group = Group::factory()->createdBy($creator)->create();
    $group->users()->attach($otherUser);

    $mapMarker = MapMarker::factory()->forGroup($group)->createdBy($creator)->create();

    $this->actingAs($otherUser);

    $response = $this->put("/groups/{$group->id}/mapMarkers/{$mapMarker->id}", [
        'name' => 'Unauthorized Update',
        'address' => 'Test Address',
    ]);

    $response->assertForbidden();
});

test('group admin can update any map marker in their group', function () {
    $groupAdmin = User::factory()->create();
    $member = User::factory()->create();
    $group = Group::factory()->createdBy($groupAdmin)->create();
    $group->users()->attach($member);

    $mapMarker = MapMarker::factory()->forGroup($group)->createdBy($member)->create([
        'name' => 'Member Created',
    ]);

    $this->actingAs($groupAdmin);

    $response = $this->put("/groups/{$group->id}/mapMarkers/{$mapMarker->id}", [
        'name' => 'Admin Updated',
        'address' => $mapMarker->address,
    ]);

    $response->assertRedirect("/groups/{$group->id}/mapMarkers")
        ->assertSessionHas('success', 'Map marker updated successfully.');

    $mapMarker->refresh();
    expect($mapMarker->name)->toBe('Admin Updated');
});

test('user can delete map marker they created', function () {
    $user = User::factory()->create();
    $group = Group::factory()->createdBy($user)->create();
    $mapMarker = MapMarker::factory()->forGroup($group)->createdBy($user)->create();

    $this->actingAs($user);

    $response = $this->delete("/groups/{$group->id}/mapMarkers/{$mapMarker->id}");

    $response->assertRedirect("/groups/{$group->id}/mapMarkers")
        ->assertSessionHas('success', 'Map marker deleted successfully.');

    expect(MapMarker::find($mapMarker->id))->toBeNull();
});

test('user cannot delete map marker they did not create and are not group admin', function () {
    $creator = User::factory()->create();
    $otherUser = User::factory()->create();
    $group = Group::factory()->createdBy($creator)->create();
    $group->users()->attach($otherUser);

    $mapMarker = MapMarker::factory()->forGroup($group)->createdBy($creator)->create();

    $this->actingAs($otherUser);

    $response = $this->delete("/groups/{$group->id}/mapMarkers/{$mapMarker->id}");

    $response->assertForbidden();
    expect(MapMarker::find($mapMarker->id))->not->toBeNull();
});

test('non-group-member cannot access map marker pages', function () {
    $user = User::factory()->create();
    $outsideUser = User::factory()->create();
    $group = Group::factory()->createdBy($user)->create();

    $this->actingAs($outsideUser);

    $response = $this->get("/groups/{$group->id}/mapMarkers/create");
    $response->assertForbidden();

    $response = $this->get("/groups/{$group->id}/mapMarkers");
    $response->assertForbidden();
});

test('map marker index page shows only group markers', function () {
    $user = User::factory()->create();
    $otherUser = User::factory()->create();

    $userGroup = Group::factory()->createdBy($user)->create();
    $otherGroup = Group::factory()->createdBy($otherUser)->create();

    $userMarker = MapMarker::factory()->forGroup($userGroup)->createdBy($user)->create(['name' => 'User Marker']);
    $otherMarker = MapMarker::factory()->forGroup($otherGroup)->createdBy($otherUser)->create(['name' => 'Other Marker']);

    $this->actingAs($user);

    $response = $this->get("/groups/{$userGroup->id}/mapMarkers");

    $response->assertSee('User Marker')
        ->assertDontSee('Other Marker');
});

test('map marker creation stores creator information', function () {
    Http::fake([
        '*' => Http::response([
            [
                'lat' => '49.4521',
                'lon' => '11.0767',
            ],
        ], 200),
    ]);

    $user = User::factory()->create();
    $group = Group::factory()->createdBy($user)->create();

    $this->actingAs($user);

    $this->post("/groups/{$group->id}/mapMarkers", [
        'name' => 'Creator Test',
        'address' => 'Test Address',
    ]);

    $mapMarker = MapMarker::where('name', 'Creator Test')->first();
    expect($mapMarker->created_by)->toBe($user->id);
});

test('map marker update only geocodes when address changes', function () {
    $user = User::factory()->create();
    $group = Group::factory()->createdBy($user)->create();
    $mapMarker = MapMarker::factory()->forGroup($group)->createdBy($user)->create([
        'address' => 'Original Address',
        'lat' => 50.0,
        'lon' => 10.0,
    ]);

    $this->actingAs($user);

    $response = $this->put("/groups/{$group->id}/mapMarkers/{$mapMarker->id}", [
        'name' => 'Updated Name Only',
        'address' => 'Original Address', // Same address
    ]);

    Http::assertNothingSent();

    $response->assertRedirect("/groups/{$group->id}/mapMarkers");

    $mapMarker->refresh();
    expect($mapMarker->name)->toBe('Updated Name Only');
    expect($mapMarker->lat)->toBe(50.0); // Coordinates unchanged
    expect($mapMarker->lon)->toBe(10.0);
});

test('map marker update preserves nullable fields when not provided', function () {
    $user = User::factory()->create();
    $group = Group::factory()->createdBy($user)->create();
    $mapMarker = MapMarker::factory()->forGroup($group)->createdBy($user)->create([
        'name' => 'Original Name',
        'description' => 'Original description',
        'address' => 'Original Address',
        'emoji' => '🏠',
        'lat' => 50.0,
        'lon' => 10.0,
    ]);

    $this->actingAs($user);

    // Update only required fields - should preserve nullable ones
    $response = $this->put("/groups/{$group->id}/mapMarkers/{$mapMarker->id}", [
        'name' => 'Updated Name',
        'address' => 'Original Address', // Same address to avoid geocoding
    ]);

    $response->assertRedirect("/groups/{$group->id}/mapMarkers")
        ->assertSessionHas('success', 'Map marker updated successfully.');

    $mapMarker->refresh();
    expect($mapMarker->name)->toBe('Updated Name');
    expect($mapMarker->description)->toBe('Original description'); // Should be preserved
    expect($mapMarker->emoji)->toBe('🏠'); // Should be preserved
    expect($mapMarker->address)->toBe('Original Address');
});

test('map marker update can clear nullable fields when explicitly provided as empty', function () {
    $user = User::factory()->create();
    $group = Group::factory()->createdBy($user)->create();
    $mapMarker = MapMarker::factory()->forGroup($group)->createdBy($user)->create([
        'name' => 'Original Name',
        'description' => 'Original description',
        'address' => 'Original Address',
        'emoji' => '🏠',
        'lat' => 50.0,
        'lon' => 10.0,
    ]);

    $this->actingAs($user);

    // Explicitly provide empty values for nullable fields
    $response = $this->put("/groups/{$group->id}/mapMarkers/{$mapMarker->id}", [
        'name' => 'Updated Name',
        'description' => '', // Explicitly empty
        'address' => 'Original Address',
        'emoji' => '', // Explicitly empty
    ]);

    $response->assertRedirect("/groups/{$group->id}/mapMarkers")
        ->assertSessionHas('success', 'Map marker updated successfully.');

    $mapMarker->refresh();
    expect($mapMarker->name)->toBe('Updated Name');
    expect($mapMarker->description)->toBeNull(); // Should be null
    expect($mapMarker->emoji)->toBe('📍'); // Should be default emoji
    expect($mapMarker->address)->toBe('Original Address');
});

test('map marker creation sets default values for optional fields when not provided', function () {
    Http::fake([
        '*' => Http::response([
            [
                'lat' => '49.4521',
                'lon' => '11.0767',
            ],
        ], 200),
    ]);

    $user = User::factory()->create();
    $group = Group::factory()->createdBy($user)->create();

    $this->actingAs($user);

    $response = $this->post("/groups/{$group->id}/mapMarkers", [
        'name' => 'Minimal Marker',
        'address' => 'Main Street 123',
        // no description and emoji
    ]);

    $response->assertRedirect("/groups/{$group->id}/mapMarkers")
        ->assertSessionHas('success', 'Map marker created successfully.');

    $mapMarker = MapMarker::where('name', 'Minimal Marker')->first();
    expect($mapMarker->description)->toBeNull();
    expect($mapMarker->emoji)->toBe('📍');
});
