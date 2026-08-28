<x-layouts::auth :title="__('Registreren')">
    <div class="flex flex-col gap-6">
        <x-auth-header
            :title="__('Aanmelden bij PinoCrew')"
            :description="__('Maak een account aan en kies het team dat je begeleidt.')"
        />

        <form method="POST" action="{{ route('register.store') }}" class="flex flex-col gap-5">
            @csrf

            <flux:input
                name="name"
                :label="__('Naam')"
                :value="old('name')"
                type="text"
                required
                autofocus
                autocomplete="name"
                placeholder="Voor- en achternaam"
            />

            <flux:input
                name="email"
                :label="__('E-mailadres')"
                :value="old('email')"
                type="email"
                required
                autocomplete="email"
                placeholder="naam@voorbeeld.nl"
            />

            <flux:select
                name="hockey_team_id"
                :label="__('Team')"
                required
            >
                <flux:select.option value="">
                    Kies je team
                </flux:select.option>

                @foreach (\App\Models\HockeyTeam::orderBy('name')->get() as $team)
                    <flux:select.option
                        :value="$team->id"
                        :selected="old('hockey_team_id') == $team->id"
                    >
                        {{ $team->name }}
                    </flux:select.option>
                @endforeach
            </flux:select>

            <flux:input
                name="password"
                :label="__('Wachtwoord')"
                type="password"
                required
                autocomplete="new-password"
                viewable
            />

            <flux:input
                name="password_confirmation"
                :label="__('Herhaal wachtwoord')"
                type="password"
                required
                autocomplete="new-password"
                viewable
            />

            <flux:button
                variant="primary"
                type="submit"
                class="w-full"
            >
                Account aanmaken
            </flux:button>
        </form>

        <p class="text-center text-sm text-zinc-500">
            Heb je al een account?

            <flux:link :href="route('login')" wire:navigate>
                Inloggen
            </flux:link>
        </p>
    </div>
</x-layouts::auth>