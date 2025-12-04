<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

final class ShoppingListItem extends Model
{
    use HasFactory;

    protected $fillable = ['shopping_list_id', 'name', 'is_completed'];

    public function shoppingList(): BelongsTo
    {
        return $this->belongsTo(ShoppingList::class);
    }

    protected function casts(): array
    {
        return [
            'is_completed' => 'boolean',
        ];
    }
}
