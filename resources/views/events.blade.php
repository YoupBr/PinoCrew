<x-layouts::app :title="__('Evenementen')">
    <div class="flex h-full w-full flex-1 flex-col gap-6">
        <div>
            <flux:heading size="xl">Evenementen</flux:heading>
            <flux:subheading>
                Bekijk en beheer evenementen van {{ auth()->user()->currentTeam->name }}.
            </flux:subheading>
        </div>
    </div>
</x-layouts::app>