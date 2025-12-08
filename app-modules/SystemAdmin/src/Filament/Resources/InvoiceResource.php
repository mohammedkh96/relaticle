<?php

declare(strict_types=1);

namespace Relaticle\SystemAdmin\Filament\Resources;

use App\Enums\InvoiceStatus;
use App\Models\Invoice;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Relaticle\SystemAdmin\Filament\Resources\InvoiceResource\Pages\ListInvoices;
use Relaticle\SystemAdmin\Filament\Resources\InvoiceResource\Pages\CreateInvoice;
use Relaticle\SystemAdmin\Filament\Resources\InvoiceResource\Pages\EditInvoice;
use Relaticle\SystemAdmin\Filament\Resources\InvoiceResource\Pages\ViewInvoice;

final class InvoiceResource extends Resource
{
    protected static ?string $model = Invoice::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-document-text';

    protected static string|\UnitEnum|null $navigationGroup = 'Financials';

    protected static ?int $navigationSort = 2;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                \Filament\Schemas\Components\Section::make('Invoice Details')
                    ->schema([
                        Select::make('event_id')
                            ->relationship('event', 'name')
                            ->required()
                            ->live(),
                        Select::make('participation_id')
                            ->relationship('participation', 'id')
                            ->label('Company / Participation')
                            ->getOptionLabelFromRecordUsing(fn($record) => $record->company->name . ' (' . $record->stand_number . ')')
                            ->required()
                            ->reactive()
                            ->afterStateUpdated(function ($state, $set) {
                                $participation = \App\Models\Participation::with(['company', 'event'])->find($state);
                                if ($participation) {
                                    $set('company_id', $participation->company_id);
                                    $set('total_amount', $participation->final_price);

                                    // Auto-fill description
                                    $desc = "Exhibition Space Rental and Participation Fees\nEvent: {$participation->event->name}\nDesignated Stand: {$participation->stand_number}";
                                    $set('simple_description', $desc);
                                }
                            })
                            ->visible(fn($get) => filled($get('event_id'))),
                        Select::make('company_id')
                            ->relationship('company', 'name')
                            ->disabled()
                            ->dehydrated()
                            ->required(),
                        TextInput::make('invoice_number')
                            ->default(function () {
                                $last = \App\Models\Invoice::latest('id')->first();
                                if ($last && preg_match('/IN-(\d+)/', $last->invoice_number, $matches)) {
                                    $next = (int) $matches[1] + 1;
                                } else {
                                    $next = 1;
                                }
                                return 'IN-' . str_pad((string) $next, 3, '0', STR_PAD_LEFT);
                            })
                            ->required()
                            ->unique(ignoreRecord: true),
                        DatePicker::make('issue_date')
                            ->default(now())
                            ->required(),
                        DatePicker::make('due_date')
                            ->default(now()->addDays(14)),
                        Select::make('status')
                            ->options(InvoiceStatus::class)
                            ->default(InvoiceStatus::DRAFT)
                            ->required(),
                    ])->columns(2),

                \Filament\Schemas\Components\Section::make('Service & Payment')
                    ->description('Automatically filled from participation details.')
                    ->schema([
                        Textarea::make('simple_description')
                            ->label('Service Description')
                            ->required()
                            ->rows(3)
                            ->dehydrated(false) // Handled manually in Pages
                            ->columnSpanFull(),

                        TextInput::make('total_amount')
                            ->label('Total Amount')
                            ->numeric()
                            ->prefix('$')
                            ->required(),

                        Textarea::make('notes')
                            ->label('Notes')
                            ->rows(2)
                            ->columnSpanFull(),
                    ])->columns(1),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('invoice_number')->searchable()->sortable(),
                TextColumn::make('company.name')->label('Company')->searchable()->sortable(),
                TextColumn::make('issue_date')->date()->sortable(),
                TextColumn::make('due_date')->date()->sortable(),
                TextColumn::make('total_amount')->money('USD')->sortable(),
                TextColumn::make('status')->badge()->sortable(),
            ])
            ->filters([
                \Filament\Tables\Filters\SelectFilter::make('status')
                    ->options(InvoiceStatus::class)
                    ->multiple(),
                \Filament\Tables\Filters\SelectFilter::make('event_id')
                    ->relationship('event', 'name')
                    ->searchable()
                    ->preload()
                    ->label('Event'),
            ])
            ->actions([
                \Filament\Actions\ViewAction::make(),
                \Filament\Actions\EditAction::make(),
                \Filament\Actions\Action::make('print')
                    ->label('Print Invoice')
                    ->icon('heroicon-o-printer')
                    ->url(fn(Invoice $record) => route('invoice.print', $record))
                    ->openUrlInNewTab(),
            ])
            ->bulkActions([
                \Filament\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListInvoices::route('/'),
            'create' => CreateInvoice::route('/create'),
            'view' => ViewInvoice::route('/{record}'),
            'edit' => EditInvoice::route('/{record}/edit'),
        ];
    }
}
