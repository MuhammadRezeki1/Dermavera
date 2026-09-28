<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SafetyAssessment extends Model
{
    protected $guarded = [];

    protected function casts(): array
    {
        return ['red_flag' => 'boolean', 'hard_constraint_codes' => 'array', 'explanation_codes' => 'array'];
    }
}
