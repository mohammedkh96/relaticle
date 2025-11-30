<?php

declare(strict_types=1);

namespace Relaticle\SystemAdmin\Filament\Imports;

use App\Models\Visitor;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import;

class VisitorImporter extends Importer
{
    protected static ?string $model = Visitor::class;

    public static function getColumns(): array
    {
        return [
            ImportColumn::make('event_id')
                ->label('Event ID')
                ->requiredMapping()
                ->numeric()
                ->rules(['required', 'exists:events,id']),
            ImportColumn::make('name')
                ->requiredMapping()
                ->rules(['required', 'max:255']),
            ImportColumn::make('phone')
                ->rules(['nullable', 'max:255']),
            ImportColumn::make('email')
                ->rules(['nullable', 'email', 'max:255']),
            ImportColumn::make('job')
                ->rules(['nullable', 'max:255']),
            ImportColumn::make('country')
                ->rules(['nullable', 'max:255']),
            ImportColumn::make('city')
                ->rules(['nullable', 'max:255']),
        ];
    }

    public function resolveRecord(): ?Visitor
    {
        // Check for existing visitor by email or phone and event
        if (!empty($this->data['email'])) {
            return Visitor::firstOrNew([
                'email' => $this->data['email'],
                'event_id' => $this->data['event_id'],
            ]);
        }

        if (!empty($this->data['phone'])) {
            return Visitor::firstOrNew([
                'phone' => $this->data['phone'],
                'event_id' => $this->data['event_id'],
            ]);
        }

        // If no email or phone, create new record
        return new Visitor();
    }

    public static function getCompletedNotificationBody(Import $import): string
    {
        $body = 'Your visitor import has completed and ' . number_format($import->successful_rows) . ' ' . str('row')->plural($import->successful_rows) . ' imported.';

        if ($failedRowsCount = $import->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to import.';
        }

        return $body;
    }
}
