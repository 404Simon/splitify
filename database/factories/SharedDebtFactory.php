<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Group;
use App\Models\RecurringSharedDebt;
use App\Models\SharedDebt;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\SharedDebt>
 */
final class SharedDebtFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'group_id' => Group::factory(),
            'created_by' => User::factory(),
            'name' => fake()->randomElement([
                'Dinner at Restaurant',
                'Grocery Shopping',
                'Gas Bill',
                'Movie Tickets',
                'Coffee Run',
                'Taxi Ride',
                'Pizza Night',
                'Concert Tickets',
                'Hotel Stay',
                'Uber Ride',
            ]),
            'amount' => fake()->randomFloat(2, 5, 200),
            'recurring_shared_debt_id' => null,
        ];
    }

    /**
     * Indicate that the shared debt should belong to a specific group.
     */
    public function forGroup(Group $group): static
    {
        return $this->state(fn (array $attributes): array => [
            'group_id' => $group->id,
        ]);
    }

    /**
     * Indicate that the shared debt should be created by a specific user.
     */
    public function createdBy(User $user): static
    {
        return $this->state(fn (array $attributes): array => [
            'created_by' => $user->id,
        ]);
    }

    /**
     * Indicate that the shared debt should be generated from a recurring debt.
     */
    public function fromRecurring(RecurringSharedDebt $recurringDebt): static
    {
        return $this->state(fn (array $attributes): array => [
            'group_id' => $recurringDebt->group_id,
            'created_by' => $recurringDebt->created_by,
            'name' => $recurringDebt->name,
            'amount' => $recurringDebt->amount,
            'recurring_shared_debt_id' => $recurringDebt->id,
        ]);
    }

    /**
     * Indicate that the shared debt should be a large expense.
     */
    public function largeExpense(): static
    {
        return $this->state(fn (array $attributes): array => [
            'amount' => fake()->randomFloat(2, 100, 1000),
            'name' => fake()->randomElement([
                'House Rent',
                'Vacation Trip',
                'Furniture Purchase',
                'Electronics',
                'Event Catering',
            ]),
        ]);
    }

    /**
     * Indicate that the shared debt should be a small expense.
     */
    public function smallExpense(): static
    {
        return $this->state(fn (array $attributes): array => [
            'amount' => fake()->randomFloat(2, 1, 25),
            'name' => fake()->randomElement([
                'Coffee',
                'Snacks',
                'Parking Fee',
                'Bus Ticket',
                'Tip',
            ]),
        ]);
    }

    /**
     * Indicate that the shared debt should have specific users.
     */
    public function withUsers(array $userIds): static
    {
        return $this->afterCreating(function (SharedDebt $debt) use ($userIds): void {
            $debt->users()->attach($userIds);
        });
    }

    /**
     * Indicate that the shared debt should split between all group members.
     */
    public function splitBetweenAllMembers(): static
    {
        return $this->afterCreating(function (SharedDebt $debt): void {
            $users = $debt->group->users;
            $debt->users()->attach($users->pluck('id'));
        });
    }

    /**
     * Indicate that the shared debt should not auto-attach users.
     */
    public function withoutUsers(): static
    {
        return $this;
    }
}
