<x-layouts::app :title="__('Diensten')">
    <div class="flex h-full w-full flex-1 flex-col gap-6">
        <div>
            <flux:heading size="xl">Diensten</flux:heading>
            <flux:subheading>
                Bekijk en beheer de diensten van {{ auth()->user()->currentTeam->name }}.
            </flux:subheading>
        </div>
    </div>
</x-layouts::app>