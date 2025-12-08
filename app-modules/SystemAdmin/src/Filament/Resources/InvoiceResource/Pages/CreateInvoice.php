<?php

namespace Relaticle\SystemAdmin\Filament\Resources\InvoiceResource\Pages;

use Filament\Resources\Pages\CreateRecord;
use Relaticle\SystemAdmin\Filament\Resources\InvoiceResource;

class CreateInvoice extends CreateRecord
{
    protected static string $resource = InvoiceResource::class;

    public function mount(): void
    {
        parent::mount();

        if (request()->has('payment_id')) {
            $payment = \App\Models\Payment::with(['participation', 'participation.company'])->find(request('payment_id'));

            if ($payment) {
                // Preserve the auto-generated invoice number
                $invoiceNumber = $this->form->getRawState()['invoice_number'] ?? null;
                // If getRawState is empty, try accessing the data property directly if set by parent mount
                if (!$invoiceNumber && isset($this->data['invoice_number'])) {
                    $invoiceNumber = $this->data['invoice_number'];
                }

                $this->form->fill([
                    'invoice_number' => $invoiceNumber,
                    'event_id' => $payment->event_id,
                    'participation_id' => $payment->participation_id,
                    'company_id' => $payment->participation->company_id,
                    'issue_date' => $payment->payment_date,
                    'total_amount' => $payment->amount,
                    'simple_description' => "Exhibition Space Rental and Participation Fees\nEvent: {$payment->participation->event->name}\nDesignated Stand: {$payment->participation->stand_number}\n\n(Payment Ref: {$payment->transaction_ref})",
                    'notes' => "Linked to Payment #{$payment->id}",
                ]);
            }
        }
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

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
