<?php

namespace Relaticle\SystemAdmin\Filament\Resources\InvoiceResource\Pages;

use Filament\Resources\Pages\CreateRecord;
use Relaticle\SystemAdmin\Filament\Resources\InvoiceResource;

class CreateInvoice extends CreateRecord
{
    protected static string $resource = InvoiceResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Transform simple description/amount into items JSON structure
        if (isset($data['simple_description'])) {
            $data['items'] = [
                [
                    'description' => $data['simple_description'],
                    'quantity' => 1,
                    'unit_price' => $data['total_amount'],
                    'amount' => $data['total_amount'],
                ]
            ];
            unset($data['simple_description']);
        }

        return $data;
    }
}
