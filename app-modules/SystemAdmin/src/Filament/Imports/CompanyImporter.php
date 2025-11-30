<?php

declare(strict_types=1);

namespace Relaticle\SystemAdmin\Filament\Imports;

use App\Models\Company;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import;

class CompanyImporter extends Importer
{
    protected static ?string $model = Company::class;

    public static function getColumns(): array
    {
        return [
            ImportColumn::make('team_id')
                ->label('Team ID')
                ->requiredMapping()
                ->numeric()
                ->rules(['required', 'exists:teams,id']),
            ImportColumn::make('name')
                ->requiredMapping()
                ->rules(['required', 'max:255']),
            ImportColumn::make('phone')
                ->requiredMapping()
                ->rules(['required', 'max:255']),
            ImportColumn::make('address')
                ->requiredMapping()
                ->rules(['required', 'max:255']),
            ImportColumn::make('country')
                ->rules(['nullable', 'max:255']),
            ImportColumn::make('city')
                ->rules(['nullable', 'max:255']),
            ImportColumn::make('account_owner_id')
                ->label('Account Owner ID')
                ->numeric()
                ->rules(['nullable', 'exists:users,id']),
        ];
    }

    public function resolveRecord(): ?Company
    {
        // Check for existing company by name and team
        return Company::firstOrNew([
            'name' => $this->data['name'],
            'team_id' => $this->data['team_id'],
        ]);
    }

    public static function getCompletedNotificationBody(Import $import): string
    {
        $body = 'Your company import has completed and ' . number_format($import->successful_rows) . ' ' . str('row')->plural($import->successful_rows) . ' imported.';

        if ($failedRowsCount = $import->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to import.';
        }

        return $body;
    }
}
