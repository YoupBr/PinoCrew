<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>PinoCrew - Export</title>

    <style>
        * {
            box-sizing: border-box;
        }

        @page {
            size: A4 portrait;
            margin: 14mm 15mm 16mm;
        }

        body {
            margin: 0;
            background: #f3f4f6;
            color: #111827;
            font-family: Arial, Helvetica, sans-serif;
            font-size: 10pt;
            line-height: 1.4;
        }

        .page {
            width: 210mm;
            min-height: 297mm;
            margin: 20px auto;
            padding: 15mm;
            background: white;
            box-shadow: 0 4px 24px rgba(0, 0, 0, .08);
        }

        /* HEADER */

        .header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            padding-bottom: 10mm;
            border-bottom: 2px solid #00257b;
        }

        .brand {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .logo {
            height: 38px;
            width: auto;
        }

        .brand-name {
            font-size: 20pt;
            font-weight: 800;
            letter-spacing: -0.5px;
            color: #00257b;
        }

        .document-meta {
            text-align: right;
            color: #6b7280;
            font-size: 8.5pt;
        }

        .document-meta strong {
            display: block;
            color: #111827;
            font-size: 9.5pt;
        }

        /* TITLE */

        .title-section {
            padding: 10mm 0 7mm;
        }

        .eyebrow {
            margin-bottom: 2mm;
            color: #00257b;
            font-size: 8pt;
            font-weight: 700;
            letter-spacing: 1.2px;
            text-transform: uppercase;
        }

        h1 {
            margin: 0;
            font-size: 24pt;
            line-height: 1.1;
            letter-spacing: -0.7px;
        }

        .subtitle {
            margin-top: 2mm;
            color: #6b7280;
            font-size: 11pt;
        }

        /* INFO */

        .info-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 3mm;
            margin-bottom: 8mm;
        }

        .info-card {
            padding: 4mm;
            background: #f7f8fb;
            border-radius: 2mm;
        }

        .info-label {
            margin-bottom: 1mm;
            color: #6b7280;
            font-size: 7.5pt;
            font-weight: 700;
            letter-spacing: .6px;
            text-transform: uppercase;
        }

        .info-value {
            font-size: 10.5pt;
            font-weight: 700;
        }

        /* TABLE */

        table {
            width: 100%;
            border-collapse: collapse;
        }

        thead {
            display: table-header-group;
        }

        tr {
            break-inside: avoid;
            page-break-inside: avoid;
        }

        th {
            padding: 3mm 2.5mm;
            border-bottom: 2px solid #00257b;
            color: #00257b;
            font-size: 7.5pt;
            font-weight: 700;
            letter-spacing: .5px;
            text-align: left;
            text-transform: uppercase;
        }

        td {
            padding: 3.2mm 2.5mm;
            border-bottom: 1px solid #e5e7eb;
            vertical-align: middle;
        }

        tbody tr:nth-child(even) {
            background: #fafafa;
        }

        .number {
            width: 8mm;
            color: #9ca3af;
        }

        .name {
            font-weight: 700;
        }

        .team {
            white-space: nowrap;
        }

        .phone {
            white-space: nowrap;
        }

        .check-column {
            width: 12mm;
            text-align: center;
        }

        .checkbox {
            display: inline-block;
            width: 4.5mm;
            height: 4.5mm;
            border: 1.5px solid #6b7280;
            border-radius: 1mm;
        }

        /* FOOTER */

        .footer {
            margin-top: 10mm;
            padding-top: 4mm;
            border-top: 1px solid #e5e7eb;
            display: flex;
            justify-content: space-between;
            color: #9ca3af;
            font-size: 8pt;
        }

        /* SCREEN BUTTONS */

        .toolbar {
            position: fixed;
            top: 20px;
            right: 20px;
            display: flex;
            gap: 8px;
        }

        .toolbar button {
            padding: 10px 16px;
            border: 0;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 700;
        }

        .print-button {
            background: #00257b;
            color: white;
        }

        .close-button {
            background: white;
            color: #111827;
            border: 1px solid #d1d5db !important;
        }

        /* ACTUAL PRINT */

        @media print {
            body {
                background: white;
            }

            .page {
                width: auto;
                min-height: 0;
                margin: 0;
                padding: 0;
                box-shadow: none;
            }

            .toolbar {
                display: none !important;
            }

            .info-card {
                background: #f7f8fb !important;
                print-color-adjust: exact;
                -webkit-print-color-adjust: exact;
            }

            tbody tr:nth-child(even) {
                background: #fafafa !important;
                print-color-adjust: exact;
                -webkit-print-color-adjust: exact;
            }

            .header,
            th {
                print-color-adjust: exact;
                -webkit-print-color-adjust: exact;
            }
        }
    </style>
</head>

<body>

<div class="toolbar">
    <button class="close-button" onclick="window.close()">
        Sluiten
    </button>

    <button class="print-button" onclick="window.print()">
        Printen
    </button>
</div>

<main class="page">

    <header class="header">
        <div class="brand">
            <img
                src="{{ asset('img/logoblauw.png') }}"
                alt="Pinoké Logo"
                class="logo"
            >

            <div class="brand-name">
                PinoCrew
            </div>
        </div>

        <div class="document-meta">
            Gegenereerd op
            <strong>{{ now()->format('d-m-Y H:i') }}</strong>
        </div>
    </header>


    <section class="title-section">
        <div class="eyebrow">
            Crewmanagement
        </div>

        <h1>Aanmeldingen</h1>

        <div class="subtitle">
            Overzicht van aangemelde vrijwilligers
        </div>
    </section>


    <section class="info-grid">

        <div class="info-card">
            <div class="info-label">Aanmeldingen</div>
            <div class="info-value">
                {{ $signups->count() }}
            </div>
        </div>

        <div class="info-card">
            <div class="info-label">Dienst</div>
            <div class="info-value">
                {{ $shift?->title ?? 'Alle diensten' }}
            </div>
        </div>

        <div class="info-card">
            <div class="info-label">Team</div>
            <div class="info-value">
                {{ $hockeyTeam?->name ?? 'Alle teams' }}
            </div>
        </div>

    </section>


    <table>
        <thead>
        <tr>
            <th class="number">#</th>
            <th>Naam</th>
            <th>Team</th>
            <th>Telefoon</th>
            <th class="check-column">Aanwezig</th>
        </tr>
        </thead>

        <tbody>
        @forelse ($signups as $signup)
            <tr>
                <td class="number">
                    {{ $loop->iteration }}
                </td>

                <td class="name">
                    {{ $signup->name }}
                </td>

                <td class="team">
                    {{ $signup->hockeyTeam?->name ?? '—' }}
                </td>

                <td class="phone">
                    {{ $signup->phone ?? '—' }}
                </td>

                <td class="check-column">
                    <span class="checkbox"></span>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="5">
                    Geen aanmeldingen gevonden.
                </td>
            </tr>
        @endforelse
        </tbody>
    </table>


    <footer class="footer">
        <span>PinoCrew • pinocrew.nl</span>
        <span>Intern crew-overzicht</span>
    </footer>

</main>

</body>
</html>