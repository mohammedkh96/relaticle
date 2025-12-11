<?php

declare(strict_types=1);

namespace Relaticle\SystemAdmin\Filament\Exports;

use App\Models\Payment;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;

class PaymentExporter extends Exporter
{
    protected static ?string $model = Payment::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('id')->label('ID'),
            ExportColumn::make('event.name')->label('Event'),
            ExportColumn::make('participation.company.name')->label('Company'),
            ExportColumn::make('participation.stand_number')->label('Stand Number'),
            ExportColumn::make('amount')->label('Amount'),
            ExportColumn::make('type')->label('Payment Type'),
            ExportColumn::make('method')->label('Payment Method'),
            ExportColumn::make('status')->label('Status'),
            ExportColumn::make('transaction_ref')->label('Transaction Ref'),
            ExportColumn::make('payment_date')->label('Payment Date'),
            ExportColumn::make('receiver.name')->label('Received By'),
            ExportColumn::make('created_at')->label('Created At'),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        return 'Your payment export has completed with ' . number_format($export->successful_rows) . ' rows.';
    }
}
