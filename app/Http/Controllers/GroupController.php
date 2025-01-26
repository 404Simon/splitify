<?php

namespace App\Http\Controllers;

use App\Models\Group;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class GroupController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        // Get the authenticated user
        $user = auth()->user();

        // Retrieve all groups the user belongs to
        $groups = $user->groups()->with('users')->get();

        // Pass the groups to the view
        return view('groups.index', compact('groups'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $users = User::where('id', '!=', auth()->id())->get();
        return view('groups.create', compact('users'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'members' => 'required|array',  // Array of user IDs
            'members.*' => 'exists:users,id',  // Ensure each ID exists in the users table
        ]);

        $group = Group::create([
            'name' => $validated['name'],
            'created_by' => auth()->id(),
        ]);

        $group->users()->attach(auth()->id());

        $group->users()->attach($validated['members']);

        return redirect()->route('groups.index')->with('success', 'Group created successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Group $group)
    {
        Gate::authorize('view', $group);
        $group = Group::with(['users', 'sharedDebts', 'transactions' => function ($query) {
            $query->with(['payer', 'recipient']);  // Eager load payer and recipient
        }])->findOrFail($group->id);
        $userDebts = $group->calculateUserDebts();
        return view('groups.show', compact('group', 'userDebts'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Group $group)
    {
        Gate::authorize('update', $group);
        $users = User::all();

        return view('groups.edit', compact('group', 'users'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Group $group)
    {
        Gate::authorize('update', $group);
        $request->validate([
            'name' => 'required|string|max:32',
            'members' => 'array|exists:users,id',
        ]);

        $group->name = $request->input('name');
        $group->users()->sync($request->input('members'));  // Sync the group members (remove and add as necessary)

        $group->save();

        return redirect()->route('groups.index')->with('success', 'Group updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Group $group)
    {
        Gate::authorize('delete', $group);
        $group->delete();
        return redirect()->route('groups.index')->with('success', 'Group deleted successfully!');
    }
}
