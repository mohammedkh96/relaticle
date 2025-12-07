<?php

declare(strict_types=1);

namespace Relaticle\SystemAdmin\Filament\Exports;

use App\Models\Company;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;

class CompanyExporter extends Exporter
{
    protected static ?string $model = Company::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('id')->label('ID'),
            ExportColumn::make('name')->label('Company Name'),
            ExportColumn::make('phone')->label('Phone'),
            ExportColumn::make('email')->label('Email'),
            ExportColumn::make('address')->label('Address'),
            ExportColumn::make('city')->label('City'),
            ExportColumn::make('country')->label('Country'),
            ExportColumn::make('team.name')->label('Team'),
            ExportColumn::make('accountOwner.name')->label('Account Owner'),
            ExportColumn::make('participation_count')
                ->label('Events Participated')
                ->state(fn(Company $record): int => $record->participations()->count()),
            ExportColumn::make('participation_years_display')
                ->label('Years Participated')
                ->state(fn(Company $record): string => $record->participation_years_display),
            ExportColumn::make('creation_source')->label('Source'),
            ExportColumn::make('created_at')->label('Created At'),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        return 'Your company export has completed with ' . number_format($export->successful_rows) . ' rows.';
    }
}
