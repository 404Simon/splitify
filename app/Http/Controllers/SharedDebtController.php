<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Group;
use App\Models\SharedDebt;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\View\View;

final class SharedDebtController extends Controller
{
    public function index(Group $group): View
    {
        $this->authorize('view', $group);

        $sharedDebts = $group->sharedDebts()
            ->latest()
            ->get();

        return view('sharedDebts.index', ['group' => $group, 'sharedDebts' => $sharedDebts]);
    }

    public function create(Group $group): View
    {
        $this->authorize('view', $group);

        return view('sharedDebts.create', ['group' => $group]);
    }

    public function store(Request $request, Group $group): RedirectResponse
    {
        $this->authorize('view', $group);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0.01',
            'members' => 'required|array',
            'members.*' => 'exists:users,id',
        ]);

        $invalidMembers = $this->validateGroupMembers($validated['members'], $group);

        if ($invalidMembers->isNotEmpty()) {
            return redirect()
                ->back()
                ->withErrors(['members' => 'Some selected members are not part of this group.'])
                ->withInput();
        }

        $sharedDebt = SharedDebt::query()->create([
            'group_id' => $group->id,
            'created_by' => $request->user()->id,
            'name' => $validated['name'],
            'amount' => $validated['amount'],
        ]);

        $sharedDebt->users()->attach($validated['members']);

        return redirect()
            ->route('groups.show', $group->id)
            ->with('success', 'Shared debt added successfully!');
    }

    public function edit(Group $group, SharedDebt $sharedDebt): View
    {
        $this->authorize('update', $sharedDebt);

        return view('sharedDebts.edit', ['group' => $group, 'sharedDebt' => $sharedDebt]);
    }

    public function update(Request $request, Group $group, SharedDebt $sharedDebt): RedirectResponse
    {
        $this->authorize('update', $sharedDebt);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0.01',
            'members' => 'required|array',
            'members.*' => 'exists:users,id',
        ]);

        $invalidMembers = $this->validateGroupMembers($validated['members'], $group);

        if ($invalidMembers->isNotEmpty()) {
            return redirect()
                ->back()
                ->withErrors(['members' => 'Some selected members are not part of this group.'])
                ->withInput();
        }

        $sharedDebt->update([
            'name' => $validated['name'],
            'amount' => $validated['amount'],
        ]);

        $sharedDebt->users()->sync($validated['members']);

        return redirect()
            ->route('groups.show', $group->id)
            ->with('success', 'Shared debt updated successfully!');
    }

    public function destroy(Group $group, SharedDebt $sharedDebt): RedirectResponse
    {
        $this->authorize('delete', $sharedDebt);

        $sharedDebt->delete();

        return redirect()
            ->route('groups.show', $group->id)
            ->with('success', 'Shared debt deleted successfully!');
    }

    private function validateGroupMembers(array $memberIds, Group $group): Collection
    {
        return collect($memberIds)->filter(
            fn ($memberId): bool => ! $group->users->contains('id', $memberId)
        );
    }
}
