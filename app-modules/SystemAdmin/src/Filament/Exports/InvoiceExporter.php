<?php

declare(strict_types=1);

namespace Relaticle\SystemAdmin\Filament\Exports;

use App\Models\Invoice;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;

class InvoiceExporter extends Exporter
{
    protected static ?string $model = Invoice::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('id')->label('ID'),
            ExportColumn::make('invoice_number')->label('Invoice Number'),
            ExportColumn::make('company.name')->label('Company'),
            ExportColumn::make('event.name')->label('Event'),
            ExportColumn::make('participation.stand_number')->label('Stand Number'),
            ExportColumn::make('issue_date')->label('Issue Date'),
            ExportColumn::make('due_date')->label('Due Date'),
            ExportColumn::make('total_amount')->label('Total Amount'),
            ExportColumn::make('status')->label('Status'),
            ExportColumn::make('notes')->label('Notes'),
            ExportColumn::make('created_at')->label('Created At'),
            ExportColumn::make('updated_at')->label('Updated At'),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        return 'Your invoice export has completed with ' . number_format($export->successful_rows) . ' rows.';
    }
}
