<x-filament-panels::page>
    <div class="space-y-6">
        <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-4">
            <div class="flex gap-3">
                <x-heroicon-o-information-circle class="w-5 h-5 text-blue-600 flex-shrink-0 mt-0.5" />
                <div class="text-sm text-blue-800 dark:text-blue-200">
                    <p class="font-semibold mb-1">Configuration Tips:</p>
                    <ul class="list-disc list-inside space-y-1">
                        <li><strong>Email Tab:</strong> Configure your SMTP server for sending bulk emails</li>
                        <li><strong>WhatsApp Tab:</strong> Set up your Meta WhatsApp Business API credentials</li>
                        <li>Settings are saved to the database and override .env values</li>
                        <li>Make sure to run <code
                                class="bg-blue-100 dark:bg-blue-900 px-1 rounded">php artisan queue:work</code> for
                            messages to send</li>
                    </ul>
                </div>
            </div>
        </div>

        <form wire:submit="save">
            {{ $this->form }}

            <div class="mt-6 flex justify-end">
                <x-filament::button type="submit" size="lg">
                    <x-heroicon-o-check-circle class="w-5 h-5 mr-2" />
                    Save Settings
                </x-filament::button>
            </div>
        </form>
    </div>
</x-filament-panels::page>