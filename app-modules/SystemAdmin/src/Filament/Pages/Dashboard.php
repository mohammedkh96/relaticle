<?php

declare(strict_types=1);

namespace Relaticle\SystemAdmin\Filament\Pages;

use Filament\Actions\Action;
use Filament\Pages\Dashboard as BaseDashboard;

final class Dashboard extends BaseDashboard
{
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-home';

    protected ?string $heading = 'Invest Expo CRM Dashboard';

    protected ?string $subheading = 'Manage your events, companies, participations, and visitors.';

    protected static ?string $navigationLabel = 'Dashboard';

    public function getColumns(): int|array
    {
        return 2;
    }

    public function getWidgets(): array
    {
        return [
            \Relaticle\SystemAdmin\Filament\Widgets\QuickActionsWidget::class,
            \Relaticle\SystemAdmin\Filament\Widgets\DashboardStatsWidget::class,
            \Relaticle\SystemAdmin\Filament\Widgets\RevenueChartWidget::class,
            \Relaticle\SystemAdmin\Filament\Widgets\LatestInvoicesWidget::class,
        ];
    }
}
