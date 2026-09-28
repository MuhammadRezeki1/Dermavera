<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $version
 * @property string $status
 * @property string $hash
 * @property array<string, mixed> $snapshot
 */
class DatasetVersion extends Model
{
    protected $guarded = [];

    protected function casts(): array
    {
        return ['snapshot' => 'array', 'activated_at' => 'datetime'];
    }
}
