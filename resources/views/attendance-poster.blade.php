<x-layouts::app :title="__('QR-poster')">
    <div class="mx-auto flex max-w-5xl flex-col gap-8">
        <div>
            <flux:heading size="xl">QR-poster voor aanwezigheid</flux:heading>
            <flux:text class="mt-2">Maak een duidelijke A4-poster om op locatie op te hangen.</flux:text>
        </div>
        <div class="grid gap-8 lg:grid-cols-2">
            <form method="GET" action="{{ route('attendance-poster.print', ['current_team' => request()->route('current_team')]) }}" class="space-y-6 rounded-2xl border border-zinc-200 bg-white p-6 dark:border-zinc-700 dark:bg-zinc-900">
                <flux:input name="title" label="Titel op de poster" :value="old('title', 'Meld je aanwezigheid aan')" maxlength="70" required />
                <flux:input name="location" label="Locatie of activiteit (optioneel)" :value="old('location')" placeholder="Bijvoorbeeld: Blaashal · Pinoké Veld 14" maxlength="90" />
                <flux:textarea name="instruction" label="Instructie" maxlength="220" rows="3" required>{{ old('instruction', 'Scan de QR-code met de camera van je telefoon en meld je aanwezigheid aan.') }}</flux:textarea>
                <flux:error name="instruction" />
                <div class="rounded-xl bg-zinc-50 p-4 text-sm dark:bg-zinc-800">
                    <p class="font-medium">Bestemming van de QR-code</p>
                    <p class="mt-1 break-all">https://pinocrew.nl/aanwezigheid</p>
                    <p class="mt-3 text-zinc-500 dark:text-zinc-400">De aanwezigheidspagina wordt later gekoppeld. Een titel of locatie verandert de QR-link niet.</p>
                </div>
                <flux:button type="submit" variant="primary">Maak A4-poster</flux:button>
                <flux:text>In het volgende scherm kun je printen of via je browser opslaan als PDF.</flux:text>
            </form>
            <div class="flex flex-col items-center justify-center gap-5 rounded-2xl border border-zinc-200 bg-zinc-50 p-8 text-center dark:border-zinc-700 dark:bg-zinc-900">
                <img src="{{ asset('img/logo-dark.png') }}" alt="PinoCrew" class="h-10 w-auto dark:hidden">
                <img src="{{ asset('img/logo-light.png') }}" alt="PinoCrew" class="hidden h-10 w-auto dark:block">
                <flux:heading size="lg">Even scannen. Klaar om te helpen.</flux:heading>
                <img src="{{ asset('img/attendance-qr.svg') }}" alt="QR-code naar pinocrew.nl/aanwezigheid" width="240" height="240" class="rounded-xl bg-white">
                <flux:text>Een scherpe QR-code op een rustige, goed leesbare poster.</flux:text>
            </div>
        </div>
    </div>
</x-layouts::app>
