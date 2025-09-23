<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Group;
use App\Models\Transaction;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

final class TransactionController extends Controller
{
    public function index(Group $group): View
    {
        $this->authorize('view', $group);

        $transactions = $group->transactions()
            ->latest()
            ->get();

        return view('transactions.index', ['group' => $group, 'transactions' => $transactions]);
    }

    public function create(Group $group): View
    {
        $this->authorize('view', $group);

        $otherMembers = $group->users->where('id', '!=', auth()->id());
        $preselectedRecipient = $otherMembers->count() === 1 ? $otherMembers->first()->id : null;

        return view('transactions.create', ['group' => $group, 'preselectedRecipient' => $preselectedRecipient]);
    }

    public function store(Request $request, Group $group): RedirectResponse
    {
        $this->authorize('view', $group);

        $validated = $request->validate([
            'recipient_id' => 'required|exists:users,id',
            'amount' => 'required|numeric|min:0.01',
            'description' => 'nullable|string|max:255',
        ]);

        if (! $group->users()->where('user_id', $validated['recipient_id'])->exists()) {
            return redirect()
                ->back()
                ->withErrors(['recipient_id' => 'The selected recipient is not part of this group.'])
                ->withInput();
        }

        Transaction::query()->create([
            ...$validated,
            'group_id' => $group->id,
            'payer_id' => $request->user()->id,
        ]);

        return redirect()
            ->route('groups.show', $group->id)
            ->with('success', 'Transaction added successfully!');
    }

    public function edit(Group $group, Transaction $transaction): View
    {
        $this->authorize('update', $transaction);

        $otherMembers = $group->users->where('id', '!=', auth()->id());
        $preselectedRecipient = $otherMembers->count() === 1 ? $otherMembers->first()->id : null;

        return view('transactions.edit', ['group' => $group, 'transaction' => $transaction, 'preselectedRecipient' => $preselectedRecipient]);
    }

    public function update(Request $request, Group $group, Transaction $transaction): RedirectResponse
    {
        $this->authorize('update', $transaction);

        $validated = $request->validate([
            'recipient_id' => 'required|exists:users,id',
            'amount' => 'required|numeric|min:0.01',
            'description' => 'nullable|string|max:255',
        ]);

        if (! $group->users()->where('user_id', $validated['recipient_id'])->exists()) {
            return redirect()
                ->back()
                ->withErrors(['recipient_id' => 'The selected recipient is not part of this group.'])
                ->withInput();
        }

        $transaction->update($validated);

        return redirect()
            ->route('groups.show', $group->id)
            ->with('success', 'Transaction updated successfully!');
    }

    public function destroy(Group $group, Transaction $transaction): RedirectResponse
    {
        $this->authorize('delete', $transaction);

        $transaction->delete();

        return redirect()
            ->back()
            ->with('success', 'Transaction deleted successfully!');
    }
}
