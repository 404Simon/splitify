<?php

namespace App\Http\Controllers;

use App\Models\Group;
use App\Models\Invite;
use Illuminate\Http\Request;
use Illuminate\View\View;

class InviteController extends Controller
{
    public function index(Request $request, Group $group): View
    {
        $invites = $group->invites();
        return view('invites.index', compact('group'));
    }

    public function create(Group $group)
    {
        return view('invites.create', compact('group'));
    }

    public function store(Request $request, Group $group)
    {
        $validated = $request->validate([
            'duration_days' => 'required|int|min:1|max:30',
            'is_reusable' => 'boolean',
            'name' => 'nullable|string|max:128',
        ]);

        $validated['group_id'] = $group->id;
        if (!array_key_exists('is_reusable', $validated)) {
            $validated['is_reusable'] = false;
        }

        $invite = Invite::create($validated);

        return redirect()->route('groups.invites.index', compact('group'))->with('success', 'Invite created successfully!');
    }

    public function show(Request $request, Invite $invite)
    {
        //
    }

    public function destroy(Group $group, Invite $invite)
    {
        $invite->delete();
        return redirect()->route('groups.invites.index', compact('group'))->with('success', 'Invite deleted successfully!');
    }
}
