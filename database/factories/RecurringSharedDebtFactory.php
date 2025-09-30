<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Group;
use App\Models\RecurringSharedDebt;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\RecurringSharedDebt>
 */
final class RecurringSharedDebtFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $startDate = fake()->dateTimeBetween('-1 month', '+1 week');

        return [
            'group_id' => Group::factory(),
            'created_by' => User::factory(),
            'name' => fake()->randomElement([
                'Monthly Rent',
                'Weekly Groceries',
                'Internet Bill',
                'Electricity Bill',
                'Cleaning Service',
                'Netflix Subscription',
                'Gym Membership',
            ]),
            'amount' => fake()->randomFloat(2, 10, 500),
            'frequency' => fake()->randomElement(['daily', 'weekly', 'monthly', 'yearly']),
            'start_date' => $startDate,
            'end_date' => fake()->optional(0.3)->dateTimeBetween($startDate, '+1 year'),
            'next_generation_date' => $startDate,
            'is_active' => fake()->boolean(90), // 90% chance of being active
            'description' => fake()->optional()->sentence(),
        ];
    }

    /**
     * Indicate that the recurring debt should belong to a specific group.
     */
    public function forGroup(Group $group): static
    {
        return $this->state(fn (array $attributes): array => [
            'group_id' => $group->id,
        ]);
    }

    /**
     * Indicate that the recurring debt should be created by a specific user.
     */
    public function createdBy(User $user): static
    {
        return $this->state(fn (array $attributes): array => [
            'created_by' => $user->id,
        ]);
    }

    /**
     * Indicate that the recurring debt should be active.
     */
    public function active(): static
    {
        return $this->state(fn (array $attributes): array => [
            'is_active' => true,
        ]);
    }

    /**
     * Indicate that the recurring debt should be inactive.
     */
    public function inactive(): static
    {
        return $this->state(fn (array $attributes): array => [
            'is_active' => false,
        ]);
    }

    /**
     * Indicate that the recurring debt should be monthly.
     */
    public function monthly(): static
    {
        return $this->state(fn (array $attributes): array => [
            'frequency' => 'monthly',
        ]);
    }

    /**
     * Indicate that the recurring debt should be weekly.
     */
    public function weekly(): static
    {
        return $this->state(fn (array $attributes): array => [
            'frequency' => 'weekly',
        ]);
    }

    /**
     * Indicate that the recurring debt should be daily.
     */
    public function daily(): static
    {
        return $this->state(fn (array $attributes): array => [
            'frequency' => 'daily',
        ]);
    }

    /**
     * Indicate that the recurring debt should be yearly.
     */
    public function yearly(): static
    {
        return $this->state(fn (array $attributes): array => [
            'frequency' => 'yearly',
        ]);
    }

    /**
     * Indicate that the recurring debt should be ready for generation.
     */
    public function readyForGeneration(): static
    {
        return $this->state(fn (array $attributes): array => [
            'is_active' => true,
            'next_generation_date' => now()->subDay(),
        ]);
    }

    /**
     * Indicate that the recurring debt should be expired.
     */
    public function expired(): static
    {
        return $this->state(fn (array $attributes): array => [
            'end_date' => now()->subDay(),
        ]);
    }

    /**
     * Configure the model factory with users.
     */
    public function configure(): static
    {
        return $this;
    }

    /**
     * Indicate that the recurring debt should have specific users.
     */
    public function withUsers(array $userIds): static
    {
        return $this->afterCreating(function (RecurringSharedDebt $debt) use ($userIds): void {
            $debt->users()->attach($userIds);
        });
    }

    /**
     * Auto-attach random users from the group.
     */
    public function withRandomUsers(): static
    {
        return $this->afterCreating(function (RecurringSharedDebt $debt): void {
            // Attach some users to the recurring debt
            $users = $debt->group->users()->inRandomOrder()->limit(fake()->numberBetween(2, 4))->get();
            $debt->users()->attach($users->pluck('id'));
        });
    }
}
