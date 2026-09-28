<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FormulaIngredient extends Model
{
    protected $guarded = [];

    /** @return BelongsTo<FormulaVersion, $this> */
    public function formula(): BelongsTo
    {
        return $this->belongsTo(FormulaVersion::class, 'formula_version_id');
    }

    /** @return BelongsTo<Ingredient, $this> */
    public function ingredient(): BelongsTo
    {
        return $this->belongsTo(Ingredient::class);
    }
}
