<x-filament-widgets::widget>
    <x-filament::section>
        <x-slot name="heading">
            Recent Activity
        </x-slot>
        <x-slot name="description">
            Latest changes across the system
        </x-slot>

        <div class="divide-y divide-gray-200 dark:divide-gray-700">
            @forelse($this->getActivities() as $activity)
                <div class="flex items-start gap-4 py-3">
                    <div class="flex-shrink-0">
                        <span class="{{ $this->getActivityColor($activity->event) }}">
                            @svg($this->getActivityIcon($activity->event), 'w-5 h-5')
                        </span>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-gray-900 dark:text-gray-100">
                            {{ class_basename($activity->subject_type ?? 'Unknown') }}
                            <span class="font-normal text-gray-500 dark:text-gray-400">was</span>
                            {{ $activity->event }}
                        </p>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                            by {{ $activity->causer?->name ?? 'System' }}
                            •
                            {{ $activity->created_at->diffForHumans() }}
                        </p>
                    </div>
                </div>
            @empty
                <div class="py-8 text-center text-gray-500 dark:text-gray-400">
                    <x-heroicon-o-clipboard-document-list class="w-12 h-12 mx-auto mb-3 opacity-50" />
                    <p>No recent activity</p>
                </div>
            @endforelse
        </div>

        @if($this->getActivities()->count() > 0)
            <div class="mt-4 pt-4 border-t border-gray-200 dark:border-gray-700">
                <a href="{{ \Relaticle\SystemAdmin\Filament\Resources\ActivityLogResource::getUrl() }}"
                    class="text-sm text-primary-600 hover:text-primary-500 dark:text-primary-400 font-medium">
                    View all activity →
                </a>
            </div>
        @endif
    </x-filament::section>
</x-filament-widgets::widget>