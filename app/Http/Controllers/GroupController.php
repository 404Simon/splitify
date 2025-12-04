<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Group;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\View\View;

final class GroupController extends Controller
{
    public function index(Request $request): View
    {
        $groups = $request->user()->groups()->latest()->get();

        return view('groups.index', ['groups' => $groups]);
    }

    public function create(Request $request): View
    {
        $users = User::query()->where('id', '!=', $request->user()->id)->get();

        return view('groups.create', ['users' => $users]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $group = Group::query()->create([
            'name' => $validated['name'],
            'created_by' => $request->user()->id,
        ]);

        $group->users()->attach($request->user()->id);

        return redirect()
            ->route('groups.index')
            ->with('success', 'Group created successfully!');
    }

    public function show(Group $group): View
    {
        $this->authorize('view', $group);

        $userDebts = collect($group->calculateUserDebts())
            ->mapWithKeys(fn ($debts, $userId): array => [
                (string) $userId => collect($debts)->mapWithKeys(fn ($amount, $otherUserId): array => [
                    (string) $otherUserId => $amount,
                ]),
            ]);

        $userBalances = $this->calculateUserBalances($group, $userDebts);

        $recentSharedDebts = $this->getRecentSharedDebts($group);
        $recentTransactions = $this->getRecentTransactions($group);
        $activeRecurringDebts = $this->getActiveRecurringDebts($group);

        return view('groups.show', ['group' => $group, 'userDebts' => $userDebts, 'userBalances' => $userBalances, 'recentSharedDebts' => $recentSharedDebts, 'recentTransactions' => $recentTransactions, 'activeRecurringDebts' => $activeRecurringDebts]);
    }

    public function edit(Group $group): View
    {
        $this->authorize('update', $group);

        $selectedUsers = $group->users;

        return view('groups.edit', ['group' => $group, 'selectedUsers' => $selectedUsers]);
    }

    public function update(Request $request, Group $group): RedirectResponse
    {
        $this->authorize('update', $group);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'members' => 'required|array|exists:users,id',
        ]);

        $memberIds = collect($validated['members']);

        if (! $memberIds->contains(auth()->id())) {
            return redirect()
                ->back()
                ->withErrors(['members' => 'You cannot remove yourself from the group!']);
        }

        $group->update(['name' => $validated['name']]);
        $group->users()->sync($validated['members']);

        return redirect()
            ->route('groups.index')
            ->with('success', 'Group updated successfully!');
    }

    public function destroy(Group $group): RedirectResponse
    {
        $this->authorize('delete', $group);

        $groupName = $group->name;
        $group->delete();

        return redirect()
            ->route('groups.index')
            ->with('success', sprintf("Group '%s' and all associated data have been deleted successfully!", $groupName));
    }

    private function calculateUserBalances(Group $group, Collection $userDebts): Collection
    {
        return $group->users->mapWithKeys(function ($user) use ($group, $userDebts): array {
            $userDebtsForUser = $userDebts->get((string) $user->id, collect());

            [$relationships, $totals] = $this->processUserRelationships($userDebtsForUser, $group);

            $netAmount = $totals['owed'] - $totals['owing'];

            return [
                (string) $user->id => [
                    'user' => $user,
                    'relationships' => $relationships,
                    'total_owed' => number_format($totals['owed'], 2),
                    'total_owing' => number_format($totals['owing'], 2),
                    'net_amount' => number_format(abs($netAmount), 2),
                    'net_type' => $this->determineNetType($netAmount),
                ],
            ];
        });
    }

    private function processUserRelationships(Collection $userDebts, Group $group): array
    {
        $relationships = collect();
        $totals = ['owed' => 0, 'owing' => 0];

        $userDebts->each(function ($amount, $otherUserId) use (&$relationships, &$totals, $group): void {
            $otherUser = $group->users->firstWhere('id', $otherUserId);
            $formattedAmount = number_format(abs($amount), 2);

            if ($amount > 0) {
                $relationships->push([
                    'type' => 'owes',
                    'user' => $otherUser,
                    'amount' => $formattedAmount,
                    'raw_amount' => $amount,
                ]);
                $totals['owing'] += $amount;
            } elseif ($amount < 0) {
                $relationships->push([
                    'type' => 'owed',
                    'user' => $otherUser,
                    'amount' => $formattedAmount,
                    'raw_amount' => abs($amount),
                ]);
                $totals['owed'] += abs($amount);
            }
        });

        return [$relationships->all(), $totals];
    }

    private function determineNetType(float $netAmount): string
    {
        return match (true) {
            $netAmount > 0 => 'positive',
            $netAmount < 0 => 'negative',
            default => 'neutral'
        };
    }

    private function getRecentSharedDebts(Group $group): Collection
    {
        return $group->sharedDebts()
            ->latest()
            ->take(3)
            ->get()
            ->map(fn ($debt): array => [
                'debt' => $debt,
                'shares' => $debt->getUserShares(),
                'can_edit' => $debt->created_by === auth()->id(),
            ]);
    }

    private function getRecentTransactions(Group $group): Collection
    {
        return $group->transactions()
            ->latest()
            ->take(3)
            ->get()
            ->map(fn ($transaction): array => [
                'transaction' => $transaction,
                'can_edit' => $transaction->payer_id === auth()->id(),
            ]);
    }

    private function getActiveRecurringDebts(Group $group): Collection
    {
        return $group->recurringSharedDebts()
            ->where('is_active', true)
            ->take(3)
            ->get();
    }
}
