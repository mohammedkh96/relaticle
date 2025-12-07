<?php

declare(strict_types=1);

namespace Relaticle\SystemAdmin\Filament\Exports;

use App\Models\Visitor;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;

class VisitorExporter extends Exporter
{
    protected static ?string $model = Visitor::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('id')->label('ID'),
            ExportColumn::make('name')->label('Name'),
            ExportColumn::make('email')->label('Email'),
            ExportColumn::make('phone')->label('Phone'),
            ExportColumn::make('company')->label('Company'),
            ExportColumn::make('job_title')->label('Job Title'),
            ExportColumn::make('event.name')->label('Event'),
            ExportColumn::make('event.year')->label('Year'),
            ExportColumn::make('created_at')->label('Registered At'),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        return 'Your visitor export has completed with ' . number_format($export->successful_rows) . ' rows.';
    }
}
