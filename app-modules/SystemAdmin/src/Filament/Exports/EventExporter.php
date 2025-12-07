<?php

declare(strict_types=1);

namespace Relaticle\SystemAdmin\Filament\Exports;

use App\Models\Event;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;

class EventExporter extends Exporter
{
    protected static ?string $model = Event::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('id')->label('ID'),
            ExportColumn::make('name')->label('Event Name'),
            ExportColumn::make('year')->label('Year'),
            ExportColumn::make('start_date')->label('Start Date'),
            ExportColumn::make('end_date')->label('End Date'),
            ExportColumn::make('participations_count')
                ->label('Participants')
                ->state(fn(Event $record): int => $record->participations()->count()),
            ExportColumn::make('visitors_count')
                ->label('Visitors')
                ->state(fn(Event $record): int => $record->visitors()->count()),
            ExportColumn::make('created_at')->label('Created At'),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        return 'Your event export has completed with ' . number_format($export->successful_rows) . ' rows.';
    }
}
