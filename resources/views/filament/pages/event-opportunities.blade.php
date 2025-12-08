<x-filament-panels::page>
    <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">

        {{-- Opportunities Content (Left Side - 3 cols) --}}
        <div class="lg:col-span-3 order-2 lg:order-1">
            @if($selectedEventId)
                {{ $this->table }}
            @else
                <div
                    class="fi-section rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
                    <div class="fi-section-header px-6 py-4 border-b border-gray-200 dark:border-white/10">
                        <h3 class="fi-section-heading text-base font-semibold leading-6 text-gray-950 dark:text-white">
                            Select an Event
                        </h3>
                    </div>
                </div>
                {{-- Shared Event List Table for Empty State --}}
                <div
                    class="fi-section rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10 mt-4">
                    <div class="fi-section-content">
                        <div class="overflow-x-auto">
                            <table class="w-full divide-y divide-gray-200 dark:divide-gray-700">
                                <thead>
                                    <tr class="bg-gray-50 dark:bg-gray-800">
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                            Event Name</th>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                            Year</th>
                                        <th
                                            class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                            Action</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200 dark:divide-gray-700 bg-white dark:bg-gray-900">
                                    @foreach($this->getEvents() as $event)
                                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-800 transition cursor-pointer"
                                            wire:click="selectEvent({{ $event->id }})">
                                            <td
                                                class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">
                                                {{ $event->name }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                                {{ $event->year }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                                <button type="button" wire:click.stop="selectEvent({{ $event->id }})"
                                                    class="text-primary-600 hover:text-primary-900 dark:text-primary-400 dark:hover:text-primary-300">
                                                    View Pipeline
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

        {{-- Events List Sidebar (Right Side - 1 col) --}}
        <div class="lg:col-span-1 order-1 lg:order-2 space-y-4">
            <h3 class="text-lg font-semibold text-gray-950 dark:text-white px-1">Events</h3>
            <div class="flex flex-col space-y-2">
                @foreach($this->getEvents() as $event)
                            <button type="button" wire:click="selectEvent({{ $event->id }})" class="group flex items-center justify-between w-full p-3 text-left rounded-lg transition duration-75 outline-none
                                        {{ $selectedEventId == $event->id
                    ? 'bg-primary-600 text-white shadow-md'
                    : 'bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-700 ring-1 ring-gray-200 dark:ring-gray-700' 
                                        }}">
                                <div class="flex items-center gap-3">
                                    <x-heroicon-m-calendar
                                        class="h-5 w-5 {{ $selectedEventId == $event->id ? 'text-white' : 'text-gray-400 group-hover:text-primary-600' }}" />
                                    <div class="flex flex-col">
                                        <span class="font-medium">{{ $event->name }}</span>
                                        <span
                                            class="text-xs {{ $selectedEventId == $event->id ? 'text-white/80' : 'text-gray-500' }}">{{ $event->year }}</span>
                                    </div>
                                </div>
                            </button>
                @endforeach
            </div>
        </div>

    </div>
</x-filament-panels::page>