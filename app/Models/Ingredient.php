<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ingredient extends Model
{
    protected $guarded = [];

    protected function casts(): array
    {
        return ['is_fragrance' => 'boolean', 'is_menthol' => 'boolean', 'is_physical_scrub' => 'boolean', 'is_exfoliant' => 'boolean'];
    }
}
