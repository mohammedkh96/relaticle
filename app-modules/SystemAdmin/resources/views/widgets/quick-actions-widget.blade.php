<x-filament::widget>
    <x-filament::section>
        <x-slot name="heading">
            Quick Actions
        </x-slot>

        <div class="grid grid-cols-2 gap-4 md:grid-cols-4 lg:grid-cols-5">
            @php
                $user = auth()->user();
            @endphp

            @if($user->role->isSuperAdmin() || $user->hasPermission('create_companies'))
                <x-filament::button tag="a"
                    href="{{ \Relaticle\SystemAdmin\Filament\Resources\CompanyResource::getUrl('create') }}"
                    icon="heroicon-o-building-office" size="lg" color="primary" outlined>
                    New Company
                </x-filament::button>
            @endif

            @if($user->role->isSuperAdmin() || $user->hasPermission('create_people'))
                <x-filament::button tag="a"
                    href="{{ \Relaticle\SystemAdmin\Filament\Resources\PeopleResource::getUrl('create') }}"
                    icon="heroicon-o-user" size="lg" color="primary" outlined>
                    New Person
                </x-filament::button>
            @endif

            @if($user->role->isSuperAdmin() || $user->hasPermission('create_invoices'))
                <x-filament::button tag="a"
                    href="{{ \Relaticle\SystemAdmin\Filament\Resources\InvoiceResource::getUrl('create') }}"
                    icon="heroicon-o-document-currency-dollar" size="lg" color="primary" outlined>
                    New Invoice
                </x-filament::button>
            @endif

            @if($user->role->isSuperAdmin() || $user->hasPermission('create_payments'))
                <x-filament::button tag="a"
                    href="{{ \Relaticle\SystemAdmin\Filament\Resources\PaymentResource::getUrl('create') }}"
                    icon="heroicon-o-banknotes" size="lg" color="primary" outlined>
                    New Payment
                </x-filament::button>
            @endif

            @if($user->role->isSuperAdmin() || $user->hasPermission('create_events'))
                <x-filament::button tag="a"
                    href="{{ \Relaticle\SystemAdmin\Filament\Resources\EventResource::getUrl('create') }}"
                    icon="heroicon-o-calendar" size="lg" color="primary" outlined>
                    New Event
                </x-filament::button>
            @endif

            @if($user->role->isSuperAdmin() || $user->hasPermission('create_opportunities'))
                <x-filament::button tag="a"
                    href="{{ \Relaticle\SystemAdmin\Filament\Resources\OpportunityResource::getUrl('create') }}"
                    icon="heroicon-o-light-bulb" size="lg" color="primary" outlined>
                    New Opportunity
                </x-filament::button>
            @endif

            @if($user->role->isSuperAdmin() || $user->hasPermission('create_tasks'))
                <x-filament::button tag="a"
                    href="{{ \Relaticle\SystemAdmin\Filament\Resources\TaskResource::getUrl('create') }}"
                    icon="heroicon-o-check-circle" size="lg" color="primary" outlined>
                    New Task
                </x-filament::button>
            @endif
        </div>
    </x-filament::section>
</x-filament::widget>