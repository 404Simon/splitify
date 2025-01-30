<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Invite extends Model
{
    protected $primaryKey = 'uuid';
    public $incrementing = false;

    protected $fillable = [
        'group_id'
    ];

    protected static function booted(): void
    {
        static::creating(function (Model $model) {
            if (empty($model->uuid)) {
                $model->uuid = Str::uuid()->toString();
            }
        });
    }

    public function group()
    {
        return $this->belongsTo(Group::class);
    }
}
