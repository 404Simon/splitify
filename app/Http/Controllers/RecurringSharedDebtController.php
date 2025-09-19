<?php

namespace App\Http\Controllers;

use App\Models\Group;
use App\Models\RecurringSharedDebt;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\View\View;

class RecurringSharedDebtController extends Controller
{
    public function index(Group $group): View
    {
        $this->authorize('view', $group);

        $recurringDebts = $group->recurringSharedDebts()
            ->latest()
            ->get();

        return view('recurringSharedDebts.index', compact('group', 'recurringDebts'));
    }

    public function create(Group $group): View
    {
        $this->authorize('view', $group);

        return view('recurringSharedDebts.create', compact('group'));
    }

    public function store(Request $request, Group $group): RedirectResponse
    {
        $this->authorize('view', $group);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0.01',
            'frequency' => 'required|in:daily,weekly,monthly,yearly',
            'start_date' => 'required|date|after_or_equal:today',
            'end_date' => 'nullable|date|after:start_date',
            'description' => 'nullable|string|max:1000',
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

        $recurringDebt = RecurringSharedDebt::query()->create([
            'group_id' => $group->id,
            'created_by' => $request->user()->id,
            'name' => $validated['name'],
            'amount' => $validated['amount'],
            'frequency' => $validated['frequency'],
            'start_date' => $validated['start_date'],
            'end_date' => $validated['end_date'],
            'next_generation_date' => $validated['start_date'],
            'description' => $validated['description'],
        ]);

        $recurringDebt->users()->attach($validated['members']);

        return redirect()
            ->route('groups.recurring-debts.index', $group)
            ->with('success', 'Recurring shared debt created successfully!');
    }

    public function show(Group $group, RecurringSharedDebt $recurringDebt): View
    {
        $this->authorize('view', $recurringDebt);

        return view('recurringSharedDebts.show', compact('group', 'recurringDebt'));
    }

    public function edit(Group $group, RecurringSharedDebt $recurringDebt): View
    {
        $this->authorize('update', $recurringDebt);

        return view('recurringSharedDebts.edit', compact('group', 'recurringDebt'));
    }

    public function update(Request $request, Group $group, RecurringSharedDebt $recurringDebt): RedirectResponse
    {
        $this->authorize('update', $recurringDebt);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0.01',
            'frequency' => 'required|in:daily,weekly,monthly,yearly',
            'end_date' => 'nullable|date|after:start_date',
            'description' => 'nullable|string|max:1000',
            'is_active' => 'boolean',
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

        $recurringDebt->update([
            'name' => $validated['name'],
            'amount' => $validated['amount'],
            'frequency' => $validated['frequency'],
            'end_date' => $validated['end_date'],
            'description' => $validated['description'],
            'is_active' => $validated['is_active'] ?? true,
        ]);

        $recurringDebt->users()->sync($validated['members']);

        return redirect()
            ->route('groups.recurring-debts.show', [$group, $recurringDebt])
            ->with('success', 'Recurring shared debt updated successfully!');
    }

    public function destroy(Group $group, RecurringSharedDebt $recurringDebt): RedirectResponse
    {
        $this->authorize('delete', $recurringDebt);

        $recurringDebt->delete();

        return redirect()
            ->route('groups.recurring-debts.index', $group)
            ->with('success', 'Recurring shared debt deleted successfully!');
    }

    public function toggleActive(Group $group, RecurringSharedDebt $recurringDebt): RedirectResponse
    {
        $this->authorize('update', $recurringDebt);

        $recurringDebt->update([
            'is_active' => ! $recurringDebt->is_active,
        ]);

        $status = $recurringDebt->is_active ? 'activated' : 'deactivated';

        return redirect()
            ->back()
            ->with('success', "Recurring shared debt {$status} successfully!");
    }

    public function generateNow(Group $group, RecurringSharedDebt $recurringDebt): RedirectResponse
    {
        $this->authorize('update', $recurringDebt);

        if (! $recurringDebt->is_active) {
            return redirect()
                ->back()
                ->withErrors(['recurring_debt' => 'Cannot generate debt from inactive recurring debt.']);
        }

        $recurringDebt->generateSharedDebt();

        return redirect()
            ->route('groups.show', $group)
            ->with('success', 'Shared debt generated successfully!');
    }

    private function validateGroupMembers(array $memberIds, Group $group): Collection
    {
        return collect($memberIds)->filter(
            fn ($memberId) => ! $group->users->contains('id', $memberId)
        );
    }
}
