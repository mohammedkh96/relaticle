<?php

namespace Relaticle\SystemAdmin\Filament\Resources\InvoiceResource\Pages;

use Filament\Resources\Pages\EditRecord;
use Relaticle\SystemAdmin\Filament\Resources\InvoiceResource;

class EditInvoice extends EditRecord
{
    protected static string $resource = InvoiceResource::class;

    protected function mutateFormDataBeforeFill(array $data): array
    {
        // Extract first item description for simple view
        if (isset($data['items']) && is_array($data['items']) && count($data['items']) > 0) {
            $data['simple_description'] = $data['items'][0]['description'] ?? '';
        }

        return $data;
    }

    protected function mutateFormDataBeforeSave(array $data): array
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
