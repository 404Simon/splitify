<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MapMarker extends Model
{
    protected $fillable = [
        'group_id',
        'created_by',
        'emoji',
        'name',
        'description',
        'address',
        'lat',
        'lon',
    ];

    public function group(): BelongsTo
    {
        return $this->belongsTo(Group::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
