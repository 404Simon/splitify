<?php

declare(strict_types=1);

namespace Tests\Browser;

use App\Models\Group;
use App\Models\MapMarker;
use App\Models\User;
use Illuminate\Support\Facades\Http;

beforeEach(function () {
    Http::fake([
        '*' => Http::response([
            [
                'lat' => '49.4521',
                'lon' => '11.0767',
            ],
        ], 200),
    ]);
});

test('user can view map markers list in group', function () {
    $user = User::factory()->create();
    $group = Group::factory()->createdBy($user)->withUsers(1)->create(['name' => 'Test Group']);
    $mapMarker = MapMarker::factory()->forGroup($group)->createdBy($user)->create([
        'name' => 'Coffee Shop',
        'description' => 'Great coffee place',
        'emoji' => '☕',
        'address' => 'Main Street 123',
    ]);

    $this->actingAs($user);

    visit("/groups/{$group->id}/mapMarkers")
        ->assertNoSmoke()
        ->assertSee('Map Markers')
        ->assertSee('Test Group')
        ->assertSee('Coffee Shop')
        ->assertSee('Great coffee place')
        ->assertSee('☕')
        ->assertSee('Main Street 123');
});

test('user can create a new map marker', function () {
    $user = User::factory()->create(['name' => 'John Doe']);
    $group = Group::factory()->createdBy($user)->create(['name' => 'Test Group']);

    $this->actingAs($user);

    visit("/groups/{$group->id}/mapMarkers")
        ->assertNoSmoke()
        ->assertSee('Map Markers')
        ->assertSee('Test Group')
        ->click('Create Your First Map Marker')
        ->assertPathIs("/groups/{$group->id}/mapMarkers/create")
        ->assertNoSmoke()
        ->assertSee('New Map Marker')
        ->assertSee('Name')
        ->assertSee('Description')
        ->assertSee('Address')
        ->assertSee('Emoji')
        ->fill('name', 'Pizza Place')
        ->fill('description', 'Best pizza in town')
        ->fill('address', '456 Oak Street')
        ->click('Create Map Marker')
        ->assertPathIs("/groups/{$group->id}/mapMarkers")
        ->assertNoSmoke()
        ->assertSee('Map marker created successfully.')
        ->assertSee('Pizza Place')
        ->assertSee('Best pizza in town')
        ->assertSee('📍') // Default emoji
        ->assertSee('456 Oak Street');
});

test('user can edit an existing map marker', function () {
    $user = User::factory()->create();
    $group = Group::factory()->createdBy($user)->create(['name' => 'Test Group']);

    $mapMarker = MapMarker::factory()->forGroup($group)->createdBy($user)->create([
        'name' => 'Original Cafe',
        'description' => 'Original description',
        'address' => 'Original Street 123',
        'emoji' => '☕',
    ]);

    $this->actingAs($user);

    visit("/groups/{$group->id}/mapMarkers")
        ->assertNoSmoke()
        ->assertSee('Original Cafe')
        ->assertSee('Original description')
        ->click('Edit')
        ->assertPathIs("/groups/{$group->id}/mapMarkers/{$mapMarker->id}/edit")
        ->assertNoSmoke()
        ->assertSee('Edit Map Marker')
        ->assertValue('name', 'Original Cafe')
        ->assertValue('description', 'Original description')
        ->assertValue('address', 'Original Street 123')
        ->assertSee('☕')
        ->clear('name')
        ->fill('name', 'Updated Cafe')
        ->clear('description')
        ->fill('description', 'Updated description')
        ->clear('address')
        ->fill('address', 'Updated Street 456')
        ->click('☕')
        ->click('🏠')
        ->click('Update Map Marker')
        ->assertPathIs("/groups/{$group->id}/mapMarkers")
        ->assertNoSmoke()
        ->assertSee('Map marker updated successfully.')
        ->assertSee('Updated Cafe')
        ->assertSee('Updated description')
        ->assertSee('Updated Street 456')
        ->assertSee('🏠')
        ->assertDontSee('Original Cafe')
        ->assertDontSee('Original description');
});

test('user can delete a map marker', function () {
    $user = User::factory()->create();
    $group = Group::factory()->createdBy($user)->create(['name' => 'Test Group']);

    $mapMarker = MapMarker::factory()->forGroup($group)->createdBy($user)->create([
        'name' => 'Marker to Delete',
        'description' => 'Will be deleted',
        'emoji' => '🗑️',
    ]);

    $this->actingAs($user);

    visit("/groups/{$group->id}/mapMarkers")
        ->assertNoSmoke()
        ->assertSee('Marker to Delete')
        ->assertSee('Will be deleted')
        ->click('Edit')
        ->click('Delete Map Marker')
        ->click('Delete')
        ->assertPathIs("/groups/{$group->id}/mapMarkers")
        ->assertNoSmoke()
        ->assertSee('Map marker deleted successfully.')
        ->assertDontSee('Marker to Delete')
        ->assertDontSee('Will be deleted');
});

test('map marker form validation prevents empty submission', function () {
    $user = User::factory()->create();
    $group = Group::factory()->createdBy($user)->create();

    $this->actingAs($user);

    visit("/groups/{$group->id}/mapMarkers/create")
        ->assertNoSmoke()
        ->assertSee('New Map Marker')
        ->click('Create Map Marker')
        ->assertPathIs("/groups/{$group->id}/mapMarkers/create")
        ->assertNoSmoke();
});

