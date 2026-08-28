<x-layouts::auth :title="__('Inloggen')">
    <div class="flex flex-col gap-6">
        <div class="text-center">
            <div class="mb-4 text-sm font-medium text-blue-600">
                PinoCrew Beheer
            </div>

            <x-auth-header
                :title="__('Welkom terug')"
                :description="__('Log in om PinoCrew te beheren.')"
            />
        </div>

        <x-auth-session-status
            class="text-center"
            :status="session('status')"
        />

        <form
            method="POST"
            action="{{ route('login.store') }}"
            class="flex flex-col gap-5"
        >
            @csrf

            <flux:input
                name="email"
                :label="__('E-mailadres')"
                :value="old('email')"
                type="email"
                required
                autofocus
                autocomplete="email"
                placeholder="naam@pinoke.nl"
            />

            <div class="relative">
                <flux:input
                    name="password"
                    :label="__('Wachtwoord')"
                    type="password"
                    required
                    autocomplete="current-password"
                    :placeholder="__('Wachtwoord')"
                    viewable
                />

                @if (Route::has('password.request'))
                    <flux:link
                        class="absolute top-0 end-0 text-sm"
                        :href="route('password.request')"
                        wire:navigate
                    >
                        {{ __('Wachtwoord vergeten?') }}
                    </flux:link>
                @endif
            </div>

            <flux:checkbox
                name="remember"
                :label="__('Ingelogd blijven')"
                :checked="old('remember')"
            />

            <flux:button
                variant="primary"
                type="submit"
                class="w-full"
                data-test="login-button"
            >
                {{ __('Inloggen') }}
            </flux:button>
        </form>

        <div class="border-t border-zinc-200 pt-5 text-center dark:border-zinc-800">
            <p class="text-sm text-zinc-500">
                Wil je je aanmelden voor een vrijwilligersdienst?
            </p>

            <flux:link
                :href="route('signup')"
                wire:navigate
                class="mt-1 inline-block"
            >
                Naar inschrijven
            </flux:link>
        </div>
    </div>
</x-layouts::auth>