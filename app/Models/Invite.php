<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Invite extends Model
{
    protected $primaryKey = 'uuid';
    public $incrementing = false;

    protected $fillable = [
        'group_id',
        'name',
        'is_reusable',
        'duration_days'
    ];

    protected static function booted(): void
    {
        static::creating(function (Model $model) {
            if (empty($model->uuid)) {
                $model->uuid = Str::uuid()->toString();
            }
            if (empty($model->is_reusable)) {
                $model->is_reusable = false;
            }
            if (empty($model->duration_days)) {
                $model->duration_days = 1;
            }
        });
    }

    public function group()
    {
        return $this->belongsTo(Group::class);
    }
}
