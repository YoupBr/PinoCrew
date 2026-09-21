<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bevestiging PinoCrew</title>
</head>

<body style="
    margin: 0;
    padding: 0;
    background-color: #f3f4f6;
    font-family: Arial, Helvetica, sans-serif;
    color: #1f2937;">

<table
    role="presentation"
    width="100%"
    cellspacing="0"
    cellpadding="0"
    border="0"
    style="width: 100%; background-color: #f3f4f6;">

    <tr>
        <td align="center" style="padding: 32px 16px;">

            <!-- Mail container -->
            <table
                role="presentation"
                width="100%"
                cellspacing="0"
                cellpadding="0"
                border="0"
                style="
                    width: 100%;
                    max-width: 600px;
                    background-color: #ffffff;
                    border-radius: 16px;
                    overflow: hidden;">

                <!-- Header -->
                <tr>
                    <td
                        align="center"
                        style="
                            background-color: #00257b;
                            padding: 28px 32px;">
                        <img
                            src="{{ $message->embed(public_path('img/logo.png')) }}"
                            alt="PinoCrew"
                            width="160"
                            style="
                                display: block;
                                width: 160px;
                                max-width: 100%;
                                height: auto;
                                border: 0;">
                    </td>
                </tr>

                <!-- Intro -->
                <tr>
                    <td style="padding: 36px 32px 20px 32px;">

                        <h1 style="
                            margin: 0 0 24px 0;
                            font-size: 26px;
                            line-height: 1.25;
                            color: #111827;">
                            Je inschrijving is gelukt! 🎉
                        </h1>

                        <p style="
                            margin: 0 0 16px 0;
                            font-size: 16px;
                            line-height: 1.6;
                        ">
                            Hoi {{ $signup->name }},
                        </p>

                        <p style="
                            margin: 0;
                            font-size: 16px;
                            line-height: 1.6;
                            color: #4b5563;">
                            Bedankt voor je inschrijving bij PinoCrew!
                            Je inschrijving is succesvol ontvangen.
                            Hieronder vind je de gegevens van je dienst.
                        </p>

                    </td>
                </tr>

                <!-- Details -->
                <tr>
                    <td style="padding: 4px 32px 32px 32px;">

                        <table
                            role="presentation"
                            width="100%"
                            cellspacing="0"
                            cellpadding="0"
                            border="0"
                            style="
                                width: 100%;
                                background-color: #f8fafc;
                                border: 1px solid #e5e7eb;
                                border-radius: 12px;">

                            <tr>
                                <td style="padding: 24px;">

                                    <p style="
                                        margin: 0 0 20px 0;
                                        font-size: 12px;
                                        line-height: 1.4;
                                        font-weight: bold;
                                        text-transform: uppercase;
                                        letter-spacing: 1px;
                                        color: #64748b;
                                    ">
                                        Jouw inschrijving
                                    </p>

                                    <p style="margin: 0 0 14px 0; font-size: 15px; line-height: 1.5;">
                                        <strong style="color: #111827;">Dienst</strong><br>
                                        <span style="color: #4b5563;">
                                            {{ $signup->shift->title }}
                                        </span>
                                    </p>

                                    <p style="margin: 0 0 14px 0; font-size: 15px; line-height: 1.5;">
                                        <strong style="color: #111827;">Datum</strong><br>
                                        <span style="color: #4b5563;">
                                            {{ $signup->shift->date->format('d-m-Y') }}
                                        </span>
                                    </p>

                                    <p style="margin: 0 0 14px 0; font-size: 15px; line-height: 1.5;">
                                        <strong style="color: #111827;">Tijd</strong><br>
                                        <span style="color: #4b5563;">
                                            {{ \Carbon\Carbon::parse($signup->shift->starts_at)->format('H:i') }}
                                            –
                                            {{ \Carbon\Carbon::parse($signup->shift->ends_at)->format('H:i') }}
                                        </span>
                                    </p>

                                    @if($signup->shift->location)
                                        <p style="margin: 0 0 14px 0; font-size: 15px; line-height: 1.5;">
                                            <strong style="color: #111827;">Locatie</strong><br>
                                            <span style="color: #4b5563;">
                                                {{ $signup->shift->location }}
                                            </span>
                                        </p>
                                    @endif

                                    <p style="margin: 0; font-size: 15px; line-height: 1.5;">
                                        <strong style="color: #111827;">Team</strong><br>
                                        <span style="color: #4b5563;">
                                            {{ $signup->hockeyTeam->name }}
                                        </span>
                                    </p>

                                </td>
                            </tr>
                        </table>

                    </td>
                </tr>

                <!-- Closing -->
                <tr>
                    <td style="padding: 0 32px 36px 32px;">

                        <p style="margin: 0 0 16px 0;
                            font-size: 15px;
                            line-height: 1.6;
                            color: #4b5563;">
                            We sturen je voor aanvang van de dienst nog een herinnering.
                        </p>

                        <p style="margin: 0 0 24px 0;
                            font-size: 15px;
                            line-height: 1.6;
                            color: #4b5563;">
                            Tot dan en bedankt voor je hulp!
                        </p>

                        <p style="
                            margin: 0;
                            font-size: 15px;
                            line-height: 1.6;">
                            Met vriendelijke groet,<br>
                            <strong>PinoCrew</strong>
                        </p>

                    </td>
                </tr>

                <!-- Footer -->
                <tr>
                    <td
                        align="center"
                        style="padding: 20px 32px;
                            background-color: #f8fafc;
                            border-top: 1px solid #e5e7eb;">
                        <p style="margin: 0; font-size: 12px; line-height: 1.5; color: #94a3b8;">
                            Deze e-mail is automatisch verzonden door PinoCrew.</p>
                    </td>
                </tr>

            </table>

        </td>
    </tr>
</table>

</body>
</html>