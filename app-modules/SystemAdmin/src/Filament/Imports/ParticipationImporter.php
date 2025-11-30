<?php

declare(strict_types=1);

namespace Relaticle\SystemAdmin\Filament\Imports;

use App\Models\Participation;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import;

class ParticipationImporter extends Importer
{
    protected static ?string $model = Participation::class;

    public static function getColumns(): array
    {
        return [
            ImportColumn::make('company_id')
                ->label('Company ID')
                ->requiredMapping()
                ->numeric()
                ->rules(['required', 'exists:companies,id']),
            ImportColumn::make('event_id')
                ->label('Event ID')
                ->requiredMapping()
                ->numeric()
                ->rules(['required', 'exists:events,id']),
            ImportColumn::make('stand_number')
                ->rules(['nullable', 'max:255']),
            ImportColumn::make('notes')
                ->rules(['nullable']),
        ];
    }

    public function resolveRecord(): ?Participation
    {
        // Check for existing participation by company and event
        return Participation::firstOrNew([
            'company_id' => $this->data['company_id'],
            'event_id' => $this->data['event_id'],
        ]);
    }

    public static function getCompletedNotificationBody(Import $import): string
    {
        $body = 'Your participation import has completed and ' . number_format($import->successful_rows) . ' ' . str('row')->plural($import->successful_rows) . ' imported.';

        if ($failedRowsCount = $import->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to import.';
        }

        return $body;
    }
}
