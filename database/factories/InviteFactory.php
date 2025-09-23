<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Group;
use App\Models\Invite;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Invite>
 */
final class InviteFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'uuid' => Str::uuid()->toString(),
            'group_id' => Group::factory(),
            'name' => fake()->words(2, true).' Invite',
            'is_reusable' => fake()->boolean(30), // 30% chance of being reusable
            'duration_days' => fake()->randomElement([1, 3, 7, 14, 30]),
        ];
    }

    /**
     * Indicate that the invite should be reusable.
     */
    public function reusable(): static
    {
        return $this->state(fn (array $attributes): array => [
            'is_reusable' => true,
        ]);
    }

    /**
     * Indicate that the invite should be single-use.
     */
    public function singleUse(): static
    {
        return $this->state(fn (array $attributes): array => [
            'is_reusable' => false,
        ]);
    }

    /**
     * Indicate that the invite should be expired.
     */
    public function expired(): static
    {
        return $this->state(fn (array $attributes): array => [
            'duration_days' => 1,
            'created_at' => now()->subDays(2),
        ]);
    }

    /**
     * Indicate that the invite should be valid for a specific duration.
     */
    public function validFor(int $days): static
    {
        return $this->state(fn (array $attributes): array => [
            'duration_days' => $days,
        ]);
    }

    /**
     * Indicate that the invite should belong to a specific group.
     */
    public function forGroup(Group $group): static
    {
        return $this->state(fn (array $attributes): array => [
            'group_id' => $group->id,
        ]);
    }
}
