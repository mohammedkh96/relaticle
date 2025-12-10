<x-filament-widgets::widget>
    <x-filament::section>
        <div class="flex items-center justify-between mb-2">
            <h2 class="text-base font-semibold tracking-tight text-gray-950 dark:text-white">
                Quick Actions
            </h2>
        </div>
        <div class="grid grid-cols-2 gap-3 md:grid-cols-4">
            {{-- New Invoice --}}
            <a href="/sysadmin/invoices/create"
                class="flex items-center gap-3 p-3 bg-white dark:bg-gray-900 rounded-lg border border-gray-200 dark:border-gray-800 hover:border-primary-500 dark:hover:border-primary-500 hover:bg-gray-50 dark:hover:bg-gray-800 transition group">
                <div
                    class="flex-shrink-0 p-2 rounded-md bg-primary-50 dark:bg-primary-900/20 text-primary-600 dark:text-primary-400 group-hover:scale-105 transition">
                    <x-heroicon-o-document-plus class="w-5 h-5" />
                </div>
                <div class="flex flex-col">
                    <span class="text-sm font-medium text-gray-700 dark:text-gray-200">New Invoice</span>
                </div>
            </a>

            {{-- Record Payment --}}
            <a href="/sysadmin/payments/create"
                class="flex items-center gap-3 p-3 bg-white dark:bg-gray-900 rounded-lg border border-gray-200 dark:border-gray-800 hover:border-success-500 dark:hover:border-success-500 hover:bg-gray-50 dark:hover:bg-gray-800 transition group">
                <div
                    class="flex-shrink-0 p-2 rounded-md bg-success-50 dark:bg-success-900/20 text-success-600 dark:text-success-400 group-hover:scale-105 transition">
                    <x-heroicon-o-currency-dollar class="w-5 h-5" />
                </div>
                <div class="flex flex-col">
                    <span class="text-sm font-medium text-gray-700 dark:text-gray-200">Record Payment</span>
                </div>
            </a>

            {{-- Add Company --}}
            <a href="/sysadmin/companies/create"
                class="flex items-center gap-3 p-3 bg-white dark:bg-gray-900 rounded-lg border border-gray-200 dark:border-gray-800 hover:border-info-500 dark:hover:border-info-500 hover:bg-gray-50 dark:hover:bg-gray-800 transition group">
                <div
                    class="flex-shrink-0 p-2 rounded-md bg-info-50 dark:bg-info-900/20 text-info-600 dark:text-info-400 group-hover:scale-105 transition">
                    <x-heroicon-o-building-office-2 class="w-5 h-5" />
                </div>
                <div class="flex flex-col">
                    <span class="text-sm font-medium text-gray-700 dark:text-gray-200">Add Company</span>
                </div>
            </a>

            {{-- Create Event --}}
            <a href="/sysadmin/events/create"
                class="flex items-center gap-3 p-3 bg-white dark:bg-gray-900 rounded-lg border border-gray-200 dark:border-gray-800 hover:border-warning-500 dark:hover:border-warning-500 hover:bg-gray-50 dark:hover:bg-gray-800 transition group">
                <div
                    class="flex-shrink-0 p-2 rounded-md bg-warning-50 dark:bg-warning-900/20 text-warning-600 dark:text-warning-400 group-hover:scale-105 transition">
                    <x-heroicon-o-calendar class="w-5 h-5" />
                </div>
                <div class="flex flex-col">
                    <span class="text-sm font-medium text-gray-700 dark:text-gray-200">Create Event</span>
                </div>
            </a>
        </div>
    </x-filament::section>
</x-filament-widgets::widget>