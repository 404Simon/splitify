<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Model;

class SharedDebt extends Model
{
    protected $fillable = ['group_id', 'created_by', 'name', 'amount'];

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

    public function getUserShares()
    {
        $users = $this->users;
        $sharePerUser = $this->amount / $users->count();

        return $users->map(function ($user) use ($sharePerUser) {
            return [
                'user' => $user,
                'amount' => number_format($sharePerUser, 2),
            ];
        });
    }

    public function calculateIndividualDebts()
    {
        $sharePerUser = $this->amount / $this->users->count();

        return $this->users->mapWithKeys(function ($user) use ($sharePerUser) {
            return [$user->id => $sharePerUser];
        });
    }
}
