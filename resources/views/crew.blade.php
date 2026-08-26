<x-layouts::app :title="__('Crew')">
    <div class="flex h-full w-full flex-1 flex-col gap-6">
        <div>
            <flux:heading size="xl">Crew</flux:heading>
            <flux:subheading>
                Beheer de crew van {{ auth()->user()->currentTeam->name }}.
            </flux:subheading>
        </div>
    </div>
</x-layouts::app>