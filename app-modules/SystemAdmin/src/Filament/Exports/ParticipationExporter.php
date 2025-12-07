<?php

declare(strict_types=1);

namespace Relaticle\SystemAdmin\Filament\Exports;

use App\Models\Participation;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;

class ParticipationExporter extends Exporter
{
    protected static ?string $model = Participation::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('id')->label('ID'),
            ExportColumn::make('company.name')->label('Company'),
            ExportColumn::make('company.phone')->label('Company Phone'),
            ExportColumn::make('company.email')->label('Company Email'),
            ExportColumn::make('event.name')->label('Event'),
            ExportColumn::make('event.year')->label('Year'),
            ExportColumn::make('stand_number')->label('Stand Number'),
            ExportColumn::make('notes')->label('Notes'),
            ExportColumn::make('created_at')->label('Created At'),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        return 'Your participation export has completed with ' . number_format($export->successful_rows) . ' rows.';
    }
}
