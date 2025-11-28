<?php

declare(strict_types=1);

namespace App\Filament\Pages;

use App\Models\Company;
use App\Models\Event;
use App\Models\Participation;
use App\Models\Visitor;
use Filament\Pages\Dashboard as BaseDashboard;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class Dashboard extends BaseDashboard
{
    public function getWidgets(): array
    {
        return [
            DashboardStatsWidget::class,
        ];
    }
}

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

            Stat::make('Total Visitors', Visitor::count())
                ->description('All visitors')
                ->descriptionIcon('heroicon-o-user-group')
                ->color('info'),

            Stat::make('Current Year Visitors', $currentEvent ? Visitor::where('event_id', $currentEvent->id)->count() : 0)
                ->description("For year {$currentYear}")
                ->descriptionIcon('heroicon-o-users')
                ->color('success'),

            Stat::make('Total Participations', Participation::count())
                ->description('All time')
                ->descriptionIcon('heroicon-o-chart-bar')
                ->color('primary'),
        ];
    }
}
