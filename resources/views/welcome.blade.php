<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>PinoCrew</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="min-h-screen bg-[#071426] text-white antialiased">

    <div class="relative min-h-screen overflow-hidden">

        {{-- Subtle background glow --}}
        <div
            class="pointer-events-none absolute left-1/2 top-[-300px] h-[700px] w-[900px]
                   -translate-x-1/2 rounded-full bg-blue-600/20 blur-[140px]"
        ></div>

        {{-- Navigation --}}
        <header class="relative z-10">
            <div class="mx-auto flex max-w-7xl items-center justify-between px-6 py-7 lg:px-8">

                <a href="{{ route('home') }}" class="flex items-center gap-3">
                    <div class="flex h-19 w-19 items-center justify-center rounded-xl bg-blue-600 font-bold shadow-lg shadow-blue-600/20">
                        <img src="{{ asset('img/logo.png') }}" alt="PinoCrew" class="h-14 w-14" />
                    </div>

                    <span class="text-lg font-semibold tracking-tight">
                        PinoCrew
                    </span>
                </a>

                <div>
                    @auth
                        @if (auth()->user()->currentTeam)
                            <a
                                href="{{ route('dashboard', [
                                    'current_team' => auth()->user()->currentTeam->slug,
                                ]) }}"
                                class="inline-flex items-center rounded-xl bg-white px-4 py-2.5
                                       text-sm font-semibold text-[#071426]
                                       transition hover:bg-blue-50"
                            >
                                Naar dashboard
                            </a>
                        @endif
                    @else
                        <a
                            href="{{ route('login') }}"
                            class="inline-flex items-center rounded-xl border border-white/15
                                   bg-white/5 px-4 py-2.5 text-sm font-medium text-white
                                   backdrop-blur transition hover:bg-white/10"
                        >
                            Inloggen
                        </a>
                    @endauth
                </div>

            </div>
        </header>

        {{-- Hero --}}
        <main class="relative z-10">
            <div class="mx-auto flex min-h-[calc(100vh-170px)] max-w-7xl items-center px-6 pb-24 pt-16 lg:px-8">

                <div class="max-w-4xl">

                    <div class="mb-7 inline-flex items-center gap-2 rounded-full border border-blue-400/20
                                bg-blue-400/10 px-3.5 py-1.5 text-sm font-medium text-blue-200">
                        <span class="h-1.5 w-1.5 rounded-full bg-blue-400"></span>
                        Vrijwilligersplatform van Pinoké
                    </div>

                    <h1 class="max-w-4xl text-5xl font-semibold tracking-[-0.04em] text-white sm:text-6xl lg:text-7xl">
                        Samen maken we
                        <span class="text-blue-400">Pinoké</span>
                        mogelijk.
                    </h1>

                    <p class="mt-7 max-w-2xl text-lg leading-8 text-slate-300 sm:text-xl">
                        Eén centrale plek voor de crew van Pinoké.
                        Bekijk evenementen, schrijf je in voor diensten
                        en blijf op de hoogte van de planning.
                    </p>

                    <div class="mt-10 flex flex-wrap items-center gap-4">

                        @guest
                            <a
                                href="{{ route('login') }}"
                                class="inline-flex items-center gap-2 rounded-xl bg-blue-600
                                       px-5 py-3 text-sm font-semibold text-white
                                       shadow-lg shadow-blue-600/20 transition
                                       hover:bg-blue-500"
                            >
                                Inloggen bij PinoCrew

                                <svg
                                    class="h-4 w-4"
                                    viewBox="0 0 20 20"
                                    fill="none"
                                    stroke="currentColor"
                                    stroke-width="2"
                                >
                                    <path d="M4 10h12M11 5l5 5-5 5"/>
                                </svg>
                            </a>
                        @endguest

                        @auth
                            @if (auth()->user()->currentTeam)
                                <a
                                    href="{{ route('dashboard', [
                                        'current_team' => auth()->user()->currentTeam->slug,
                                    ]) }}"
                                    class="inline-flex items-center gap-2 rounded-xl bg-blue-600
                                           px-5 py-3 text-sm font-semibold text-white
                                           shadow-lg shadow-blue-600/20 transition
                                           hover:bg-blue-500"
                                >
                                    Open PinoCrew

                                    <svg
                                        class="h-4 w-4"
                                        viewBox="0 0 20 20"
                                        fill="none"
                                        stroke="currentColor"
                                        stroke-width="2"
                                    >
                                        <path d="M4 10h12M11 5l5 5-5 5"/>
                                    </svg>
                                </a>
                            @endif
                        @endauth

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