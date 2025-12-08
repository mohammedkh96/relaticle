<?php

declare(strict_types=1);

namespace Relaticle\SystemAdmin\Filament\Widgets;

use App\Models\Company;
use App\Models\Event;
use App\Models\Participation;
use App\Models\Visitor;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class DashboardStatsWidget extends StatsOverviewWidget
{
    protected function getStats(): array
    {
        $currentYear = date('Y');
        $currentEvent = Event::where('year', $currentYear)->first();

        return [
            Stat::make('Total Events', Event::count())
                ->description('All events')
                ->descriptionIcon('heroicon-o-calendar')
                ->color('success'),

            Stat::make('Total Companies', Company::count())
                ->description('Registered companies')
                ->descriptionIcon('heroicon-o-building-office')
                ->color('primary'),

            Stat::make('Current Year Participations', $currentEvent ? Participation::where('event_id', $currentEvent->id)->count() : 0)
                ->description("For year {$currentYear}")
                ->descriptionIcon('heroicon-o-building-storefront')
                ->color('warning'),

            Stat::make('Total Revenue', \Filament\Support\format_money(\App\Models\Payment::where('status', \App\Enums\PaymentStatus::PAID)->sum('amount'), 'USD'))
                ->description('Total collected payments')
                ->descriptionIcon('heroicon-o-currency-dollar')
                ->color('success')
                ->chart([7, 2, 10, 3, 15, 4, 17]), // Dummy chart for visuals

            Stat::make('Total Visitors', Visitor::count())
                ->description('All visitors')
                ->descriptionIcon('heroicon-o-user-group')
                ->color('info'),
        ];
    }
}
