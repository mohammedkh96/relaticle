<x-filament-panels::page>
    <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
        
        {{-- Participations Content (Left Side - 3 cols) --}}
        <div class="lg:col-span-3 order-2 lg:order-1">
            @if($selectedEventId)
                <div class="fi-section rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
                    <div class="fi-section-header flex items-center justify-between gap-x-3 px-6 py-4">
                        <h3 class="fi-section-heading text-base font-semibold leading-6 text-gray-950 dark:text-white">
                            Participations for {{ $this->getEvents()->firstWhere('id', $selectedEventId)?->name }}
                        </h3>
                        <a href="{{ route('filament.sysadmin.resources.participations.create', ['event' => $selectedEventId]) }}"
                            class="fi-btn fi-btn-size-sm inline-flex items-center justify-center gap-1 rounded-lg px-3 py-2 text-sm font-semibold text-white bg-primary-600 hover:bg-primary-500 transition">
                            <x-heroicon-m-plus class="h-4 w-4" />
                            Add Participation
                        </a>
                    </div>
                    <div class="fi-section-content px-6 pb-6">
                        @if($this->getParticipations()->isEmpty())
                            <div class="text-center py-12">
                                <x-heroicon-o-building-storefront class="mx-auto h-12 w-12 text-gray-400" />
                                <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">No participations</h3>
                                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Select an event from the list to view participations.</p>
                            </div>
                        @else
                            <div class="overflow-x-auto">
                                <table class="w-full divide-y divide-gray-200 dark:divide-gray-700">
                                    <thead>
                                        <tr class="bg-gray-50 dark:bg-gray-800">
                                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Company</th>
                                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Stand</th>
                                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Notes</th>
                                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                                        @foreach($this->getParticipations() as $participation)
                                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-800 transition">
                                                <td class="px-4 py-3 whitespace-nowrap">
                                                    <div class="font-medium text-gray-900 dark:text-white">
                                                        {{ $participation->company?->name ?? 'N/A' }}
                                                    </div>
                                                    <div class="text-sm text-gray-500 dark:text-gray-400">
                                                        {{ $participation->company?->phone ?? '' }}
                                                    </div>
                                                </td>
                                                <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                                    {{ $participation->stand_number ?? '-' }}
                                                </td>
                                                <td class="px-4 py-3 text-sm text-gray-500 dark:text-gray-400 max-w-xs truncate">
                                                    {{ $participation->notes ?? '-' }}
                                                </td>
                                                <td class="px-4 py-3 whitespace-nowrap">
                                                    <a href="{{ route('filament.sysadmin.resources.participations.edit', $participation) }}"
                                                        class="text-primary-600 hover:text-primary-900 dark:text-primary-400 dark:hover:text-primary-300 text-sm font-medium">
                                                        Edit
                                                    </a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endif
                    </div>
                </div>
            @else
                <div class="fi-section rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
                    <div class="fi-section-header px-6 py-4 border-b border-gray-200 dark:border-white/10">
                        <h3 class="fi-section-heading text-base font-semibold leading-6 text-gray-950 dark:text-white">
                            Select an Event
                        </h3>
                    </div>
                    <div class="fi-section-content">
                        <div class="overflow-x-auto">
                            <table class="w-full divide-y divide-gray-200 dark:divide-gray-700">
                                <thead>
                                    <tr class="bg-gray-50 dark:bg-gray-800">
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Event Name</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Year</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Participations</th>
                                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Action</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200 dark:divide-gray-700 bg-white dark:bg-gray-900">
                                    @foreach($this->getEvents() as $event)
                                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-800 transition cursor-pointer" wire:click="selectEvent({{ $event->id }})">
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">
                                                {{ $event->name }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                                {{ $event->year }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                                <span class="inline-flex items-center rounded-md bg-primary-50 px-2 py-1 text-xs font-medium text-primary-700 ring-1 ring-inset ring-primary-700/10 dark:bg-primary-400/10 dark:text-primary-400 dark:ring-primary-400/30">
                                                    {{ $event->participations()->count() }} Participations
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                                <button type="button" wire:click.stop="selectEvent({{ $event->id }})" 
                                                    class="text-primary-600 hover:text-primary-900 dark:text-primary-400 dark:hover:text-primary-300">
                                                    View Details
                                                </button>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            @endif
        </div>

        {{-- Events List (Right Side - 1 col) --}}
        <div class="lg:col-span-1 order-1 lg:order-2 space-y-4">
            <h3 class="text-lg font-semibold text-gray-950 dark:text-white px-1">Events</h3>
            <div class="flex flex-col space-y-2">
                @foreach($this->getEvents() as $event)
                    <button type="button" wire:click="selectEvent({{ $event->id }})"
                        class="group flex items-center justify-between w-full p-3 text-left rounded-lg transition duration-75 outline-none
                            {{ $selectedEventId == $event->id 
                                ? 'bg-primary-600 text-white shadow-md' 
                                : 'bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-700 ring-1 ring-gray-200 dark:ring-gray-700' 
                            }}">
                        <div class="flex items-center gap-3">
                            <x-heroicon-m-calendar class="h-5 w-5 {{ $selectedEventId == $event->id ? 'text-white' : 'text-gray-400 group-hover:text-primary-600' }}" />
                            <div class="flex flex-col">
                                <span class="font-medium">{{ $event->name }}</span>
                                <span class="text-xs {{ $selectedEventId == $event->id ? 'text-white/80' : 'text-gray-500' }}">{{ $event->year }}</span>
                            </div>
                        </div>
                        <span class="inline-flex items-center justify-center rounded-full px-2.5 py-0.5 text-xs font-medium
                            {{ $selectedEventId == $event->id 
                                ? 'bg-white/20 text-white' 
                                : 'bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-300' 
                            }}">
                            {{ $event->participations()->count() }}
                        </span>
                    </button>
                @endforeach
            </div>
        </div>

    </div>
</x-filament-panels::page>