test('map marker form validation requires name and address', function () {
    $user = User::factory()->create();
    $group = Group::factory()->createdBy($user)->create();

    $this->actingAs($user);

    visit("/groups/{$group->id}/mapMarkers/create")
        ->assertNoSmoke()
        ->fill('description', 'Only description filled')
        ->click('Create Map Marker')
        ->assertPathIs("/groups/{$group->id}/mapMarkers/create")
        ->assertNoSmoke();
});

test('user can view map marker details', function () {
    $user = User::factory()->create(['name' => 'John Doe']);
    $group = Group::factory()->createdBy($user)->create(['name' => 'Test Group']);

    $mapMarker = MapMarker::factory()->forGroup($group)->createdBy($user)->create([
        'name' => 'Detailed Marker',
        'description' => 'Detailed description with lots of information',
        'address' => '789 Elm Street, City, Country',
        'emoji' => '🏢',
    ]);

    $this->actingAs($user);

    visit("/groups/{$group->id}/mapMarkers/{$mapMarker->id}")
        ->assertNoSmoke()
        ->assertSee('Detailed Marker')
        ->assertSee('Detailed description with lots of information')
        ->assertSee('789 Elm Street, City, Country')
        ->assertSee('🏢')
        ->assertSee('Created by John Doe');
});

test('map marker displays coordinates information', function () {
    $user = User::factory()->create();
    $group = Group::factory()->createdBy($user)->create();

    $mapMarker = MapMarker::factory()->forGroup($group)->createdBy($user)->atLocation(52.5200, 13.4050)->create([
        'name' => 'Berlin Location',
        'address' => 'Berlin, Germany',
    ]);

    $this->actingAs($user);

    visit("/groups/{$group->id}/mapMarkers/{$mapMarker->id}")
        ->assertNoSmoke()
        ->assertSee('Berlin Location')
        ->assertSee('52.52')
        ->assertSee('13.405');
});

test('user can access map display from marker list', function () {
    $user = User::factory()->create();
    $group = Group::factory()->createdBy($user)->create(['name' => 'Test Group']);

    MapMarker::factory()->forGroup($group)->createdBy($user)->create([
        'name' => 'Test Marker',
    ]);

    $this->actingAs($user);

    visit("/groups/{$group->id}/mapMarkers")
        ->assertNoSmoke()
        ->click('View Map')
        ->assertPathIs("/groups/{$group->id}/map")
        ->assertNoSmoke()
        ->assertSee('Test Group');
});

test('empty marker list shows helpful message', function () {
    $user = User::factory()->create();
    $group = Group::factory()->createdBy($user)->create(['name' => 'Empty Group']);

    $this->actingAs($user);

    visit("/groups/{$group->id}/mapMarkers")
        ->assertNoSmoke()
        ->assertSee('Create Your First Map Marker')
        ->assertSee('No map markers yet');
});

test('user can navigate between marker actions', function () {
    $user = User::factory()->create();
    $group = Group::factory()->createdBy($user)->create();

    $mapMarker = MapMarker::factory()->forGroup($group)->createdBy($user)->create([
        'name' => 'Navigation Test',
    ]);

    $this->actingAs($user);

    visit("/groups/{$group->id}/mapMarkers")
        ->assertNoSmoke()
        ->click('View')
        ->assertPathIs("/groups/{$group->id}/mapMarkers/{$mapMarker->id}")
        ->assertNoSmoke()
        ->click('Edit Map Marker')
        ->assertPathIs("/groups/{$group->id}/mapMarkers/{$mapMarker->id}/edit")
        ->assertNoSmoke()
        ->click('Cancel')
        ->assertPathIs("/groups/{$group->id}/mapMarkers/{$mapMarker->id}")
        ->assertNoSmoke();
});

test('map marker creation with emoji selector', function () {
    $user = User::factory()->create();
    $group = Group::factory()->createdBy($user)->create();

    $this->actingAs($user);

    visit("/groups/{$group->id}/mapMarkers/create")
        ->assertNoSmoke()
        ->fill('name', 'Restaurant')
        ->fill('address', 'Food Street 123')
        ->click('📍') // CLick default emoji
        ->click('🍽️') // Click target emoji
        ->click('Create Map Marker')
        ->assertPathIs("/groups/{$group->id}/mapMarkers")
        ->assertNoSmoke()
        ->assertSee('Restaurant')
        ->assertSee('🍽️');
});

test('map marker shows creator information', function () {
    $creator = User::factory()->create(['name' => 'Alice Creator']);
    $viewer = User::factory()->create(['name' => 'Bob Viewer']);
    $group = Group::factory()->createdBy($creator)->create();
    $group->users()->attach($viewer);

    $mapMarker = MapMarker::factory()->forGroup($group)->createdBy($creator)->create([
        'name' => 'Alice Marker',
    ]);

    $this->actingAs($viewer);

    visit("/groups/{$group->id}/mapMarkers/{$mapMarker->id}")
        ->assertNoSmoke()
        ->assertSee('Alice Marker')
        ->assertSee('Alice Creator');
});
