<?php

declare(strict_types=1);

namespace Relaticle\SystemAdmin\Filament\Imports;

use App\Models\Event;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import;

class EventImporter extends Importer
{
    protected static ?string $model = Event::class;

    public static function getColumns(): array
    {
        return [
            ImportColumn::make('name')
                ->requiredMapping()
                ->rules(['required', 'max:255']),
            ImportColumn::make('year')
                ->requiredMapping()
                ->numeric()
                ->rules(['required', 'integer', 'min:2000', 'max:2100']),
            ImportColumn::make('start_date')
                ->rules(['nullable', 'date']),
            ImportColumn::make('end_date')
                ->rules(['nullable', 'date', 'after_or_equal:start_date']),
        ];
    }

    public function resolveRecord(): ?Event
    {
        // Check for existing event by name and year
        return Event::firstOrNew([
            'name' => $this->data['name'],
            'year' => $this->data['year'],
        ]);
    }

    public static function getCompletedNotificationBody(Import $import): string
    {
        $body = 'Your event import has completed and ' . number_format($import->successful_rows) . ' ' . str('row')->plural($import->successful_rows) . ' imported.';

        if ($failedRowsCount = $import->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to import.';
        }

        return $body;
    }
}
