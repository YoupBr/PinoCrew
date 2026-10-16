<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

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

    public function attendanceStartsAt(): Carbon
    {
        return Carbon::parse($this->date->format('Y-m-d').' '.$this->starts_at, 'Europe/Amsterdam');
    }

    public function attendanceEndsAt(): Carbon
    {
        $start = $this->attendanceStartsAt();

        if (! $this->ends_at) {
            return $start->copy()->endOfDay();
        }

        $end = Carbon::parse($this->date->format('Y-m-d').' '.$this->ends_at, 'Europe/Amsterdam');

        return $end->lt($start) ? $end->addDay() : $end;
    }

    public function isAttendanceOpen(): bool
    {
        return $this->is_published && now('Europe/Amsterdam')->betweenIncluded(
            $this->attendanceStartsAt()->subHours(2), $this->attendanceEndsAt()
        );
    }

    protected function casts(): array
    {
        return [
            'date' => 'date',
            'is_published' => 'boolean',
        ];
    }

    /**
     * @return HasMany<Signup, $this>
     */
    public function signups(): HasMany
    {
        return $this->hasMany(Signup::class);
    }
}
