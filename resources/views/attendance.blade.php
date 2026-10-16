<x-layouts::public title="Aanwezigheid | PinoCrew">
    <main class="mx-auto max-w-xl px-5 py-10 sm:py-16">
        <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm sm:p-8">
            <h1 class="text-3xl font-bold text-slate-900">Welkom!</h1>
            <p class="mt-3 text-slate-600">Kies je team en zoek je naam. Je meldt je aan voor een bestaande dienst van vandaag.</p>
            @if (session('attendance_success'))
                <p role="status" class="mt-6 rounded-xl bg-green-50 p-4 text-green-800">{{ session('attendance_success') }}</p>
                <a href="{{ route('attendance.index') }}" class="mt-5 inline-block underline">Volgende vrijwilliger aanmelden</a>
            @else
                <form method="POST" action="{{ route('attendance.store') }}" class="mt-7 space-y-5"
                    x-data="{
                        team: '', name: '', matches: [], token: '', message: '', loading: false, sequence: 0,
                        async search() {
                            const sequence = ++this.sequence;
                            this.matches = []; this.token = ''; this.message = ''; this.loading = false;
                            if (!this.team || this.name.trim().length < 3) return;
                            this.loading = true;
                            try {
                                const params = new URLSearchParams({team: this.team, name: this.name.trim()});
                                const response = await fetch(@js(route('attendance.search')) + '?' + params, {headers: {'Accept': 'application/json'}, cache: 'no-store'});
                                if (sequence !== this.sequence) return;
                                if (!response.ok) throw new Error();
                                const result = await response.json();
                                if (sequence !== this.sequence) return;
                                this.matches = result.matches;
                                if (!this.matches.length) this.message = 'Geen dienst gevonden. Controleer je team en naam of vraag de coördinator om hulp.';
                            } catch (error) {
                                if (sequence === this.sequence) this.message = 'Zoeken lukt nu niet. Probeer het even later of meld je bij de coördinator.';
                            } finally { if (sequence === this.sequence) this.loading = false; }
                        }
                    }">
                    @csrf
                    <label class="block font-medium text-slate-900" for="attendance-team">Je team</label>
                    <select id="attendance-team" x-model="team" @change="search()" required class="w-full rounded-xl border border-slate-300 bg-white p-3 text-slate-900">
                        <option value="">Kies je team</option>
                        @foreach ($teams as $team)
                            <option value="{{ $team->id }}">{{ $team->name }}</option>
                        @endforeach
                    </select>
                    <label class="block font-medium text-slate-900" for="attendance-name">Je naam</label>
                    <input id="attendance-name" type="text" x-model="name" @input="sequence++; token = ''; matches = []; message = ''" @input.debounce.400ms="search()" autocomplete="off" maxlength="100" placeholder="Typ minimaal 3 letters van je naam" class="w-full rounded-xl border border-slate-300 p-3 text-slate-900" aria-describedby="attendance-help">
                    <p id="attendance-help" class="text-sm text-slate-500">Selecteer hieronder je eigen naam en de juiste dienst. Aanmelden kan vanaf twee uur voor aanvang tot het einde van je dienst.</p>
                    <div aria-live="polite" class="text-sm text-slate-600">
                        <p x-show="loading" x-cloak>Naam zoeken…</p>
                        <p x-text="message"></p>
                    </div>
                    <fieldset x-show="matches.length" x-cloak class="space-y-2">
                        <legend class="mb-2 font-medium text-slate-900">Selecteer je inschrijving</legend>
                        <template x-for="match in matches" :key="match.token">
                            <label class="flex cursor-pointer items-start gap-3 rounded-xl border border-slate-200 p-4 text-slate-900">
                                <input type="radio" name="token" x-model="token" :value="match.token" required class="mt-1">
                                <span x-text="match.label"></span>
                            </label>
                        </template>
                    </fieldset>
                    @error('token')<p role="alert" class="text-sm text-red-700">{{ $message }}</p>@enderror
                    <button type="submit" :disabled="!token || loading" class="w-full rounded-xl bg-slate-900 px-5 py-4 font-semibold text-white disabled:cursor-not-allowed disabled:opacity-40">Ik ben aanwezig</button>
                    <noscript>Schakel JavaScript in of meld je bij de coördinator.</noscript>
                </form>
            @endif
            <p class="mt-7 text-sm text-slate-500">We registreren je aankomst bij je inschrijving. <a class="underline" href="{{ route('legal.privacy') }}">Privacyverklaring</a></p>
            <p class="mt-2 text-sm text-slate-500">Powered by <a class="underline" href="https://mryav.nl">MRY AV</a></p>
        </div>
    </main>
</x-layouts::public>
