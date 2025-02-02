<?php

namespace App\Http\Controllers;

use App\Models\Group;
use App\Models\Invite;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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

    private function checkInvite(Invite $invite)
    {
        if (!$invite->isValid()) {
            return redirect()->back()->with('error', 'The invite is not valid or has expired.');
        }

        $group = $invite->group;
        if (!$group) {
            return redirect()->back()->with('error', 'The group associated with this invite was not found.');
        }

        $user = auth()->user();
        if ($group->users()->where('user_id', $user->id)->exists()) {
            return redirect()->back()->with('error', 'You are already a member of this group.');
        }
    }

    public function show(Request $request, string $uuid)
    {
        if ($uuid) {
            session(['invite_token' => $uuid]);
        }

        if (Auth::check()) {
            $invite = Invite::findOrFail($uuid);
            $this->checkInvite($invite);
            return view('invites.show', compact('invite'));
        }

        return redirect()->guest(route('login'))->with('message', 'You were invited to join a group in Splitify. Please log in or create an Account to accept it.');
    }

    public function accept(Request $request, Invite $invite)
    {
        if (!$invite->isValid()) {
            return redirect()->back()->with('error', 'The invite is not valid or has expired.');
        }

        $group = $invite->group;
        if (!$group) {
            return redirect()->back()->with('error', 'The group associated with this invite was not found.');
        }

        $user = auth()->user();
        if ($group->users()->where('user_id', $user->id)->exists()) {
            return redirect()->back()->with('error', 'You are already a member of this group.');
        }

        $group->users()->attach($user);

        if (!$invite->is_reusable) {
            $invite->delete();
        }

        return redirect()
            ->route('groups.show', ['group' => $group->id])
            ->with('success', 'You have successfully joined the group!');
    }

    public function deny(Request $request, Invite $invite)
    {
        if (!$invite->is_reusable) {
            $invite->delete();
        }

        return redirect()
            ->route('groups.index')
            ->with('error', 'You did not join the group.');
    }

    public function destroy(Group $group, Invite $invite)
    {
        $invite->delete();
        return redirect()->route('groups.invites.index', compact('group'))->with('success', 'Invite deleted successfully!');
    }
}
