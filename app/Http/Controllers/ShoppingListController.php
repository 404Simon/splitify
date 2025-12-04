<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Group;
use App\Models\ShoppingList;
use App\Models\ShoppingListItem;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

final class ShoppingListController extends Controller
{
    public function index(Group $group): View
    {
        $this->authorize('view', $group);

        $shoppingLists = $group->shoppingLists()
            ->latest()
            ->get();

        return view('shoppingLists.index', ['group' => $group, 'shoppingLists' => $shoppingLists]);
    }

    public function create(Group $group): View
    {
        $this->authorize('view', $group);

        return view('shoppingLists.create', ['group' => $group]);
    }

    public function store(Request $request, Group $group): RedirectResponse
    {
        $this->authorize('view', $group);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $group->shoppingLists()->create([
            'created_by' => $request->user()->id,
            'name' => $validated['name'],
        ]);

        return redirect()
            ->route('groups.shoppingLists.index', $group)
            ->with('success', 'Shopping list created successfully!');
    }

    public function show(Group $group, ShoppingList $shoppingList): View
    {
        $this->authorize('view', $shoppingList);

        return view('shoppingLists.show', ['group' => $group, 'shoppingList' => $shoppingList]);
    }

    public function edit(Group $group, ShoppingList $shoppingList): View
    {
        $this->authorize('update', $shoppingList);

        return view('shoppingLists.edit', ['group' => $group, 'shoppingList' => $shoppingList]);
    }

    public function update(Request $request, Group $group, ShoppingList $shoppingList): RedirectResponse
    {
        $this->authorize('update', $shoppingList);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $shoppingList->update($validated);

        return redirect()
            ->route('groups.shoppingLists.show', [$group, $shoppingList])
            ->with('success', 'Shopping list updated successfully!');
    }

    public function destroy(Group $group, ShoppingList $shoppingList): RedirectResponse
    {
        $this->authorize('delete', $shoppingList);

        $shoppingList->delete();

        return redirect()
            ->route('groups.shoppingLists.index', $group)
            ->with('success', 'Shopping list deleted successfully!');
    }

    public function addItem(Request $request, Group $group, ShoppingList $shoppingList): RedirectResponse
    {
        $this->authorize('update', $shoppingList);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $shoppingList->items()->create($validated);

        return $this->redirectToShow($request, $group, $shoppingList, 'Item added successfully!');
    }

    public function toggleItem(Request $request, Group $group, ShoppingList $shoppingList, ShoppingListItem $item): RedirectResponse
    {
        $this->authorize('update', $shoppingList);

        $item->update(['is_completed' => ! $item->is_completed]);

        return $this->redirectToShow($request, $group, $shoppingList);
    }

    public function deleteItem(Request $request, Group $group, ShoppingList $shoppingList, ShoppingListItem $item): RedirectResponse
    {
        $this->authorize('update', $shoppingList);

        $item->delete();

        return $this->redirectToShow($request, $group, $shoppingList, 'Item deleted successfully!');
    }

    private function redirectToShow(Request $request, Group $group, ShoppingList $shoppingList, ?string $message = null): RedirectResponse
    {
        $route = route('groups.shoppingLists.show', [$group, $shoppingList]);

        if ($request->boolean('showCompleted')) {
            $route .= '?showCompleted=true';
        }

        $redirect = redirect()->to($route);

        return $message ? $redirect->with('success', $message) : $redirect;
    }
}
