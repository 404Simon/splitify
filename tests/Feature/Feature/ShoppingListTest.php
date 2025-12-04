<?php

declare(strict_types=1);

use App\Models\Group;
use App\Models\ShoppingList;
use App\Models\ShoppingListItem;
use App\Models\User;

test('user can create shopping list for their group', function () {
    $user = User::factory()->create();
    $group = Group::factory()->createdBy($user)->create();

    $this->actingAs($user);

    $response = $this->post("/groups/{$group->id}/shoppingLists", [
        'name' => 'Weekly Groceries',
    ]);

    $response->assertRedirect("/groups/{$group->id}/shoppingLists")
        ->assertSessionHas('success', 'Shopping list created successfully!');

    expect(ShoppingList::where('name', 'Weekly Groceries')->exists())->toBeTrue();
});

test('shopping list validation requires name field', function () {
    $user = User::factory()->create();
    $group = Group::factory()->createdBy($user)->create();

    $this->actingAs($user);

    $response = $this->post("/groups/{$group->id}/shoppingLists", [
        'name' => '',
    ]);

    $response->assertRedirect()
        ->assertSessionHasErrors(['name']);
});

test('non-group-member cannot create shopping list', function () {
    $user = User::factory()->create();
    $outsideUser = User::factory()->create();
    $group = Group::factory()->createdBy($user)->create();

    $this->actingAs($outsideUser);

    $response = $this->post("/groups/{$group->id}/shoppingLists", [
        'name' => 'Unauthorized List',
    ]);

    $response->assertForbidden();
});

test('user can view shopping lists for their group', function () {
    $user = User::factory()->create();
    $group = Group::factory()->createdBy($user)->create();
    $shoppingList = ShoppingList::factory()->create([
        'group_id' => $group->id,
        'created_by' => $user->id,
        'name' => 'My Shopping List',
    ]);

    $this->actingAs($user);

    $response = $this->get("/groups/{$group->id}/shoppingLists");

    $response->assertOk()
        ->assertSee('My Shopping List');
});

test('user can add items to shopping list', function () {
    $user = User::factory()->create();
    $group = Group::factory()->createdBy($user)->create();
    $shoppingList = ShoppingList::factory()->create([
        'group_id' => $group->id,
        'created_by' => $user->id,
    ]);

    $this->actingAs($user);

    $response = $this->post("/groups/{$group->id}/shoppingLists/{$shoppingList->id}/items", [
        'name' => 'Milk',
    ]);

    $response->assertRedirect()
        ->assertSessionHas('success', 'Item added successfully!');

    expect(ShoppingListItem::where('name', 'Milk')->exists())->toBeTrue();
});

test('item validation requires name field', function () {
    $user = User::factory()->create();
    $group = Group::factory()->createdBy($user)->create();
    $shoppingList = ShoppingList::factory()->create([
        'group_id' => $group->id,
        'created_by' => $user->id,
    ]);

    $this->actingAs($user);

    $response = $this->post("/groups/{$group->id}/shoppingLists/{$shoppingList->id}/items", [
        'name' => '',
    ]);

    $response->assertRedirect()
        ->assertSessionHasErrors(['name']);
});

test('user can toggle item completion status', function () {
    $user = User::factory()->create();
    $group = Group::factory()->createdBy($user)->create();
    $shoppingList = ShoppingList::factory()->create([
        'group_id' => $group->id,
        'created_by' => $user->id,
    ]);
    $item = ShoppingListItem::factory()->create([
        'shopping_list_id' => $shoppingList->id,
        'name' => 'Bread',
        'is_completed' => false,
    ]);

    $this->actingAs($user);

    $response = $this->patch("/groups/{$group->id}/shoppingLists/{$shoppingList->id}/items/{$item->id}/toggle");

    $response->assertRedirect();

    $item->refresh();
    expect($item->is_completed)->toBeTrue();
});

