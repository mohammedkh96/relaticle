<x-filament-panels::page>
    <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
        
        {{-- Visitors Content (Left Side - 3 cols) --}}
        <div class="lg:col-span-3 order-2 lg:order-1">
            @if($selectedEventId)
                <div class="fi-section rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
                    <div class="fi-section-header flex items-center justify-between gap-x-3 px-6 py-4">
                        <h3 class="fi-section-heading text-base font-semibold leading-6 text-gray-950 dark:text-white">
                            Visitors for {{ $this->getEvents()->firstWhere('id', $selectedEventId)?->name }}
                        </h3>
                        <a href="{{ route('filament.sysadmin.resources.visitors.create', ['event' => $selectedEventId]) }}"
                            class="fi-btn fi-btn-size-sm inline-flex items-center justify-center gap-1 rounded-lg px-3 py-2 text-sm font-semibold text-white bg-primary-600 hover:bg-primary-500 transition">
                            <x-heroicon-m-plus class="h-4 w-4" />
                            Add Visitor
                        </a>
                    </div>
                    <div class="fi-section-content px-6 pb-6">
                        @if($this->getVisitors()->isEmpty())
                            <div class="text-center py-12">
                                <x-heroicon-o-users class="mx-auto h-12 w-12 text-gray-400" />
                                <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">No visitors</h3>
                                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Select an event from the list to view visitors.</p>
                            </div>
                        @else
                            <div class="overflow-x-auto">
                                <table class="w-full divide-y divide-gray-200 dark:divide-gray-700">
                                    <thead>
                                        <tr class="bg-gray-50 dark:bg-gray-800">
                                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Name</th>
                                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Email</th>
                                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Phone</th>
                                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Company</th>
                                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                                        @foreach($this->getVisitors() as $visitor)
                                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-800 transition">
                                                <td class="px-4 py-3 whitespace-nowrap">
                                                    <div class="font-medium text-gray-900 dark:text-white">{{ $visitor->name }}</div>
                                                    <div class="text-sm text-gray-500 dark:text-gray-400">{{ $visitor->job_title ?? '' }}</div>
                                                </td>
                                                <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                                    {{ $visitor->email ?? '-' }}
                                                </td>
                                                <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                                    {{ $visitor->phone ?? '-' }}
                                                </td>
                                                <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                                    {{ $visitor->company ?? '-' }}
                                                </td>
                                                <td class="px-4 py-3 whitespace-nowrap">
                                                    <a href="{{ route('filament.sysadmin.resources.visitors.edit', $visitor) }}"
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
                <div class="flex flex-col items-center justify-center p-12 bg-white rounded-xl shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10 h-full min-h-[400px]">
                    <x-heroicon-o-cursor-arrow-ripple class="h-16 w-16 text-gray-400" />
                    <h3 class="mt-4 text-lg font-semibold text-gray-900 dark:text-white">Select an Event</h3>
                    <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">Click on an event from the list on the right to view visitors.</p>
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
                            {{ $event->visitors()->count() }}
                        </span>
                    </button>
                @endforeach
            </div>
        </div>

    </div>
</x-filament-panels::page>