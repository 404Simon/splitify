<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Group extends Model
{
    protected $fillable = ['name', 'created_by'];

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class);
    }

    public function sharedDebts(): HasMany
    {
        return $this->hasMany(SharedDebt::class);
    }

    public function calculateUserDebts()
    {
        $debts = [];

        foreach ($this->sharedDebts as $debt) {
            $creatorId = $debt->created_by;
            $individualDebts = $debt->calculateIndividualDebts();

            foreach ($individualDebts as $userId => $amount) {
                if ($userId == $creatorId) continue;

                if (!isset($debts[$userId][$creatorId])) {
                    $debts[$userId][$creatorId] = 0;
                }
                $debts[$userId][$creatorId] += $amount;

                if (!isset($debts[$creatorId][$userId])) {
                    $debts[$creatorId][$userId] = 0;
                }
                $debts[$creatorId][$userId] -= $amount;
            }
        }

        return $debts;
    }
}
