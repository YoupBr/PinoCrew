<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Herinnering PinoCrew-dienst</title>
</head>

<body
    style="
        margin: 0;
        padding: 0;
        background-color: #f1f5f9;
        font-family: Arial, Helvetica, sans-serif;
        color: #1e293b;
        -webkit-font-smoothing: antialiased;">

{{-- Buitenste achtergrond --}}
<table
    role="presentation"
    width="100%"
    cellpadding="0"
    cellspacing="0"
    border="0"
    style="
        width: 100%;
        background-color: #f1f5f9;
        padding: 32px 12px;">

    <tr>
        <td align="center">

            {{-- E-mail container --}}
            <table
                role="presentation"
                width="600"
                cellpadding="0"
                cellspacing="0"
                border="0"
                style="
                    width: 100%;
                    max-width: 600px;
                    background-color: #ffffff;
                    border-radius: 16px;
                    overflow: hidden;">

                {{-- Header --}}
                <tr>
                    <td
                        style="
                            padding: 28px 32px;
                            background-color: #00257b;
                            text-align: center;">

                        <img src="{{ asset('img/logo.png') }}" alt="PinoCrew" width="150" 
                        style="
                                display: block;
                                width: 150px;
                                max-width: 100%;
                                height: auto;
                                margin: 0 auto;">

                    </td>
                </tr>


                {{-- Content --}}
                <tr>
                    <td
                        style="
                            padding: 36px 32px 32px;">

                        {{-- Titel --}}
                        <h1 style="
                                margin: 0 0 12px;
                                font-size: 24px;
                                line-height: 32px;
                                font-weight: 700;
                                color: #0f172a;">
                            Je PinoCrew-dienst is morgen!
                        </h1>


                        {{-- Begroeting --}}
                        <p  style="
                                margin: 0 0 16px;
                                font-size: 16px;
                                line-height: 25px;
                                color: #475569;">
                            Beste {{ $signup->name }},
                        </p>

                        <p  style="
                                margin: 0 0 28px;
                                font-size: 16px;
                                line-height: 25px;
                                color: #475569;">
                            Even een reminder: morgen sta je ingepland voor een
                            PinoCrew-dienst. Hieronder vind je nog een keer alle
                            gegevens van je dienst.
                        </p>


                        {{-- Dienstgegevens --}}
                        <table
                            role="presentation"
                            width="100%"
                            cellpadding="0"
                            cellspacing="0"
                            border="0"
                            style="
                                width: 100%;
                                background-color: #f8fafc;
                                border: 1px solid #e2e8f0;
                                border-radius: 12px;">
                            <tr>
                                <td style="padding: 24px;">

                                    {{-- Dienst --}}
                                    <table
                                        role="presentation"
                                        width="100%"
                                        cellpadding="0"
                                        cellspacing="0"
                                        border="0">
                                        <tr>
                                            <td
                                                style="
                                                    padding-bottom: 18px;
                                                    font-size: 13px;
                                                    line-height: 18px;
                                                    font-weight: 700;
                                                    text-transform: uppercase;
                                                    letter-spacing: 0.5px;
                                                    color: #64748b;">
                                                Dienst
                                            </td>
                                        </tr>

                                        <tr>
                                            <td
                                                style="
                                                    padding-bottom: 22px;
                                                    font-size: 18px;
                                                    line-height: 24px;
                                                    font-weight: 700;
                                                    color: #0f172a;">
                                                {{ $signup->shift->title }}
                                            </td>
                                        </tr>
                                    </table>


                                    {{-- Datum --}}
                                    <table
                                        role="presentation"
                                        width="100%"
                                        cellpadding="0"
                                        cellspacing="0"
                                        border="0">

                                        <tr>
                                            <td
                                                width="110"
                                                valign="top"
                                                style="
                                                    padding: 10px 0;
                                                    font-size: 14px;
                                                    font-weight: 600;
                                                    color: #64748b;">
                                                Datum
                                            </td>

                                            <td valign="top"
                                                style="
                                                    padding: 10px 0;
                                                    font-size: 14px;
                                                    font-weight: 600;
                                                    color: #1e293b;">

                                                {{ \Carbon\Carbon::parse($signup->shift->date)
                                                    ->locale('nl')
                                                    ->translatedFormat('l j F Y') }}
                                            </td>
                                        </tr>


                                        {{-- Tijd --}}
                                        <tr>
                                            <td width="110"
                                                valign="top"
                                                style="
                                                    padding: 10px 0;
                                                    font-size: 14px;
                                                    font-weight: 600;
                                                    color: #64748b;">
                                                Tijd
                                            </td>

                                            <td valign="top"
                                                style="
                                                    padding: 10px 0;
                                                    font-size: 14px;
                                                    font-weight: 600;
                                                    color: #1e293b;">

                                                {{ \Carbon\Carbon::parse($signup->shift->starts_at)->format('H:i') }}
                                                –
                                                {{ \Carbon\Carbon::parse($signup->shift->ends_at)->format('H:i') }}
                                            </td>
                                        </tr>


                                        {{-- Locatie --}}
                                        @if ($signup->shift->location)
                                            <tr>
                                                <td
                                                    width="110"
                                                    valign="top"
                                                    style="
                                                        padding: 10px 0;
                                                        font-size: 14px;
                                                        font-weight: 600;
                                                        color: #64748b;">
                                                    Locatie
                                                </td>

                                                <td
                                                    valign="top"
                                                    style="
                                                        padding: 10px 0;
                                                        font-size: 14px;
                                                        font-weight: 600;
                                                        color: #1e293b;">

                                                    {{ $signup->shift->location }}
                                                </td>
                                            </tr>
                                        @endif


                                        {{-- Team --}}
                                        @if ($signup->hockeyTeam)
                                            <tr>
                                                <td width="110"
                                                    valign="top"
                                                    style="
                                                        padding: 10px 0;
                                                        font-size: 14px;
                                                        font-weight: 600;
                                                        color: #64748b;">
                                                    Team
                                                </td>

                                                <td valign="top"
                                                    style="
                                                        padding: 10px 0;
                                                        font-size: 14px;
                                                        font-weight: 600;
                                                        color: #1e293b;">

                                                    {{ $signup->hockeyTeam->name }}
                                                </td>
                                            </tr>
                                        @endif

                                    </table>

                                </td>
                            </tr>
                        </table>


                        {{-- Onderste tekst --}}
                        <p
                            style="
                                margin: 28px 0 0;
                                font-size: 16px;
                                line-height: 25px;
                                color: #475569;">

                            We rekenen op je. Tot morgen en alvast bedankt voor je hulp!
                        </p>


                        {{-- Contactblok --}}
                        <table
                            role="presentation"
                            width="100%"
                            cellpadding="0"
                            cellspacing="0"
                            border="0"
                            style="
                                width: 100%;
                                margin-top: 24px;
                                background-color: #eff6ff;
                                border-radius: 10px;">

                            <tr>
                                <td
                                    style="
                                        padding: 18px 20px;
                                        font-size: 14px;
                                        line-height: 22px;
                                        color: #475569;">

                                    <strong style="color: #1e293b;">
                                        Kun je onverwacht niet?
                                    </strong>
                                    <br>
                                    Voor annuleringen of wijzigingen kun je contact
                                    opnemen met de zaalhockeycommissie via
                                    <a  href="mailto:zaalhockey@pinoke.nl"
                                        style="
                                            color: #1d4ed8;
                                            text-decoration: none;
                                            font-weight: 600;">
                                        zaalhockey@pinoke.nl
                                    </a>.
                                </td>
                            </tr>
                        </table>


                        {{-- Afsluiting --}}
                        <p
                            style="
                                margin: 28px 0 0;
                                font-size: 15px;
                                line-height: 24px;
                                color: #475569;">
                            Met vriendelijke groet,<br>

                            <strong style="color: #1e293b;">
                                PinoCrew
                            </strong>
                        </p>

                    </td>
                </tr>


                {{-- Footer --}}
                <tr>
                    <td
                        style="
                            padding: 22px 32px;
                            background-color: #f8fafc;
                            border-top: 1px solid #e2e8f0;
                            text-align: center;">

                        <p  style="
                                margin: 0;
                                font-size: 12px;
                                line-height: 18px;
                                color: #94a3b8;"">
                            Deze e-mail is automatisch verstuurd door PinoCrew.
                        </p>

                        <p  style="
                                margin: 4px 0 0;
                                font-size: 12px;
                                line-height: 18px;
                                color: #94a3b8;">
                            © {{ date('Y') }} Pinoké
                        </p>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>

</body>
</html>