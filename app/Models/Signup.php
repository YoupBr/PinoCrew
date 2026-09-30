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
        'reminder_sent_at',
    ];

    /**
     * @return BelongsTo<Shift, $this>
     */
    public function shift(): BelongsTo
    {
        return $this->belongsTo(Shift::class);
    }

    /**
     * @return BelongsTo<HockeyTeam, $this>
     */
    public function hockeyTeam(): BelongsTo
    {
        return $this->belongsTo(HockeyTeam::class);
    }

    protected function casts(): array
    {
        return [
            'reminder_sent_at' => 'datetime',
        ];
    }
}
