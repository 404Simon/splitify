<?php

namespace Database\Factories;

use App\Models\Group;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Transaction>
 */
class TransactionFactory extends Factory
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
            'payer_id' => User::factory(),
            'recipient_id' => User::factory(),
            'amount' => fake()->randomFloat(2, 1, 100),
            'description' => fake()->optional()->randomElement([
                'Payment for dinner',
                'Settling grocery bill',
                'Coffee money',
                'Movie ticket reimbursement',
                'Gas money',
                'Lunch payment',
                'Shared taxi fare',
                'Utilities settlement',
            ]),
        ];
    }

    /**
     * Indicate that the transaction should belong to a specific group.
     */
    public function forGroup(Group $group): static
    {
        return $this->state(fn (array $attributes) => [
            'group_id' => $group->id,
        ]);
    }

    /**
     * Indicate that the transaction should have a specific payer.
     */
    public function paidBy(User $user): static
    {
        return $this->state(fn (array $attributes) => [
            'payer_id' => $user->id,
        ]);
    }

    /**
     * Indicate that the transaction should have a specific recipient.
     */
    public function paidTo(User $user): static
    {
        return $this->state(fn (array $attributes) => [
            'recipient_id' => $user->id,
        ]);
    }

    /**
     * Indicate that the transaction should be between specific users.
     */
    public function between(User $payer, User $recipient): static
    {
        return $this->state(fn (array $attributes) => [
            'payer_id' => $payer->id,
            'recipient_id' => $recipient->id,
        ]);
    }

    /**
     * Indicate that the transaction should be a large amount.
     */
    public function largeAmount(): static
    {
        return $this->state(fn (array $attributes) => [
            'amount' => fake()->randomFloat(2, 50, 500),
        ]);
    }

    /**
     * Indicate that the transaction should be a small amount.
     */
    public function smallAmount(): static
    {
        return $this->state(fn (array $attributes) => [
            'amount' => fake()->randomFloat(2, 1, 20),
        ]);
    }

    /**
     * Configure the model factory to ensure users belong to the group.
     */
    public function configure(): static
    {
        return $this->afterMaking(function (Transaction $transaction) {
            // Ensure both payer and recipient exist and belong to the group
            if ($transaction->group_id && $transaction->payer_id && $transaction->recipient_id) {
                $group = $transaction->group;

                // Attach users to group if they're not already members
                if (! $group->users()->where('user_id', $transaction->payer_id)->exists()) {
                    $group->users()->attach($transaction->payer_id);
                }

                if (! $group->users()->where('user_id', $transaction->recipient_id)->exists()) {
                    $group->users()->attach($transaction->recipient_id);
                }
            }
        });
    }

    /**
     * Indicate that the transaction should be between existing group members.
     */
    public function betweenGroupMembers(): static
    {
        return $this->afterMaking(function (Transaction $transaction) {
            $groupUsers = $transaction->group->users()->inRandomOrder()->limit(2)->get();

            if ($groupUsers->count() >= 2) {
                $transaction->payer_id = $groupUsers[0]->id;
                $transaction->recipient_id = $groupUsers[1]->id;
            }
        });
    }
}
