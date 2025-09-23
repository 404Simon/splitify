<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

final class SharedDebt extends Model
{
    use HasFactory;

    protected $fillable = ['group_id', 'created_by', 'name', 'amount', 'recurring_shared_debt_id'];

    public function group(): BelongsTo
    {
        return $this->belongsTo(Group::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class);
    }

    public function recurringSharedDebt(): BelongsTo
    {
        return $this->belongsTo(RecurringSharedDebt::class);
    }

    public function getUserShares()
    {
        $users = $this->users;
        $sharePerUser = $this->amount / $users->count();

        return $users->map(fn ($user): array => [
            'user' => $user,
            'amount' => $sharePerUser,
        ]);
    }

    public function calculateIndividualDebts()
    {
        $sharePerUser = $this->amount / $this->users->count();

        return $this->users->mapWithKeys(fn ($user): array => [$user->id => $sharePerUser]);
    }

    protected function casts(): array
    {
        return [
            'amount' => 'float',
        ];
    }
}
