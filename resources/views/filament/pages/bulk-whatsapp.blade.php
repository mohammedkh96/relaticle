<x-filament-panels::page>
    <div class="space-y-6">
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
            <div class="flex items-center gap-3 mb-4">
                <x-heroicon-o-chat-bubble-left-right class="w-8 h-8 text-green-600" />
                <div>
                    <h2 class="text-xl font-semibold text-gray-900 dark:text-white">
                        Bulk WhatsApp Campaign
                    </h2>
                    <p class="text-sm text-gray-600 dark:text-gray-400">
                        Send WhatsApp messages to multiple recipients at once
                    </p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                <div class="bg-blue-50 dark:bg-blue-900/20 rounded-lg p-4">
                    <div class="flex items-center gap-2">
                        <x-heroicon-o-building-office class="w-5 h-5 text-blue-600" />
                        <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Companies</span>
                    </div>
                    <p class="text-2xl font-bold text-blue-600 mt-2">
                        {{ \App\Models\Company::whereNotNull('phone')->count() }}
                    </p>
                    <p class="text-xs text-gray-600 dark:text-gray-400">with phone</p>
                </div>

                <div class="bg-green-50 dark:bg-green-900/20 rounded-lg p-4">
                    <div class="flex items-center gap-2">
                        <x-heroicon-o-user-group class="w-5 h-5 text-green-600" />
                        <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Visitors</span>
                    </div>
                    <p class="text-2xl font-bold text-green-600 mt-2">
                        {{ \App\Models\Visitor::whereNotNull('phone')->count() }}
                    </p>
                    <p class="text-xs text-gray-600 dark:text-gray-400">with phone</p>
                </div>

                <div class="bg-purple-50 dark:bg-purple-900/20 rounded-lg p-4">
                    <div class="flex items-center gap-2">
                        <x-heroicon-o-document-arrow-up class="w-5 h-5 text-purple-600" />
                        <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Import CSV</span>
                    </div>
                    <p class="text-sm text-gray-600 dark:text-gray-400 mt-2">
                        Upload your own contact list
                    </p>
                </div>
            </div>
        </div>

        <form wire:submit="send">
            {{ $this->form }}

            <div class="mt-6">
                <x-filament::actions :actions="$this->getFormActions()" />
            </div>
        </form>

        <div class="bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-lg p-4">
            <div class="flex gap-3">
                <x-heroicon-o-information-circle class="w-5 h-5 text-yellow-600 flex-shrink-0 mt-0.5" />
                <div class="text-sm text-yellow-800 dark:text-yellow-200">
                    <p class="font-semibold mb-1">Important Notes:</p>
                    <ul class="list-disc list-inside space-y-1">
                        <li>Messages are sent immediately (not queued)</li>
                        <li>Rate limiting: 0.2 second delay between messages</li>
                        <li>CSV format: Must have "phone" column, optional "name" column</li>
                        <li>Phone numbers must include country code (e.g., +1234567890)</li>
                        <li>WhatsApp API credentials must be configured in .env</li>
                        <li>Recommended: Send to max 100 recipients at a time</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</x-filament-panels::page>