test('user can delete items from shopping list', function () {
    $user = User::factory()->create();
    $group = Group::factory()->createdBy($user)->create();
    $shoppingList = ShoppingList::factory()->create([
        'group_id' => $group->id,
        'created_by' => $user->id,
    ]);
    $item = ShoppingListItem::factory()->create([
        'shopping_list_id' => $shoppingList->id,
        'name' => 'Eggs',
    ]);

    $this->actingAs($user);

    $response = $this->delete("/groups/{$group->id}/shoppingLists/{$shoppingList->id}/items/{$item->id}");

    $response->assertRedirect()
        ->assertSessionHas('success', 'Item deleted successfully!');

    expect(ShoppingListItem::find($item->id))->toBeNull();
});

test('user can update shopping list name', function () {
    $user = User::factory()->create();
    $group = Group::factory()->createdBy($user)->create();
    $shoppingList = ShoppingList::factory()->create([
        'group_id' => $group->id,
        'created_by' => $user->id,
        'name' => 'Original Name',
    ]);

    $this->actingAs($user);

    $response = $this->put("/groups/{$group->id}/shoppingLists/{$shoppingList->id}", [
        'name' => 'Updated Name',
    ]);

    $response->assertRedirect()
        ->assertSessionHas('success', 'Shopping list updated successfully!');

    $shoppingList->refresh();
    expect($shoppingList->name)->toBe('Updated Name');
});

test('only creator can delete shopping list', function () {
    $creator = User::factory()->create();
    $otherUser = User::factory()->create();
    $group = Group::factory()->createdBy($creator)->create();
    $group->users()->attach($otherUser);

    $shoppingList = ShoppingList::factory()->create([
        'group_id' => $group->id,
        'created_by' => $creator->id,
    ]);

    $this->actingAs($otherUser);

    $response = $this->delete("/groups/{$group->id}/shoppingLists/{$shoppingList->id}");

    $response->assertForbidden();
    expect(ShoppingList::find($shoppingList->id))->not->toBeNull();
});

test('creator can delete shopping list', function () {
    $user = User::factory()->create();
    $group = Group::factory()->createdBy($user)->create();
    $shoppingList = ShoppingList::factory()->create([
        'group_id' => $group->id,
        'created_by' => $user->id,
    ]);

    $this->actingAs($user);

    $response = $this->delete("/groups/{$group->id}/shoppingLists/{$shoppingList->id}");

    $response->assertRedirect()
        ->assertSessionHas('success', 'Shopping list deleted successfully!');

    expect(ShoppingList::find($shoppingList->id))->toBeNull();
});

test('non-group-member cannot view shopping list', function () {
    $user = User::factory()->create();
    $outsideUser = User::factory()->create();
    $group = Group::factory()->createdBy($user)->create();
    $shoppingList = ShoppingList::factory()->create([
        'group_id' => $group->id,
        'created_by' => $user->id,
    ]);

    $this->actingAs($outsideUser);

    $response = $this->get("/groups/{$group->id}/shoppingLists/{$shoppingList->id}");

    $response->assertForbidden();
});

test('group members can add and toggle items on any list', function () {
    $creator = User::factory()->create();
    $member = User::factory()->create();
    $group = Group::factory()->createdBy($creator)->create();
    $group->users()->attach($member);

    $shoppingList = ShoppingList::factory()->create([
        'group_id' => $group->id,
        'created_by' => $creator->id,
    ]);

    $this->actingAs($member);

    // Member can add items
    $response = $this->post("/groups/{$group->id}/shoppingLists/{$shoppingList->id}/items", [
        'name' => 'Cheese',
    ]);

    $response->assertRedirect();
    expect(ShoppingListItem::where('name', 'Cheese')->exists())->toBeTrue();

    // Member can toggle items
    $item = ShoppingListItem::where('name', 'Cheese')->first();
    $response = $this->patch("/groups/{$group->id}/shoppingLists/{$shoppingList->id}/items/{$item->id}/toggle");

    $response->assertRedirect();
    $item->refresh();
    expect($item->is_completed)->toBeTrue();
});
