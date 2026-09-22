<?php

namespace App\Console\Commands;

use App\Mail\ShiftReminder;
use App\Models\Signup;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class SendShiftReminders extends Command
{
    protected $signature = 'pinocrew:send-reminders';

    protected $description = 'Verstuur reminders voor aankomende PinoCrew-diensten';

    public function handle(): int
    {
        $now = now();

        /*
         * Zoek diensten die tussen 23 en 25 uur vanaf nu beginnen.
         *
         * Daardoor hoeft de scheduler niet exact op één specifiek
         * tijdstip te draaien om een dienst te vinden.
         */
        $from = $now->copy()->addHours(23);
        $until = $now->copy()->addHours(25);

        $signups = Signup::query()
            ->with(['shift', 'hockeyTeam'])
            ->whereNull('reminder_sent_at')
            ->whereHas('shift', function ($query) use ($from, $until) {
                $query->whereRaw(
                    "TIMESTAMP(date, starts_at) BETWEEN ? AND ?",
                    [
                        $from->format('Y-m-d H:i:s'),
                        $until->format('Y-m-d H:i:s'),
                    ]
                );
            })
            ->get();

        $this->info("{$signups->count()} reminder(s) gevonden.");

        foreach ($signups as $signup) {
            Mail::to($signup->email)
                ->send(new ShiftReminder($signup));

            $signup->update([
                'reminder_sent_at' => now(),
            ]);

            $this->info("Reminder verstuurd naar {$signup->email}");
        }

        return self::SUCCESS;
    }
}