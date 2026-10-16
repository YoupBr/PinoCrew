<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Aanwezigheid · PinoCrew</title>
    <style>
        * { box-sizing: border-box; }
        body { margin: 0; background: #e9edf3; color: #10284b; font-family: Arial, sans-serif; }
        .toolbar { max-width: 210mm; margin: 20px auto; display: flex; flex-wrap: wrap; align-items: center; gap: 12px; padding: 0 16px; }
        button, .back { border: 1px solid #10284b; border-radius: 8px; padding: 12px 18px; font: inherit; cursor: pointer; text-decoration: none; }
        button { background: #10284b; color: white; } .back { background: white; color: #10284b; }
        .hint { flex-basis: 100%; font-size: 14px; line-height: 1.5; }
        .paper { width: 210mm; min-height: 297mm; margin: 0 auto 30px; background: white; padding: 14mm 16mm 10mm; display: flex; flex-direction: column; align-items: center; text-align: center; box-shadow: 0 8px 40px #10284b20; }
        .logo { height: 15mm; max-width: 75mm; object-fit: contain; }
        .eyebrow { margin: 8mm 0 3mm; font-size: 11pt; letter-spacing: 3px; text-transform: uppercase; }
        h1 { margin: 0; max-width: 170mm; font-size: 22pt; line-height: 1.12; letter-spacing: -1px; overflow-wrap: break-word; }
        .location { margin: 5mm 0 0; font-size: 12pt; line-height: 1.3; overflow-wrap: break-word; }
        .qr { width: 75mm; height: 75mm; display: block; margin: 8mm 0 4mm; }
        .instruction { max-width: 155mm; margin: 0; font-size: 12pt; line-height: 1.4; overflow-wrap: break-word; }
        .url { margin: 7mm 0 0; padding: 4mm 6mm; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 15pt; font-weight: bold; }
        .steps { width: 100%; display: flex; justify-content: center; gap: 8mm; margin-top: 6mm; font-size: 11pt; line-height: 1.5; }
        .steps span { max-width: 45mm; } .steps b { display: block; margin-bottom: 2mm; font-size: 14pt; }
        .footer { width: 100%; margin-top: auto; padding-top: 8mm; font-size: 10pt; color: #52647d; }
        @page { size: A4 portrait; margin: 0; }
        @media print { body { background: white; } .toolbar { display: none; } .paper { margin: 0; box-shadow: none; width: 210mm; min-height: 297mm; } }
        @media screen and (max-width: 820px) { .paper { width: calc(100% - 24px); min-height: auto; padding: 32px 20px; } h1 { font-size: 30px; } .qr { width: min(95mm, 100%); height: auto; } .url { font-size: 15px; overflow-wrap: break-word; } .steps { gap: 16px; } }
    </style>
</head>
<body>
    <div class="toolbar">
        <button type="button" onclick="window.print()">Printen / opslaan als PDF</button>
        <a class="back" href="{{ route('attendance-poster.index', ['current_team' => request()->route('current_team')]) }}">Poster aanpassen</a>
        <div class="hint">Kies A4, staand en schaal 100%. Schakel kop- en voetteksten uit. Kies ‘Opslaan als PDF’ om te downloaden. De aanwezigheidspagina wordt later gekoppeld.</div>
    </div>
    <main class="paper">
        <img class="logo" src="{{ asset('img/logo-dark.png') }}" alt="PinoCrew">
        <p class="eyebrow">Aanwezigheid</p>
        <h1>{{ $title }}</h1>
        @if (! empty($location))
            <p class="location">{{ $location }}</p>
        @endif
        <img class="qr" src="{{ asset('img/attendance-qr.svg') }}" alt="QR-code naar https://pinocrew.nl/aanwezigheid" width="360" height="360">
        <p class="instruction">{{ $instruction }}</p>
        <p class="url">pinocrew.nl/aanwezigheid</p>
        <div class="steps">
            <span><b>1</b>Open je camera</span>
            <span><b>2</b>Scan de QR-code</span>
            <span><b>3</b>Meld je aan</span>
        </div>
        <footer class="footer">Powered by MRY AV</footer>
    </main>
</body>
</html>
