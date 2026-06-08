<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    protected $fillable = [
        'name',
        'description',
        'category_id'
    ];

    /**
    * Een product behoort tot een categorie
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Een product heeft meerdere varianten (maten)
     */
    public function variants(): HasMany
    {
        return $this->hasMany(ProductVariant::class);
    }
}
