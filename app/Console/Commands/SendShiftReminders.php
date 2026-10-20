<?php

namespace App\Console\Commands;

use App\Mail\ShiftReminder;
use App\Models\Signup;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Mail;
use Throwable;

class SendShiftReminders extends Command
{
    protected $signature = 'pinocrew:send-reminders {--dry-run : Alleen controleren, geen mails versturen}';

    protected $description = 'Verstuur reminders voor aankomende PinoCrew-diensten';

    public function handle(): int
    {
        $now = now('Europe/Amsterdam');
        $from = $now->copy()->addHours(23);
        $until = $now->copy()->addHours(25);

        $signups = Signup::query()
            ->with(['shift', 'hockeyTeam'])
            ->whereNull('reminder_sent_at')
            ->whereNotNull('email')
            ->where('email', '!=', '')
            ->whereHas('shift', fn ($query) => $query
                ->where('is_published', true)
                ->whereDate('date', '>=', $from->toDateString())
                ->whereDate('date', '<=', $until->toDateString()))
            ->get()
            ->filter(function (Signup $signup) use ($from, $until): bool {
                $shift = $signup->shift;

                if (! $shift?->date || ! $shift->starts_at) {
                    return false;
                }

                $startsAt = Carbon::parse(
                    $shift->date->format('Y-m-d').' '.$shift->starts_at,
                    'Europe/Amsterdam',
                );

                return $startsAt->betweenIncluded($from, $until);
            });

        $this->info("{$signups->count()} reminder(s) gevonden.");

        $failed = false;

        foreach ($signups as $signup) {
            if ($this->option('dry-run')) {
                $this->line(
                    "Zou versturen: inschrijving {$signup->id}, dienst {$signup->shift->title}"
                );

                continue;
            }

            try {
                Mail::to($signup->email)->send(new ShiftReminder($signup));

                $signup->update(['reminder_sent_at' => now()]);

                $this->info("Reminder verstuurd voor inschrijving {$signup->id}.");
            } catch (Throwable $exception) {
                report($exception);

                $this->error("Reminder mislukt voor inschrijving {$signup->id}.");
                $failed = true;
            }
        }

        return $failed ? self::FAILURE : self::SUCCESS;
    }
}