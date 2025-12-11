<div class="space-y-4">
    <div class="grid grid-cols-2 gap-4">
        <div>
            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">User</p>
            <p class="text-sm text-gray-900 dark:text-gray-100">{{ $activity->causer?->name ?? 'System' }}</p>
        </div>
        <div>
            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Date & Time</p>
            <p class="text-sm text-gray-900 dark:text-gray-100">{{ $activity->created_at->format('M d, Y H:i:s') }}</p>
        </div>
        <div>
            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Model</p>
            <p class="text-sm text-gray-900 dark:text-gray-100">
                {{ class_basename($activity->subject_type ?? 'Unknown') }}</p>
        </div>
        <div>
            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Record ID</p>
            <p class="text-sm text-gray-900 dark:text-gray-100">{{ $activity->subject_id ?? '-' }}</p>
        </div>
    </div>

    @if($activity->description)
        <div>
            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Description</p>
            <p class="text-sm text-gray-900 dark:text-gray-100">{{ $activity->description }}</p>
        </div>
    @endif

    @if(!empty($activity->properties) && (isset($activity->properties['attributes']) || isset($activity->properties['old'])))
        <div class="mt-4 pt-4 border-t border-gray-200 dark:border-gray-700">
            <p class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-3">Changes</p>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700 text-sm">
                    <thead>
                        <tr>
                            <th class="px-3 py-2 text-left font-medium text-gray-500 dark:text-gray-400">Field</th>
                            <th class="px-3 py-2 text-left font-medium text-gray-500 dark:text-gray-400">Old Value</th>
                            <th class="px-3 py-2 text-left font-medium text-gray-500 dark:text-gray-400">New Value</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                        @foreach($activity->properties['attributes'] ?? [] as $key => $newValue)
                            @php
                                $oldValue = $activity->properties['old'][$key] ?? '-';
                            @endphp
                            <tr>
                                <td class="px-3 py-2 font-medium text-gray-900 dark:text-gray-100">
                                    {{ ucwords(str_replace('_', ' ', $key)) }}</td>
                                <td class="px-3 py-2 text-red-600 dark:text-red-400">
                                    {{ is_array($oldValue) ? json_encode($oldValue) : $oldValue }}</td>
                                <td class="px-3 py-2 text-green-600 dark:text-green-400">
                                    {{ is_array($newValue) ? json_encode($newValue) : $newValue }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif
</div>