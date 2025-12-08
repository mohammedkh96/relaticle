<?php

declare(strict_types=1);

namespace Relaticle\SystemAdmin\Filament\Resources;

use App\Enums\PaymentMethod;
use App\Enums\PaymentStatus;
use App\Enums\PaymentType;
use App\Models\Payment;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Relaticle\SystemAdmin\Filament\Resources\PaymentResource\Pages\ListPayments;
use Relaticle\SystemAdmin\Filament\Resources\PaymentResource\Pages\CreatePayment;
use Relaticle\SystemAdmin\Filament\Resources\PaymentResource\Pages\EditPayment;

final class PaymentResource extends Resource
{
    protected static ?string $model = Payment::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-banknotes';

    protected static string|\UnitEnum|null $navigationGroup = 'Financials';

    protected static ?int $navigationSort = 1;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('event_id')
                    ->relationship('event', 'name')
                    ->required()
                    ->live(),
                Select::make('participation_id')
                    ->relationship('participation', 'id')
                    ->label('Company / Participation')
                    ->getOptionLabelFromRecordUsing(fn($record) => $record->company->name . ' (' . $record->stand_number . ')')
                    ->searchable()
                    ->preload()
                    ->required()
                    ->visible(fn($get) => filled($get('event_id'))),
                TextInput::make('amount')
                    ->numeric()
                    ->prefix('$')
                    ->required(),
                Select::make('type')
                    ->options(PaymentType::class)
                    ->default(PaymentType::DEPOSIT)
                    ->required(),
                Select::make('method')
                    ->options(PaymentMethod::class)
                    ->default(PaymentMethod::BANK_TRANSFER)
                    ->required(),
                DatePicker::make('payment_date')
                    ->default(now())
                    ->required(),
                Select::make('status')
                    ->options(PaymentStatus::class)
                    ->default(PaymentStatus::PENDING)
                    ->required(),
                TextInput::make('transaction_ref')
                    ->label('Transaction Reference')
                    ->maxLength(255),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('event.name')->sortable(),
                TextColumn::make('participation.company.name')->label('Company')->searchable()->sortable(),
                TextColumn::make('amount')->money('USD')->sortable(),
                TextColumn::make('type')->sortable(),
                TextColumn::make('method')->sortable(),
                TextColumn::make('status')->badge()->sortable(),
                TextColumn::make('payment_date')->date()->sortable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                \Filament\Actions\EditAction::make(),
                \Filament\Actions\Action::make('create_invoice')
                    ->label('Make Invoice')
                    ->icon('heroicon-o-document-plus')
                    ->color('success')
                    ->requiresConfirmation()
                    ->modalHeading('Generate Invoice from Payment')
                    ->modalDescription('This will create a PAID invoice for this payment amount. Continue?')
                    ->action(function (Payment $record) {
                        $participation = $record->participation;

                        $invoice = \App\Models\Invoice::create([
                            'event_id' => $record->event_id,
                            'participation_id' => $record->participation_id,
                            'company_id' => $participation->company_id,
                            'invoice_number' => 'INV-' . strtoupper(uniqid()),
                            'issue_date' => $record->payment_date,
                            'due_date' => $record->payment_date,
                            'total_amount' => $record->amount,
                            'status' => \App\Enums\InvoiceStatus::PAID,
                            'items' => [
                                [
                                    'description' => "Payment Reference: {$record->transaction_ref} ({$record->method->getLabel()})",
                                    'quantity' => 1,
                                    'unit_price' => $record->amount,
                                    'amount' => $record->amount,
                                ]
                            ],
                            'notes' => "Auto-generated from Payment ID: #{$record->id}",
                        ]);

                        \Filament\Notifications\Notification::make()
                            ->title('Invoice Generated')
                            ->success()
                            ->send();

                        return redirect()->to(\Relaticle\SystemAdmin\Filament\Resources\InvoiceResource::getUrl('view', ['record' => $invoice]));
                    }),
            ])
            ->bulkActions([
                \Filament\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListPayments::route('/'),
            'create' => CreatePayment::route('/create'),
            'edit' => EditPayment::route('/{record}/edit'),
        ];
    }
}
