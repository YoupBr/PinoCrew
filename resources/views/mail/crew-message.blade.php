<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <title>Bevestiging PinoCrew</title>
</head>
<body style="font-family: Arial, sans-serif; color: #1f2937; line-height: 1.6;">
    <p>Hoi {{ $signup->name }},</p>

    <p>
        Bedankt voor je inschrijving bij PinoCrew! Je inschrijving is succesvol ontvangen.
    </p>

    <p>
        <strong>Dienst:</strong> {{ $signup->shift->title }}<br>
        <strong>Datum:</strong> {{ $signup->shift->date->format('d-m-Y') }}<br>
        <strong>Tijd:</strong>
        {{ \Carbon\Carbon::parse($signup->shift->starts_at)->format('H:i') }}
        –
        {{ \Carbon\Carbon::parse($signup->shift->ends_at)->format('H:i') }}<br>

        @if($signup->shift->location)
            <strong>Locatie:</strong> {{ $signup->shift->location }}<br>
        @endif

        <strong>Team:</strong> {{ $signup->hockeyTeam->name }}
    </p>

    <p>
        We sturen je voor aanvang van de dienst nog een herinnering.
    </p>

    <p>
        Tot dan en bedankt voor je hulp!
    </p>

    <p>
        Met vriendelijke groet,<br>
        <strong>PinoCrew</strong>
    </p>
</body>
</html>