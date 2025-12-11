<?php

declare(strict_types=1);

namespace Relaticle\SystemAdmin\Filament\Resources;

use App\Enums\PaymentMethod;
use App\Enums\PaymentStatus;
use App\Enums\PaymentType;
use App\Filament\Actions\BulkEmailAction;
use App\Filament\Actions\BulkWhatsAppAction;
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
            ->defaultSort('created_at', 'desc')
            ->headerActions([
                \Filament\Actions\ExportAction::make()
                    ->exporter(\Relaticle\SystemAdmin\Filament\Exports\PaymentExporter::class)
                    ->label('Export'),
            ])
            ->filters([
                \Filament\Tables\Filters\SelectFilter::make('status')
                    ->options(PaymentStatus::class)
                    ->multiple(),
                \Filament\Tables\Filters\SelectFilter::make('type')
                    ->options(PaymentType::class)
                    ->multiple(),
                \Filament\Tables\Filters\SelectFilter::make('method')
                    ->options(PaymentMethod::class)
                    ->multiple(),
                \Filament\Tables\Filters\SelectFilter::make('event_id')
                    ->relationship('event', 'name')
                    ->searchable()
                    ->preload()
                    ->label('Event'),
            ])
            ->actions([
                \Filament\Actions\EditAction::make(),
                \Filament\Actions\Action::make('create_invoice')
                    ->label('Make Invoice')
                    ->icon('heroicon-o-document-plus')
                    ->color('success')
                    ->url(fn(Payment $record) => \Relaticle\SystemAdmin\Filament\Resources\InvoiceResource::getUrl('create', [
                        'payment_id' => $record->id,
                    ])),
            ])
            ->bulkActions([
                \Filament\Actions\ExportBulkAction::make()
                    ->exporter(\Relaticle\SystemAdmin\Filament\Exports\PaymentExporter::class),
                BulkEmailAction::make(),
                BulkWhatsAppAction::make(),
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
