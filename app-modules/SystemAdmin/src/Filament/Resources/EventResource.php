<?php

declare(strict_types=1);

namespace Relaticle\SystemAdmin\Filament\Resources;

use App\Models\Event;
use Illuminate\Support\Facades\Cache;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ImportAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Override;
use Relaticle\SystemAdmin\Filament\Resources\EventResource\Pages\CreateEvent;
use Relaticle\SystemAdmin\Filament\Resources\EventResource\Pages\EditEvent;
use Relaticle\SystemAdmin\Filament\Resources\EventResource\Pages\ListEvents;
use Relaticle\SystemAdmin\Filament\Resources\EventResource\Pages\ViewEvent;
use Relaticle\SystemAdmin\Filament\Imports\EventImporter;

final class EventResource extends Resource
{
    protected static ?string $model = Event::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-calendar';

    protected static string|\UnitEnum|null $navigationGroup = 'Invest Expo';

    protected static ?int $navigationGroupSort = -1;

    protected static ?int $navigationSort = 1;

    protected static ?string $modelLabel = 'Event';

    protected static ?string $pluralModelLabel = 'Events';

    public static function getNavigationBadge(): ?string
    {
        return Cache::remember('nav_badge_event', 300, function () {
            $count = self::getModel()::count();
            return $count > 0 ? (string) $count : null;
        });
    }

    protected static ?string $slug = 'events';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                TextInput::make('year')
                    ->required()
                    ->numeric()
                    ->minValue(2000)
                    ->maxValue(2100),
                DatePicker::make('start_date')
                    ->label('Start Date'),
                DatePicker::make('end_date')
                    ->label('End Date'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('year')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('start_date')
                    ->date()
                    ->sortable(),
                TextColumn::make('end_date')
                    ->date()
                    ->sortable(),
                TextColumn::make('participations_count')
                    ->counts('participations')
                    ->label('Companies'),
                TextColumn::make('visitors_count')
                    ->counts('visitors')
                    ->label('Visitors'),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->toolbarActions([
                ImportAction::make()
                    ->importer(EventImporter::class),
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('year', 'desc');
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
            'index' => ListEvents::route('/'),
            'create' => CreateEvent::route('/create'),
            'view' => ViewEvent::route('/{record}'),
            'edit' => EditEvent::route('/{record}/edit'),
        ];
    }
}
