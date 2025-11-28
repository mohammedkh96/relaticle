<?php

declare(strict_types=1);

namespace Relaticle\SystemAdmin\Filament\Resources;

use App\Models\Visitor;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Override;
use Relaticle\SystemAdmin\Filament\Resources\VisitorResource\Pages\CreateVisitor;
use Relaticle\SystemAdmin\Filament\Resources\VisitorResource\Pages\EditVisitor;
use Relaticle\SystemAdmin\Filament\Resources\VisitorResource\Pages\ListVisitors;
use Relaticle\SystemAdmin\Filament\Resources\VisitorResource\Pages\ViewVisitor;
use App\Filament\Actions\SendWhatsAppAction;

final class VisitorResource extends Resource
{
    protected static ?string $model = Visitor::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-user-group';

    protected static string|\UnitEnum|null $navigationGroup = 'Invest Expo';

    protected static ?int $navigationSort = 3;

    protected static ?string $modelLabel = 'Visitor';

    protected static ?string $pluralModelLabel = 'Visitors';

    public static function getNavigationBadge(): ?string
    {
        return self::getModel()::count() > 0 ? (string) self::getModel()::count() : null;
    }

    protected static ?string $slug = 'visitors';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('event_id')
                    ->relationship('event', 'name')
                    ->required()
                    ->searchable()
                    ->preload(),
                TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                TextInput::make('phone')
                    ->tel()
                    ->maxLength(255),
                TextInput::make('email')
                    ->email()
                    ->maxLength(255),
                TextInput::make('job')
                    ->maxLength(255),
                TextInput::make('country')
                    ->maxLength(255),
                TextInput::make('city')
                    ->maxLength(255),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('event.name')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('event.year')
                    ->sortable()
                    ->label('Year'),
                TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('phone')
                    ->searchable(),
                TextColumn::make('email')
                    ->searchable(),
                TextColumn::make('job')
                    ->searchable()
                    ->toggleable(),
                TextColumn::make('country')
                    ->searchable()
                    ->toggleable(),
                TextColumn::make('city')
                    ->searchable()
                    ->toggleable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('event_id')
                    ->relationship('event', 'name')
                    ->label('Event')
                    ->searchable()
                    ->preload(),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
                SendWhatsAppAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }

    #[Override]
    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    #[Override]
    public static function getPages(): array
    {
        return [
            'index' => ListVisitors::route('/'),
            'create' => CreateVisitor::route('/create'),
            'view' => ViewVisitor::route('/{record}'),
            'edit' => EditVisitor::route('/{record}/edit'),
        ];
    }
}
