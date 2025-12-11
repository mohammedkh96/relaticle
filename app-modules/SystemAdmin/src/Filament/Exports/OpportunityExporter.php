<?php

declare(strict_types=1);

namespace Relaticle\SystemAdmin\Filament\Exports;

use App\Models\Opportunity;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;

class OpportunityExporter extends Exporter
{
    protected static ?string $model = Opportunity::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('id')->label('ID'),
            ExportColumn::make('name')->label('Opportunity Name'),
            ExportColumn::make('company.name')->label('Company'),
            ExportColumn::make('event.name')->label('Event'),
            ExportColumn::make('status')->label('Status'),
            ExportColumn::make('temperature')->label('Temperature'),
            ExportColumn::make('assignee.name')->label('Assigned To'),
            ExportColumn::make('team.name')->label('Team'),
            ExportColumn::make('creation_source')->label('Source'),
            ExportColumn::make('created_at')->label('Created At'),
            ExportColumn::make('updated_at')->label('Updated At'),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        return 'Your opportunity export has completed with ' . number_format($export->successful_rows) . ' rows.';
    }
}
