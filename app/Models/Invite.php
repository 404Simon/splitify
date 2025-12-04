<?php

declare(strict_types=1);

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

final class Invite extends Model
{
    use HasFactory;

    public $incrementing = false;

    protected $primaryKey = 'uuid';

    protected $fillable = [
        'group_id',
        'name',
        'is_reusable',
        'duration_days',
    ];

    protected $casts = [
        'is_reusable' => 'boolean',
    ];

    public function group()
    {
        return $this->belongsTo(Group::class);
    }

    /**
     * Check if the invite is still valid.
     */
    public function isValid(): bool
    {
        if ($this->duration_days <= 0) {
            return false;
        }

        $expirationDate = $this->created_at->addDays($this->duration_days);

        return Carbon::now()->isBefore($expirationDate);
    }

    protected static function booted(): void
    {
        self::creating(function (Model $model): void {
            if (empty($model->uuid)) {
                $model->uuid = Str::uuid()->toString();
            }

            if (is_null($model->is_reusable)) {
                $model->is_reusable = false;
            }

            if (is_null($model->duration_days) || $model->duration_days < 1) {
                $model->duration_days = 1;
            }
        });
    }
}
