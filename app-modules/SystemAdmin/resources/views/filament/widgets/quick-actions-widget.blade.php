<x-filament-widgets::widget>
    <x-filament::section>
        <div class="flex items-center gap-x-3 mb-4">
            <h2 class="text-lg font-bold tracking-tight text-gray-950 dark:text-white">
                Quick Actions
            </h2>
        </div>
        <div class="grid grid-cols-2 gap-4 md:grid-cols-4">
            {{-- New Invoice --}}
            <a href="/sysadmin/invoices/create"
                class="flex flex-col items-center justify-center p-4 bg-white dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-800 hover:border-primary-500 dark:hover:border-primary-500 hover:shadow-md transition group text-center">
                <div
                    class="p-3 rounded-full bg-primary-50 dark:bg-primary-900/20 text-primary-600 dark:text-primary-400 mb-3 group-hover:scale-110 transition duration-300">
                    <x-heroicon-o-document-plus class="w-8 h-8" />
                </div>
                <span class="text-sm font-semibold text-gray-700 dark:text-gray-200">New Invoice</span>
                <span class="text-xs text-gray-500 dark:text-gray-400 mt-1">Create a new invoice</span>
            </a>

            {{-- Record Payment --}}
            <a href="/sysadmin/payments/create"
                class="flex flex-col items-center justify-center p-4 bg-white dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-800 hover:border-success-500 dark:hover:border-success-500 hover:shadow-md transition group text-center">
                <div
                    class="p-3 rounded-full bg-success-50 dark:bg-success-900/20 text-success-600 dark:text-success-400 mb-3 group-hover:scale-110 transition duration-300">
                    <x-heroicon-o-currency-dollar class="w-8 h-8" />
                </div>
                <span class="text-sm font-semibold text-gray-700 dark:text-gray-200">Record Payment</span>
                <span class="text-xs text-gray-500 dark:text-gray-400 mt-1">Log a received payment</span>
            </a>

            {{-- Add Company --}}
            <a href="/sysadmin/companies/create"
                class="flex flex-col items-center justify-center p-4 bg-white dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-800 hover:border-info-500 dark:hover:border-info-500 hover:shadow-md transition group text-center">
                <div
                    class="p-3 rounded-full bg-info-50 dark:bg-info-900/20 text-info-600 dark:text-info-400 mb-3 group-hover:scale-110 transition duration-300">
                    <x-heroicon-o-building-office-2 class="w-8 h-8" />
                </div>
                <span class="text-sm font-semibold text-gray-700 dark:text-gray-200">Add Company</span>
                <span class="text-xs text-gray-500 dark:text-gray-400 mt-1">Register new client</span>
            </a>

            {{-- Create Event --}}
            <a href="/sysadmin/events/create"
                class="flex flex-col items-center justify-center p-4 bg-white dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-800 hover:border-warning-500 dark:hover:border-warning-500 hover:shadow-md transition group text-center">
                <div
                    class="p-3 rounded-full bg-warning-50 dark:bg-warning-900/20 text-warning-600 dark:text-warning-400 mb-3 group-hover:scale-110 transition duration-300">
                    <x-heroicon-o-calendar class="w-8 h-8" />
                </div>
                <span class="text-sm font-semibold text-gray-700 dark:text-gray-200">Create Event</span>
                <span class="text-xs text-gray-500 dark:text-gray-400 mt-1">Launch a new expo</span>
            </a>
        </div>
    </x-filament::section>
</x-filament-widgets::widget>