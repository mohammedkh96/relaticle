<?php

namespace Relaticle\SystemAdmin\Filament\Widgets;

use App\Models\Invoice;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class LatestInvoicesWidget extends BaseWidget
{
    protected static ?int $sort = 3;

    protected int|string|array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Invoice::query()->latest()->limit(5)
            )
            ->columns([
                Tables\Columns\TextColumn::make('invoice_number'),
                Tables\Columns\TextColumn::make('company.name')->label('Company'),
                Tables\Columns\TextColumn::make('total_amount')->money('USD'),
                Tables\Columns\TextColumn::make('status')->badge(),
                Tables\Columns\TextColumn::make('due_date')->date(),
            ])
            ->actions([
                \Filament\Actions\Action::make('print')
                    ->icon('heroicon-o-printer')
                    ->url(fn(Invoice $record) => route('invoice.print', $record))
                    ->openUrlInNewTab(),
            ]);
    }
}
