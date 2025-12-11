<?php

declare(strict_types=1);

namespace Relaticle\SystemAdmin\Filament\Pages;

use Filament\Pages\Dashboard as BaseDashboard;

final class Dashboard extends BaseDashboard
{
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-home';

    protected ?string $heading = 'Dashboard';

    protected ?string $subheading = 'Welcome to Invest Expo CRM - Overview of your events and exhibitors';

    protected static ?string $navigationLabel = 'Dashboard';

    public function getColumns(): int|array
    {
        return [
            'default' => 1,
            'sm' => 2,
            'md' => 2,
            'lg' => 2,
            'xl' => 2,
            '2xl' => 2,
        ];
    }

    public function getWidgets(): array
    {
        return [
            \Relaticle\SystemAdmin\Filament\Widgets\DashboardStatsWidget::class,
            \Relaticle\SystemAdmin\Filament\Widgets\RevenueChartWidget::class,
            \Relaticle\SystemAdmin\Filament\Widgets\LatestInvoicesWidget::class,
        ];
    }
}
