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

    public function getHeaderActions(): array
    {
        return [
            Action::make('view-site')
                ->label('View Website')
                ->url(config('app.url'))
                ->icon('heroicon-o-globe-alt')
                ->color('gray')
                ->openUrlInNewTab(),
        ];
    }

    public function getWidgets(): array
    {
        return [
            \Relaticle\SystemAdmin\Filament\Widgets\DashboardStatsWidget::class,
        ];
    }
}
