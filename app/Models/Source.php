<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\ValidationException;

class Source extends Model
{
    protected $guarded = [];

    protected function casts(): array
    {
        return ['accessed_at' => 'datetime', 'published_at' => 'date'];
    }

    protected static function booted(): void
    {
        static::saving(function (Source $source) {
            if (! str_starts_with((string) $source->url, 'https://')) {
                throw ValidationException::withMessages(['url' => 'URL sumber wajib memakai HTTPS.']);
            }
        });
    }
}
