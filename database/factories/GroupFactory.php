<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Group;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Group>
 */
final class GroupFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->words(2, true).' Group',
            'created_by' => User::factory(),
        ];
    }

    /**
     * Indicate that the group should have a specific creator.
     */
    public function createdBy(User $user): static
    {
        return $this->state(fn (array $attributes): array => [
            'created_by' => $user->id,
        ]);
    }

    /**
     * Configure the model factory with users.
     */
    public function configure(): static
    {
        return $this->afterCreating(function (Group $group): void {
            // Attach the creator to the group
            $group->users()->attach($group->created_by);
        });
    }

    /**
     * Indicate that the group should have additional users.
     */
    public function withUsers(int $count = 3): static
    {
        return $this->afterCreating(function (Group $group) use ($count): void {
            $users = User::factory()->count($count)->create();
            $group->users()->attach($users->pluck('id'));
        });
    }
}
