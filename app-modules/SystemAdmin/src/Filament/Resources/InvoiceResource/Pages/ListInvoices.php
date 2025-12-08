<?php

namespace Relaticle\SystemAdmin\Filament\Resources\InvoiceResource\Pages;

use Filament\Resources\Pages\ListRecords;
use Relaticle\SystemAdmin\Filament\Resources\InvoiceResource;

class ListInvoices extends ListRecords
{
    protected static string $resource = InvoiceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            \Filament\Actions\CreateAction::make(),
        ];
    }
}
