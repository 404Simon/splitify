<?php

namespace App\Http\Controllers;

use App\Models\Group;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class TransactionController extends Controller
{
    public function index(Group $group)
    {
        $transactions = $group->transactions()->with(['payer', 'recipient'])->orderBy('created_at', 'desc')->get();

        return view('transactions.index', compact('group', 'transactions'));
    }

    public function create(Group $group)
    {
        return view('transactions.create', compact('group'));
    }

    public function store(Request $request, Group $group)
    {
        $validated = $request->validate([
            'recipient_id' => 'required|exists:users,id',
            'amount' => 'required|numeric|min:0.01',
            'description' => 'nullable|string|max:255',
        ]);

        if (! $group->users->contains($validated['recipient_id'])) {
            return redirect()->back()->withErrors([
                'error' => 'The recipient is not part of the group!',
            ]);
        }

        $validated['group_id'] = $group->id;
        $validated['payer_id'] = auth()->id();

        Transaction::create($validated);

        return redirect()
            ->route('groups.show', $validated['group_id'])
            ->with('success', 'Transaction added successfully!');
    }

    public function edit(Group $group, Transaction $transaction)
    {
        Gate::authorize('update', $transaction);

        return view('transactions.edit', compact('group', 'transaction'));
    }

    public function update(Request $request, Group $group, Transaction $transaction)
    {
        Gate::authorize('update', $transaction);

        $validated = $request->validate([
            'recipient_id' => 'required|exists:users,id',
            'amount' => 'required|numeric|min:0.01',
            'description' => 'nullable|string|max:255',
        ]);

        if (! $group->users->contains($validated['recipient_id'])) {
            return redirect()->back()->withErrors([
                'error' => 'The recipient is not part of the group!',
            ]);
        }

        $transaction->update($validated);

        return redirect()
            ->route('groups.show', $group->id)
            ->with('success', 'Transaction updated successfully!');
    }

    public function destroy(Group $group, Transaction $transaction)
    {
        Gate::authorize('delete', $transaction);
        $transaction->delete();

        return back()->with('success', 'Transaction deleted successfully!');
    }
}
