<?php

namespace Relaticle\SystemAdmin\Filament\Resources\PaymentResource\Pages;

use Filament\Resources\Pages\ListRecords;
use Relaticle\SystemAdmin\Filament\Resources\PaymentResource;

class ListPayments extends ListRecords
{
    protected static string $resource = PaymentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            \Filament\Actions\ExportAction::make()
                ->exporter(\Relaticle\SystemAdmin\Filament\Exports\PaymentExporter::class)
                ->label('Export'),
            \Filament\Actions\CreateAction::make(),
        ];
    }
}
