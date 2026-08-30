<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>PinoCrew</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="min-h-screen bg-[#071426] text-white antialiased">

    <div class="relative flex min-h-screen flex-col overflow-hidden">

        <div
            class="pointer-events-none absolute left-1/2 top-[-300px] h-[700px] w-[900px]
                   -translate-x-1/2 rounded-full bg-blue-600/20 blur-[140px]"
        ></div>

        <header class="absolute inset-x-0 top-0 z-20">
    <div class="mx-auto flex max-w-7xl items-center justify-end gap-3 px-6 py-6 lg:px-8">

        @auth
            @if (auth()->user()->currentTeam)
                <a href="{{ route('dashboard', [
                     'current_team' => auth()->user()->currentTeam->slug,
                    ]) }}"
                    class="rounded-lg border border-white/15 bg-white/5 px-4 py-2
                           text-sm font-medium text-white/80 transition
                           hover:bg-white/10 hover:text-white">Beheer</a>
            @endif

            <form method="POST" action="{{ route('logout') }}">
                @csrf

                <button
                    type="submit"
                    class="rounded-lg border border-white/15 bg-white/5 px-4 py-2
                           text-sm font-medium text-white/80 transition
                           hover:bg-white/10 hover:text-white">
                    Uitloggen
                </button>
            </form>
        @else
            <a href="{{ route('login') }}"
                class="rounded-lg border border-white/15 bg-white/5 px-4 py-2
                       text-sm font-medium text-white/80 transition
                       hover:bg-white/10 hover:text-white">Inloggen</a>
        @endauth

    </div>
</header>

    <main class="relative z-10 flex flex-1">
         <div class="mx-auto flex w-full max-w-7xl items-center px-6 py-16 lg:px-8">

           <div class="mx-auto max-w-4xl text-center">

            <a href="{{ route('home') }}" class="mb-8 inline-block">
                <img
                    src="{{ asset('img/logo.png') }}"
                    alt="Pinoké logo"
                    class="mx-auto h-36 w-auto object-contain"/> </a>

            <h1 class="max-w-4xl text-5xl font-semibold tracking-[-0.04em] text-white sm:text-6xl lg:text-7xl">
                Samen makenn we <span class="text-blue-400">Pinoké</span> mogelijk. </h1>

                    <p class="mt-6 text-lg leading-8 text-white/70 sm:text-xl sm:leading-9">
                        De plek voor de vrijwilligers van Pinoké om zich aan te melden voor
                    de opbouw van onze PinokéDome!</p>

                    <div class="mt-10 flex items-center justify-center gap-x-6">

                            <a href="{{ route('signup') }}"
                                class="inline-flex items-center gap-2 rounded-xl bg-blue-600
                                       px-5 py-3 text-sm font-semibold text-white
                                       shadow-lg shadow-blue-600/20 transition
                                       hover:bg-blue-500">
                                Aanmelden voor een dienst

                                <svg class="h-4 w-4"
                                    viewBox="0 0 20 20"
                                    fill="none"
                                    stroke="currentColor"
                                    stroke-width="2" >
                                    <path d="M4 10h12M11 5l5 5-5 5"/>
                                </svg>
                            </a>

                    </div>

                </div>

            </div>
        </main>

        {{-- Footer --}}
        <footer class="relative z-10 border-t border-white/5">
            <div class="mx-auto flex max-w-7xl items-center justify-between px-6 py-6 text-sm text-slate-500 lg:px-8">
                <span>© {{ date('Y') }} Pinoké</span>
                <span>PinoCrew</span>
            </div>
        </footer>

    </div>

</body>
</html>