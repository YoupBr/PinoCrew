<x-layouts::public title="Aanwezigheid | PinoCrew">
    <main class="mx-auto max-w-xl px-5 py-10 sm:py-16">
        <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm sm:p-8">
            <h1 class="text-3xl font-bold text-slate-900">Welkom!</h1>
            <p class="mt-3 text-slate-600">Zoek je naam en selecteer de juiste dienst. Je meldt je aan voor een bestaande dienst van vandaag.</p>
            @if (session('attendance_success'))
                <p role="status" class="mt-6 rounded-xl bg-green-50 p-4 text-green-800">{{ session('attendance_success') }}</p>
            @else
                <form method="POST" action="{{ route('attendance.store') }}" class="mt-7 space-y-5"
                    x-data="{
                        name: '', matches: [], token: '', message: '', loading: false, sequence: 0,
                        async search() {
                            const sequence = ++this.sequence;
                            this.matches = []; this.token = ''; this.message = ''; this.loading = false;
                            if (this.name.trim().length < 3) return;
                            this.loading = true;
                            try {
                                const params = new URLSearchParams({name: this.name.trim()});
                                const response = await fetch(@js(route('attendance.search')) + '?' + params, {headers: {'Accept': 'application/json'}, cache: 'no-store'});
                                if (sequence !== this.sequence) return;
                                if (!response.ok) {
                                    if (response.status === 429) throw new Error('Je zoekt te snel. Wacht een minuut en probeer opnieuw.');
                                    throw new Error('Zoeken lukt nu niet. Meld je bij de coördinator als dit blijft gebeuren.');
                                }
                                const result = await response.json();
                                if (sequence !== this.sequence) return;
                                this.matches = result.matches;
                                this.message = result.message || '';
                            } catch (error) {
                                if (sequence === this.sequence) this.message = error.message || 'Zoeken lukt nu niet. Probeer het even later of meld je bij de coördinator.';
                            } finally { if (sequence === this.sequence) this.loading = false; }
                        }
                    }">
                    @csrf
                    <label class="block font-medium text-slate-900" for="attendance-name">Je naam</label>
                    <input id="attendance-name" type="text" x-model="name" @input="sequence++; token = ''; matches = []; message = ''" @input.debounce.400ms="search()" autocomplete="off" maxlength="100" placeholder="Typ minimaal 3 letters van je voor- of achternaam" class="w-full rounded-xl border border-slate-300 p-3 text-slate-900" aria-describedby="attendance-help">
                    <p id="attendance-help" class="text-sm text-slate-500">Selecteer hieronder je eigen naam en de juiste dienst. Aanmelden kan vanaf twee uur voor aanvang tot het einde van je dienst.</p>
                    <div aria-live="polite" class="text-sm text-slate-600">
                        <p x-show="loading" x-cloak>Naam zoeken…</p>
                        <p x-text="message"></p>
                    </div>
                    <input type="hidden" name="token" :value="token">
                    <div x-show="matches.length" x-cloak class="space-y-2" role="group" aria-label="Selecteer je inschrijving">
                        <p class="mb-2 font-medium text-slate-900">Selecteer je inschrijving</p>
                        <template x-for="match in matches" :key="match.token">
                            <button type="button"
                                @click="token = match.token"
                                :aria-pressed="token === match.token"
                                :class="token === match.token ? 'border-blue-600 bg-blue-50 ring-2 ring-blue-600' : 'border-slate-300 bg-white hover:bg-slate-50'"
                                class="relative flex w-full cursor-pointer items-center justify-between gap-3 rounded-xl border p-4 text-left text-slate-900 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600">
                                <span class="pointer-events-none" x-text="match.label"></span>
                                <span class="pointer-events-none shrink-0 text-sm font-semibold text-blue-700" x-show="token === match.token">Geselecteerd ✓</span>
                            </button>
                        </template>
                    </div>
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
