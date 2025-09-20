<?php

namespace Database\Factories;

use App\Models\Group;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Group>
 */
class GroupFactory extends Factory
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
        return $this->state(fn (array $attributes) => [
            'created_by' => $user->id,
        ]);
    }

    /**
     * Configure the model factory with users.
     */
    public function configure(): static
    {
        return $this->afterCreating(function (Group $group) {
            // Attach the creator to the group
            $group->users()->attach($group->created_by);
        });
    }

    /**
     * Indicate that the group should have additional users.
     */
    public function withUsers(int $count = 3): static
    {
        return $this->afterCreating(function (Group $group) use ($count) {
            $users = User::factory()->count($count)->create();
            $group->users()->attach($users->pluck('id'));
        });
    }
}
