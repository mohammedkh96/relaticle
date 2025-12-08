<x-filament-widgets::widget>
    <x-filament::section>
        <div class="flex items-center gap-x-3">
            <h2 class="text-lg font-bold tracking-tight text-gray-950 dark:text-white">
                Quick Actions
            </h2>
        </div>
        <div class="mt-4 grid grid-cols-2 gap-4 md:grid-cols-4 lg:grid-cols-6">
            <x-filament::button href="/admin/invoices/create" tag="a" icon="heroicon-o-document-plus" color="primary"
                outlined>
                New Invoice
            </x-filament::button>

            <x-filament::button href="/admin/payments/create" tag="a" icon="heroicon-o-currency-dollar" color="success"
                outlined>
                Record Payment
            </x-filament::button>

            <x-filament::button href="/admin/companies/create" tag="a" icon="heroicon-o-building-office-2" color="info"
                outlined>
                Add Company
            </x-filament::button>

            <x-filament::button href="/admin/events/create" tag="a" icon="heroicon-o-calendar" color="warning" outlined>
                Create Event
            </x-filament::button>
        </div>
    </x-filament::section>
</x-filament-widgets::widget>