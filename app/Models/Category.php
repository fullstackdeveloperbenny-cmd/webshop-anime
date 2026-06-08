<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $fillable = ['name'];

    // Deze relatie is nodig voor de beveiliging
    public function products()
    {
        return $this->hasMany(Product::class);
    }
}
