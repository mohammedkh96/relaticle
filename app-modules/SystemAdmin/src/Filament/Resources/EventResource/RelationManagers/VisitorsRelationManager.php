<?php

declare(strict_types=1);

namespace Relaticle\SystemAdmin\Filament\Resources\EventResource\RelationManagers;

use App\Filament\Actions\SendWhatsAppAction;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class VisitorsRelationManager extends RelationManager
{
    protected static string $relationship = 'visitors';

    protected static ?string $title = 'Visitors';

    protected static string|\BackedEnum|null $icon = 'heroicon-o-users';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                TextInput::make('email')
                    ->email()
                    ->maxLength(255),
                TextInput::make('phone')
                    ->tel()
                    ->maxLength(50),
                TextInput::make('job')
                    ->label('Job Title')
                    ->maxLength(255),
                TextInput::make('country')
                    ->maxLength(100),
                TextInput::make('city')
                    ->maxLength(100),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('email')
                    ->searchable()
                    ->toggleable(),
                TextColumn::make('phone')
                    ->searchable(),
                TextColumn::make('job')
                    ->label('Job Title')
                    ->toggleable(),
                TextColumn::make('country')
                    ->toggleable(),
                TextColumn::make('city')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->headerActions([
                CreateAction::make()
                    ->label('Add Visitor'),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
                SendWhatsAppAction::make()
                    ->visible(fn($record) => filled($record->phone)),
            ])
            ->toolbarActions([
                DeleteBulkAction::make(),
            ])
            ->defaultSort('name');
    }
}
