<?php

namespace App\Http\Controllers;

use App\Models\Group;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class TransactionController extends Controller
{
    public function create(Group $group)
    {
        return view('transactions.create', compact('group'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'group_id' => 'required|exists:groups,id',
            'payer_id' => 'required|exists:users,id',
            'recipient_id' => 'required|exists:users,id',
            'amount' => 'required|numeric|min:0.01',
            'description' => 'nullable|string|max:255',
        ]);

        $group = Group::with('users')->find($validated['group_id']);

        // Check if the authenticated user is part of the group
        if (!$group->users->contains(auth()->user())) {
            return redirect()->back()->withErrors(['error' => 'You are not a member of this group.']);
        }

        if (!$group->users->contains($validated['recipient_id'])) {
            return redirect()->back()->withErrors([
                'error' => 'The recipient is not part of the group!',
            ]);
        }

        Transaction::create($validated);

        return redirect()
            ->route('groups.show', $validated['group_id'])
            ->with('success', 'Transaction added successfully!');
    }

    public function destroy(Transaction $transaction)
    {
        Gate::authorize('delete', $transaction);
        $transaction->delete();
        return back()->with('success', 'Transaction deleted successfully!');
    }
}
