<x-filament-panels::page>
    <div class="space-y-6">
        {{-- Header Banner --}}
        <div class="bg-gradient-to-r from-green-500 to-emerald-600 rounded-xl p-6 text-white shadow-lg">
            <div class="flex items-center gap-4">
                <div class="p-3 bg-white/20 rounded-full">
                    <x-heroicon-o-chat-bubble-left-right class="w-8 h-8" />
                </div>
                <div>
                    <h2 class="text-xl font-bold">WhatsApp Cloud API</h2>
                    <p class="text-green-100">Send bulk messages to companies, visitors, or event participants</p>
                </div>
            </div>
        </div>

        {{-- Important Notice --}}
        <div class="bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-700 rounded-lg p-4">
            <div class="flex gap-3">
                <x-heroicon-o-exclamation-triangle class="w-6 h-6 text-amber-500 flex-shrink-0" />
                <div class="text-sm text-amber-800 dark:text-amber-200">
                    <p class="font-medium">Important Notes:</p>
                    <ul class="mt-1 list-disc list-inside space-y-1">
                        <li><strong>Free Text:</strong> Only works if recipient messaged you in the last 24 hours</li>
                        <li><strong>Templates:</strong> Use pre-approved Meta templates for first-time messages</li>
                        <li><strong>Queue:</strong> Messages are sent in background - ensure queue worker is running
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        {{-- Form Card --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
            <div class="p-6">
                {{ $this->form }}
            </div>
            <div
                class="px-6 py-4 bg-gray-50 dark:bg-gray-900 border-t border-gray-200 dark:border-gray-700 rounded-b-xl">
                {{ $this->getFormActions()[0] }}
            </div>
        </div>

        {{-- Help Card --}}
        <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-700 rounded-lg p-4">
            <h3 class="font-medium text-blue-800 dark:text-blue-200 flex items-center gap-2">
                <x-heroicon-o-information-circle class="w-5 h-5" />
                Quick Tips
            </h3>
            <div class="mt-2 text-sm text-blue-700 dark:text-blue-300 space-y-1">
                <p>• <strong>Template Name:</strong> Must match exactly as shown in Meta Business Manager</p>
                <p>• <strong>Parameters:</strong> Add values for {{1}}, {{2}}, etc. in order</p>
                <p>• <strong>Phone Format:</strong> Include country code (e.g., +971501234567)</p>
            </div>
        </div>
    </div>
</x-filament-panels::page>