<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Group;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\MapMarker>
 */
final class MapMarkerFactory extends Factory
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
            'emoji' => fake()->randomElement(['🏠', '🏢', '🍕', '☕', '🎬', '🛒', '⛽', '🏥', '🎭', '🍽️']),
            'name' => fake()->company(),
            'description' => fake()->sentence(),
            'address' => fake()->address(),
            'lat' => fake()->latitude(),
            'lon' => fake()->longitude(),
        ];
    }

    /**
     * Indicate that the marker should belong to a specific group.
     */
    public function forGroup(Group $group): static
    {
        return $this->state(fn (array $attributes): array => [
            'group_id' => $group->id,
        ]);
    }

    /**
     * Indicate that the marker should be created by a specific user.
     */
    public function createdBy(User $user): static
    {
        return $this->state(fn (array $attributes): array => [
            'created_by' => $user->id,
        ]);
    }

    /**
     * Indicate that the marker should be a restaurant.
     */
    public function restaurant(): static
    {
        return $this->state(fn (array $attributes): array => [
            'emoji' => '🍽️',
            'name' => fake()->company().' Restaurant',
            'description' => 'A great place to eat',
        ]);
    }

    /**
     * Indicate that the marker should be a coffee shop.
     */
    public function coffeeShop(): static
    {
        return $this->state(fn (array $attributes): array => [
            'emoji' => '☕',
            'name' => fake()->company().' Coffee',
            'description' => 'Perfect coffee spot',
        ]);
    }

    /**
     * Indicate that the marker should be at a specific location.
     */
    public function atLocation(float $lat, float $lon): static
    {
        return $this->state(fn (array $attributes): array => [
            'lat' => $lat,
            'lon' => $lon,
        ]);
    }
}
