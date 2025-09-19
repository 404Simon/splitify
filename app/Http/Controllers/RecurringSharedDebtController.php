<?php

namespace App\Http\Controllers;

use App\Models\Group;
use App\Models\RecurringSharedDebt;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class RecurringSharedDebtController extends Controller
{
    public function index(Group $group)
    {
        $recurringDebts = $group->recurringSharedDebts()
            ->with(['creator', 'users'])
            ->orderBy('created_at', 'desc')
            ->get();

        return view('recurringSharedDebts.index', compact('group', 'recurringDebts'));
    }

    public function create(Group $group)
    {
        return view('recurringSharedDebts.create', compact('group'));
    }

    public function store(Request $request, Group $group)
    {
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

        $invalidMembers = collect($validated['members'])->filter(function ($memberId) use ($group) {
            return ! $group->users->contains('id', $memberId);
        });

        if ($invalidMembers->isNotEmpty()) {
            return redirect()->back()->withErrors([
                'error' => 'Some members are not part of this group.',
            ])->withInput();
        }

        $recurringDebt = RecurringSharedDebt::create([
            'group_id' => $group->id,
            'created_by' => auth()->id(),
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

    public function show(Group $group, RecurringSharedDebt $recurringDebt)
    {
        $recurringDebt->load(['creator', 'users', 'generatedDebts.users']);

        return view('recurringSharedDebts.show', compact('group', 'recurringDebt'));
    }

    public function edit(Group $group, RecurringSharedDebt $recurringDebt)
    {
        return view('recurringSharedDebts.edit', compact('group', 'recurringDebt'));
    }

    public function update(Request $request, Group $group, RecurringSharedDebt $recurringDebt)
    {
        Gate::authorize('update', $recurringDebt);

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

        $invalidMembers = collect($validated['members'])->filter(function ($memberId) use ($group) {
            return ! $group->users->contains('id', $memberId);
        });

        if ($invalidMembers->isNotEmpty()) {
            return redirect()->back()->withErrors([
                'error' => 'Some members are not part of this group.',
            ])->withInput();
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

    public function destroy(Group $group, RecurringSharedDebt $recurringDebt)
    {
        Gate::authorize('delete', $recurringDebt);

        $recurringDebt->delete();

        return redirect()
            ->route('groups.recurring-debts.index', $group)
            ->with('success', 'Recurring shared debt deleted successfully!');
    }

    public function toggleActive(Group $group, RecurringSharedDebt $recurringDebt)
    {
        Gate::authorize('update', $recurringDebt);

        $recurringDebt->update([
            'is_active' => ! $recurringDebt->is_active,
        ]);

        $status = $recurringDebt->is_active ? 'activated' : 'deactivated';

        return redirect()
            ->back()
            ->with('success', "Recurring shared debt {$status} successfully!");
    }

    public function generateNow(Group $group, RecurringSharedDebt $recurringDebt)
    {
        Gate::authorize('update', $recurringDebt);

        if (! $recurringDebt->is_active) {
            return redirect()
                ->back()
                ->with('error', 'Cannot generate debt from inactive recurring debt.');
        }

        $sharedDebt = $recurringDebt->generateSharedDebt();

        return redirect()
            ->route('groups.show', $group)
            ->with('success', 'Shared debt generated successfully!');
    }
}
