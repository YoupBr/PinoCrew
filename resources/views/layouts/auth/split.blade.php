<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
    <head>
        @include('partials.head')
    </head>

    <body class="min-h-screen bg-white antialiased dark:bg-[#071426]">
        <div class="relative grid h-dvh flex-col items-center justify-center px-8 sm:px-0 lg:max-w-none lg:grid-cols-2 lg:px-0">

            {{-- Left panel --}}
            <div class="relative hidden h-full flex-col overflow-hidden p-10 text-white lg:flex">
                <div class="absolute inset-0 bg-[#071426]"></div>

                <div class="absolute -left-32 top-20 h-96 w-96 rounded-full bg-blue-600/20 blur-3xl"></div>
                <div class="absolute bottom-[-120px] right-[-80px] h-[500px] w-[500px] rounded-full bg-blue-500/10 blur-3xl"></div>

                <a  href="{{ route('home') }}"
                    class="relative z-20 flex items-center gap-3 text-lg font-semibold"
                    wire:navigate >
              
                        <img src="{{ asset('img/logo.png') }}" alt="PinoCrew" class="h-12 w-auto" />
                    <span>PinoCrew</span>
                </a>

                <div class="relative z-20 mt-auto max-w-xl">
                    <h1 class="text-4xl font-semibold tracking-tight text-white">
                        Samen maken we Pinoké mogelijk.
                    </h1>

                    <p class="mt-4 max-w-lg text-lg leading-8 text-slate-300">
                        Beheer diensten, bekijk vrijwilligersaanmeldingen en blijf op de hoogte van de planning.
                    </p>
                </div>
            </div>

            {{-- Login panel --}}
            <div class="w-full lg:p-8">
                <div class="mx-auto flex w-full flex-col justify-center space-y-6 sm:w-[390px]">

                    {{ $slot }}
                </div>
            </div>
        </div>

        @persist('toast')
            <flux:toast.group>
                <flux:toast />
            </flux:toast.group>
        @endpersist

        @fluxScripts
    </body>
</html>