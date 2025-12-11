<?php

declare(strict_types=1);

namespace Relaticle\SystemAdmin\Filament\Widgets;

use App\Enums\InvoiceStatus;
use App\Models\Invoice;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class LatestInvoicesWidget extends BaseWidget
{
    protected static ?int $sort = 4;

    protected int|string|array $columnSpan = 1;

    protected static ?string $heading = 'Recent Invoices';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Invoice::query()
                    ->with(['company', 'event'])
                    ->latest()
                    ->limit(5)
            )
            ->columns([
                TextColumn::make('invoice_number')
                    ->label('Invoice')
                    ->searchable()
                    ->weight('bold')
                    ->color('primary'),

                TextColumn::make('company.name')
                    ->label('Company')
                    ->limit(20)
                    ->tooltip(fn($record) => $record->company?->name),

                TextColumn::make('total_amount')
                    ->label('Amount')
                    ->money('USD')
                    ->color('success'),

                TextColumn::make('status')
                    ->badge()
                    ->color(fn(InvoiceStatus $state): string => match ($state) {
                        InvoiceStatus::PAID => 'success',
                        InvoiceStatus::SENT => 'info',
                        InvoiceStatus::OVERDUE => 'danger',
                        InvoiceStatus::DRAFT => 'gray',
                        InvoiceStatus::CANCELLED => 'warning',
                    }),

                TextColumn::make('issue_date')
                    ->label('Date')
                    ->date('M d')
                    ->color('gray'),
            ])
            ->paginated(false)
            ->striped();
    }
}
