<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Shift extends Model
{
    protected $fillable = [
        'title',
        'description',
        'date',
        'starts_at',
        'ends_at',
        'location',
        'capacity',
        'is_published',
    ];

    protected function casts(): array
    {
        return [
            'date' => 'date',
            'is_published' => 'boolean',
        ];
    }

    public function signups(): HasMany
    {
        return $this->hasMany(Signup::class);
    }
}