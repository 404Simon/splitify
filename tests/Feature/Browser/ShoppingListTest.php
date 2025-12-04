<?php

declare(strict_types=1);

namespace Tests\Browser;

use App\Models\Group;
use App\Models\ShoppingList;
use App\Models\ShoppingListItem;
use App\Models\User;

test('user can create and view shopping lists', function () {
    $user = User::factory()->create();
    $group = Group::factory()->createdBy($user)->create(['name' => 'Test Group']);

    $this->actingAs($user);

    visit("/groups/{$group->id}")
        ->assertNoSmoke()
        ->click('Shopping Lists')
        ->assertPathIs("/groups/{$group->id}/shoppingLists")
        ->assertNoSmoke()
        ->assertSee('Shopping Lists')
        ->assertSee('No shopping lists yet')
        ->click('New Shopping List')
        ->assertPathIs("/groups/{$group->id}/shoppingLists/create")
        ->assertNoSmoke()
        ->fill('name', 'Weekly Groceries')
        ->click('Create Shopping List')
        ->assertPathIs("/groups/{$group->id}/shoppingLists")
        ->assertNoSmoke()
        ->assertSee('Shopping list created successfully!')
        ->assertSee('Weekly Groceries');
});

test('user can add items to shopping list', function () {
    $user = User::factory()->create();
    $group = Group::factory()->createdBy($user)->create();
    $shoppingList = ShoppingList::factory()->create([
        'group_id' => $group->id,
        'created_by' => $user->id,
        'name' => 'My Shopping List',
    ]);

    $this->actingAs($user);

    visit("/groups/{$group->id}/shoppingLists/{$shoppingList->id}")
        ->assertNoSmoke()
        ->assertSee('My Shopping List')
        ->assertSee('No items yet')
        ->fill('name', 'Milk')
        ->click('[data-testid="add-item-button"]')
        ->waitForText('Item added successfully!')
        ->assertSee('Milk')
        ->fill('name', 'Bread')
        ->click('[data-testid="add-item-button"]')
        ->waitForText('0 of 2 items completed')
        ->assertSee('Bread');
});

test('user can toggle item completion', function () {
    $user = User::factory()->create();
    $group = Group::factory()->createdBy($user)->create();
    $shoppingList = ShoppingList::factory()->create([
        'group_id' => $group->id,
        'created_by' => $user->id,
        'name' => 'Groceries',
    ]);
    $item = ShoppingListItem::factory()->create([
        'shopping_list_id' => $shoppingList->id,
        'name' => 'Eggs',
        'is_completed' => false,
    ]);

    $this->actingAs($user);

    $page = visit("/groups/{$group->id}/shoppingLists/{$shoppingList->id}");

    $page->assertNoSmoke()
        ->assertSee('Eggs')
        ->assertSee('0 of 1 items completed');

    // Click the item's toggle button using its testid
    $page->click("[data-testid=\"toggle-item-{$item->id}\"]");

    $page->waitForText('1 of 1 items completed');
});

test('user can toggle visibility of completed items', function () {
    $user = User::factory()->create();
    $group = Group::factory()->createdBy($user)->create();
    $shoppingList = ShoppingList::factory()->create([
        'group_id' => $group->id,
        'created_by' => $user->id,
        'name' => 'Groceries',
    ]);
    ShoppingListItem::factory()->create([
        'shopping_list_id' => $shoppingList->id,
        'name' => 'Completed Item',
        'is_completed' => true,
    ]);
    ShoppingListItem::factory()->create([
        'shopping_list_id' => $shoppingList->id,
        'name' => 'Pending Item',
        'is_completed' => false,
    ]);

    $this->actingAs($user);

    $page = visit("/groups/{$group->id}/shoppingLists/{$shoppingList->id}");

    $page->assertNoSmoke()
        ->assertDontSee('Completed Item')
        ->assertSee('Pending Item')
        ->assertSee('Show Completed')
        ->click('Show Completed');

    // After clicking show completed, completed item should be visible
    $page->assertSee('Completed Item')
        ->assertSee('Hide Completed');
});

test('user can delete shopping list', function () {
    $user = User::factory()->create();
    $group = Group::factory()->createdBy($user)->create();
    $shoppingList = ShoppingList::factory()->create([
        'group_id' => $group->id,
        'created_by' => $user->id,
        'name' => 'To Delete',
    ]);

    $this->actingAs($user);

    $page = visit("/groups/{$group->id}/shoppingLists/{$shoppingList->id}");

    $page->assertNoSmoke()
        ->assertSee('To Delete');

    // Click the delete button using its testid
    $page->click('[data-testid="delete-list-button"]');

    $page->waitForText('Delete To Delete')
        ->click('Delete List')
        ->assertPathIs("/groups/{$group->id}/shoppingLists")
        ->assertSee('Shopping list deleted successfully!')
        ->assertDontSee('To Delete');
});

test('user can edit shopping list name', function () {
    $user = User::factory()->create();
    $group = Group::factory()->createdBy($user)->create();
    $shoppingList = ShoppingList::factory()->create([
        'group_id' => $group->id,
        'created_by' => $user->id,
        'name' => 'Original Name',
    ]);

    $this->actingAs($user);

    $page = visit("/groups/{$group->id}/shoppingLists/{$shoppingList->id}");

    $page->assertNoSmoke()
        ->assertSee('Original Name');

    // Click the edit button using its testid
    $page->click('[data-testid="edit-list-button"]');

    $page->assertPathIs("/groups/{$group->id}/shoppingLists/{$shoppingList->id}/edit")
        ->assertNoSmoke()
        ->assertValue('name', 'Original Name')
        ->clear('name')
        ->fill('name', 'Updated Name')
        ->click('Update Shopping List')
        ->assertPathIs("/groups/{$group->id}/shoppingLists/{$shoppingList->id}")
        ->assertSee('Shopping list updated successfully!')
        ->assertSee('Updated Name')
        ->assertDontSee('Original Name');
});
