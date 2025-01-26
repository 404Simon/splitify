<?php

namespace App\Http\Controllers;

use App\Models\Group;
use App\Models\SharedDebt;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class SharedDebtController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'group_id' => 'required|exists:groups,id',
            'name' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0.01',
            'members' => 'required|array',
            'members.*' => 'exists:users,id',
        ]);

        $group = Group::with('users')->find($validated['group_id']);

        // Check if the authenticated user is part of the group
        if (!$group->users->contains(auth()->user())) {
            return redirect()->back()->withErrors(['error' => 'You are not a member of this group.']);
        }

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
            'group_id' => $validated['group_id'],
            'created_by' => auth()->id(),
            'name' => $validated['name'],
            'amount' => $validated['amount'],
        ]);

        $sharedDebt->users()->attach($validated['members']);

        return redirect()
            ->route('groups.show', $validated['group_id'])
            ->with('success', 'Shared debt added successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function show(SharedDebt $sharedDebt)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(SharedDebt $sharedDebt)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, SharedDebt $sharedDebt)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(SharedDebt $sharedDebt)
    {
        Gate::authorize('delete', $sharedDebt);
        $sharedDebt->delete();

        return redirect()
            ->route('groups.show', $sharedDebt['group_id'])
            ->with('success', 'Shared debt added successfully!');
    }
}
