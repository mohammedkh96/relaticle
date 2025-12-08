<x-filament-panels::page>
    <div class="space-y-6">
        {{-- Event Selection Tabs --}}
        <div class="fi-tabs flex flex-wrap gap-3">
            @foreach($this->getEvents() as $event)
                    <button type="button" wire:click="selectEvent({{ $event->id }})"
                        class="fi-tabs-item group flex items-center gap-x-2 rounded-lg px-4 py-3 text-sm font-medium outline-none transition duration-75 
                                {{ $selectedEventId == $event->id
                ? 'fi-active bg-primary-600 text-white shadow-lg'
                : 'bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-700 ring-1 ring-gray-200 dark:ring-gray-700' }}">
                        <x-heroicon-m-calendar class="h-5 w-5" />
                        <span>{{ $event->name }} {{ $event->year }}</span>
                        <span class="inline-flex items-center justify-center rounded-full px-2 py-0.5 text-xs font-medium
                                {{ $selectedEventId == $event->id
                ? 'bg-white/20 text-white'
                : 'bg-primary-100 dark:bg-primary-900 text-primary-600 dark:text-primary-400' }}">
                            {{ $event->participations()->count() }}
                        </span>
                    </button>
            @endforeach
        </div>

        {{-- Participations List --}}
        @if($selectedEventId)
            <div
                class="fi-section rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
                <div class="fi-section-header flex items-center gap-x-3 px-6 py-4">
                    <h3 class="fi-section-heading text-base font-semibold leading-6 text-gray-950 dark:text-white">
                        Participations
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
                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Get started by adding a participation.</p>
                        </div>
                    @else
                        <div class="overflow-x-auto">
                            <table class="w-full divide-y divide-gray-200 dark:divide-gray-700">
                                <thead>
                                    <tr class="bg-gray-50 dark:bg-gray-800">
                                        <th
                                            class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                            Company</th>
                                        <th
                                            class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                            Stand</th>
                                        <th
                                            class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                            Notes</th>
                                        <th
                                            class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                            Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                                    @foreach($this->getParticipations() as $participation)
                                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-800 transition">
                                            <td class="px-4 py-3 whitespace-nowrap">
                                                <div class="font-medium text-gray-900 dark:text-white">
                                                    {{ $participation->company?->name ?? 'N/A' }}</div>
                                                <div class="text-sm text-gray-500 dark:text-gray-400">
                                                    {{ $participation->company?->phone ?? '' }}</div>
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
            <div class="text-center py-16">
                <x-heroicon-o-cursor-arrow-ripple class="mx-auto h-16 w-16 text-gray-400" />
                <h3 class="mt-4 text-lg font-semibold text-gray-900 dark:text-white">Select an Event</h3>
                <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">Click on an event tab above to view its
                    participations.</p>
            </div>
        @endif
    </div>
</x-filament-panels::page>