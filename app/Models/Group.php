<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Group extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'created_by'];

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class);
    }

    public function sharedDebts(): HasMany
    {
        return $this->hasMany(SharedDebt::class);
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }

    public function invites(): HasMany
    {
        return $this->hasMany(Invite::class);
    }

    public function recurringSharedDebts(): HasMany
    {
        return $this->hasMany(RecurringSharedDebt::class);
    }

    public function mapMarkers(): HasMany
    {
        return $this->hasMany(MapMarker::class);
    }



    public function calculateUserDebts()
    {
        $debts = [];

        // Calculate debts from shared debts
        foreach ($this->sharedDebts as $debt) {
            $creatorId = $debt->created_by;
            $individualDebts = $debt->calculateIndividualDebts();

            foreach ($individualDebts as $userId => $amount) {
                if ($userId == $creatorId) {
                    continue;
                }

                if (! isset($debts[$userId][$creatorId])) {
                    $debts[$userId][$creatorId] = 0;
                }
                $debts[$userId][$creatorId] += $amount;

                if (! isset($debts[$creatorId][$userId])) {
                    $debts[$creatorId][$userId] = 0;
                }
                $debts[$creatorId][$userId] -= $amount;
            }
        }

        // Factor in direct transactions
        foreach ($this->transactions as $transaction) {
            $payerId = $transaction->payer_id;
            $recipientId = $transaction->recipient_id;
            $amount = $transaction->amount;

            if (! isset($debts[$recipientId][$payerId])) {
                $debts[$recipientId][$payerId] = 0;
            }
            $debts[$recipientId][$payerId] += $amount;  // Recipient effectively owes less to payer

            if (! isset($debts[$payerId][$recipientId])) {
                $debts[$payerId][$recipientId] = 0;
            }
            $debts[$payerId][$recipientId] -= $amount;  // Payer owes less to recipient
        }

        return $debts;
    }
}
