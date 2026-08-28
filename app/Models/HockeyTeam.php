<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class HockeyTeam extends Model
{
    protected $fillable = [
        'name',
        'manager_name',
        'manager_email',
        'required_volunteers',
        'active',
    ];

    protected function casts(): array
    {
        return [
            'required_volunteers' => 'integer',
            'active' => 'boolean',
        ];
    }

    public function signups(): HasMany
    {
        return $this->hasMany(Signup::class);
    }
}