<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Signup extends Model
{
    protected $fillable = [
        'shift_id',
        'hockey_team_id',
        'name',
        'email',
        'phone',
    ];

    public function shift(): BelongsTo
    {
        return $this->belongsTo(Shift::class);
    }

    public function hockeyTeam(): BelongsTo
    {
        return $this->belongsTo(HockeyTeam::class);
    }
}