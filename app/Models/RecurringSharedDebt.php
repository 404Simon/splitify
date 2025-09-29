<?php

declare(strict_types=1);

namespace App\Models;

use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

final class RecurringSharedDebt extends Model
{
    use HasFactory;

    protected $fillable = [
        'group_id',
        'created_by',
        'name',
        'amount',
        'frequency',
        'start_date',
        'end_date',
        'next_generation_date',
        'is_active',
        'description',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'next_generation_date' => 'date',
        'is_active' => 'boolean',
        'amount' => 'decimal:2',
    ];

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

    public function generatedDebts(): HasMany
    {
        return $this->hasMany(SharedDebt::class, 'recurring_shared_debt_id');
    }

    public function calculateNextGenerationDate(): CarbonImmutable
    {
        $current = $this->next_generation_date;

        return match ($this->frequency) {
            'daily' => $current->addDay(),
            'weekly' => $current->addWeek(),
            'monthly' => $current->addMonth(),
            'yearly' => $current->addYear(),
        };
    }

    public function shouldGenerate(): bool
    {
        if (! $this->is_active) {
            return false;
        }

        if ($this->end_date && now()->gt($this->end_date)) {
            return false;
        }

        return now()->gte($this->next_generation_date);
    }

    public function generateSharedDebt(): SharedDebt
    {
        $sharedDebt = SharedDebt::query()->create([
            'group_id' => $this->group_id,
            'created_by' => $this->created_by,
            'name' => $this->name,
            'amount' => $this->amount,
            'recurring_shared_debt_id' => $this->id,
        ]);

        $sharedDebt->users()->attach($this->users->pluck('id'));

        $this->update([
            'next_generation_date' => $this->calculateNextGenerationDate(),
        ]);

        return $sharedDebt;
    }

    public function getUserShares()
    {
        $users = $this->users;
        $sharePerUser = $this->amount / $users->count();

        return $users->map(fn ($user): array => [
            'user' => $user,
            'amount' => number_format($sharePerUser, 2),
        ]);
    }

    public function getFrequencyLabelAttribute(): string
    {
        return match ($this->frequency) {
            'daily' => 'Daily',
            'weekly' => 'Weekly',
            'monthly' => 'Monthly',
            'yearly' => 'Yearly',
        };
    }

    public function getStatusAttribute(): string
    {
        if (! $this->is_active) {
            return 'Inactive';
        }

        if ($this->end_date && now()->gt($this->end_date)) {
            return 'Expired';
        }

        return 'Active';
    }
}
