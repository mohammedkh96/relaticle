<?php

declare(strict_types=1);

namespace Relaticle\SystemAdmin\Filament\Resources\EventResource\RelationManagers;

use App\Filament\Actions\SendWhatsAppAction;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ParticipationsRelationManager extends RelationManager
{
    protected static string $relationship = 'participations';

    protected static ?string $title = 'Companies Participating';

    protected static string|\BackedEnum|null $icon = 'heroicon-o-building-office';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('company_id')
                    ->relationship('company', 'name')
                    ->searchable()
                    ->preload()
                    ->required()
                    ->createOptionForm([
                        TextInput::make('name')->required(),
                        TextInput::make('phone')->tel(),
                        TextInput::make('address'),
                        TextInput::make('country'),
                        TextInput::make('city'),
                    ]),
                TextInput::make('stand_number')
                    ->label('Stand Number')
                    ->placeholder('e.g., A12, B05'),
                Textarea::make('notes')
                    ->rows(3),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('company.name')
                    ->label('Company')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('company.phone')
                    ->label('Phone')
                    ->searchable(),
                TextColumn::make('stand_number')
                    ->label('Stand')
                    ->searchable(),
                TextColumn::make('notes')
                    ->limit(30)
                    ->toggleable(),
            ])
            ->headerActions([
                CreateAction::make()
                    ->label('Add Company'),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
                SendWhatsAppAction::make()
                    ->visible(fn($record) => filled($record->company?->phone)),
            ])
            ->toolbarActions([
                DeleteBulkAction::make(),
            ])
            ->defaultSort('company.name');
    }
}
