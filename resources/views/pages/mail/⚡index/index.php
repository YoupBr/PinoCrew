<?php

use App\Mail\CrewMessage;
use App\Models\HockeyTeam;
use App\Models\MailLog;
use App\Models\Shift;
use App\Models\Signup;
use Illuminate\Support\Facades\Mail;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Component;

new #[Layout('layouts::app')] class extends Component
{
    public string $search = '';

    public string $shiftFilter = '';

    public string $teamFilter = '';

    public array $selected = [];

    public string $subject = '';

    public string $body = '';

    public bool $sent = false;

    #[Computed]
    public function recipients()
    {
        return Signup::query()
            ->with([
                'shift',
                'hockeyTeam',
            ])
            ->when(
                $this->shiftFilter !== '',
                fn ($query) =>
                    $query->where('shift_id', $this->shiftFilter)
            )
            ->when(
                $this->teamFilter !== '',
                fn ($query) =>
                    $query->where('hockey_team_id', $this->teamFilter)
            )
            ->when($this->search !== '', function ($query) {
                $search = '%'.$this->search.'%';

                $query->where(function ($query) use ($search) {
                    $query
                        ->where('name', 'like', $search)
                        ->orWhere('email', 'like', $search)
                        ->orWhereHas(
                            'hockeyTeam',
                            fn ($query) =>
                                $query->where('name', 'like', $search)
                        )
                        ->orWhereHas(
                            'shift',
                            fn ($query) =>
                                $query->where('title', 'like', $search)
                        );
                });
            })
            ->orderBy('name')
            ->get();
    }

    #[Computed]
    public function shifts()
    {
        return Shift::query()
            ->orderByDesc('date')
            ->orderBy('starts_at')
            ->get();
    }

    #[Computed]
    public function hockeyTeams()
    {
        return HockeyTeam::query()
            ->where('active', true)
            ->orderBy('name')
            ->get();
    }

    #[Computed]
    public function selectedRecipients()
    {
        return Signup::query()
            ->whereIn('id', $this->selected)
            ->get();
    }

    #[Computed]
    public function mailLogs()
    {
        return MailLog::query()
            ->with('user')
            ->latest('sent_at')
            ->latest('id')
            ->limit(25)
            ->get();
    }

    public function selectAll(): void
    {
        $this->selected = $this->recipients
            ->pluck('id')
            ->map(fn ($id) => (string) $id)
            ->all();
    }

    public function deselectAll(): void
    {
        $this->selected = [];
    }

    public function send(): void
    {
        $validated = $this->validate([
            'selected' => [
                'required',
                'array',
                'min:1',
            ],

            'subject' => [
                'required',
                'string',
                'max:255',
            ],

            'body' => [
                'required',
                'string',
                'max:10000',
            ],
        ], [
            'selected.required' => 'Selecteer minimaal één ontvanger.',
            'selected.min' => 'Selecteer minimaal één ontvanger.',
            'subject.required' => 'Vul een onderwerp in.',
            'body.required' => 'Vul een bericht in.',
        ]);

        $recipients = Signup::query()
            ->whereIn('id', $validated['selected'])
            ->whereNotNull('email')
            ->get();

        foreach ($recipients as $recipient) {
            Mail::to($recipient->email)
                ->send(
                    new CrewMessage(
                        mailSubject: $validated['subject'],
                        mailBody: $validated['body'],
                    )
                );
        }

        MailLog::create([
            'user_id' => auth()->id(),
            'subject' => $validated['subject'],
            'body' => $validated['body'],
            'from_address' => config('mail.from.address'),
            'recipient_count' => $recipients->count(),
            'sent_at' => now(),
        ]);

        $this->selected = [];
        $this->subject = '';
        $this->body = '';

        $this->sent = true;
    }
};