<?php

declare(strict_types=1);

namespace Relaticle\SystemAdmin\Filament\Exports;

use App\Models\Note;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;

class NoteExporter extends Exporter
{
    protected static ?string $model = Note::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('id')->label('ID'),
            ExportColumn::make('title')->label('Title'),
            ExportColumn::make('content')->label('Content'),
            ExportColumn::make('creator.name')->label('Created By'),
            ExportColumn::make('team.name')->label('Team'),
            ExportColumn::make('created_at')->label('Created At'),
            ExportColumn::make('updated_at')->label('Updated At'),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        return 'Your note export has completed with ' . number_format($export->successful_rows) . ' rows.';
    }
}
