<?php

namespace App\Http\Controllers;

use App\Models\Group;
use App\Models\SharedDebt;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class SharedDebtController extends Controller
{
    public function create(Group $group)
    {
        return view('sharedDebts.create', compact('group'));
    }

    public function store(Request $request, Group $group)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0.01',
            'members' => 'required|array',
            'members.*' => 'exists:users,id',
        ]);

        // Check if all specified members are in the group
        $invalidMembers = collect($validated['members'])->filter(function ($memberId) use ($group) {
            return !$group->users->contains('id', $memberId);
        });

        if ($invalidMembers->isNotEmpty()) {
            return redirect()->back()->withErrors([
                'error' => 'Some members are not part of this group: ' . implode(', ', $invalidMembers->toArray()),
            ]);
        }

        $sharedDebt = SharedDebt::create([
            'group_id' => $group->id,
            'created_by' => auth()->id(),
            'name' => $validated['name'],
            'amount' => $validated['amount'],
        ]);

        $sharedDebt->users()->attach($validated['members']);

        return redirect()
            ->route('groups.show', $group->id)
            ->with('success', 'Shared debt added successfully!');
    }

    public function edit(Group $group, SharedDebt $sharedDebt)
    {
        return view('sharedDebts.edit', compact('group', 'sharedDebt'));
    }

    public function update(Request $request, Group $group, SharedDebt $sharedDebt)
    {
        Gate::authorize('update', $sharedDebt);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0.01',
            'members' => 'required|array',
            'members.*' => 'exists:users,id',
        ]);

        // Check if all specified members are in the group
        $invalidMembers = collect($validated['members'])->filter(function ($memberId) use ($group) {
            return !$group->users->contains('id', $memberId);
        });

        if ($invalidMembers->isNotEmpty()) {
            return redirect()->back()->withErrors([
                'error' => 'Some members are not part of this group: ' . implode(', ', $invalidMembers->toArray()),
            ])->withInput();  // Keep the input values
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

    public function destroy(Group $group, SharedDebt $sharedDebt)
    {
        Gate::authorize('delete', $sharedDebt);
        $sharedDebt->delete();

        return redirect()
            ->route('groups.show', $sharedDebt['group_id'])
            ->with('success', 'Shared debt deleted successfully!');
    }
}
