<?php

declare(strict_types=1);

namespace Relaticle\SystemAdmin\Filament\Resources;

use App\Models\Participation;
use Illuminate\Support\Facades\Cache;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ImportAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Override;
use Relaticle\SystemAdmin\Filament\Resources\ParticipationResource\Pages\CreateParticipation;
use Relaticle\SystemAdmin\Filament\Resources\ParticipationResource\Pages\EditParticipation;
use Relaticle\SystemAdmin\Filament\Resources\ParticipationResource\Pages\ListParticipations;
use Relaticle\SystemAdmin\Filament\Resources\ParticipationResource\Pages\ViewParticipation;
use App\Filament\Actions\SendWhatsAppAction;
use App\Filament\Actions\BulkWhatsAppAction;
use App\Filament\Actions\BulkEmailAction;
use Relaticle\SystemAdmin\Filament\Imports\ParticipationImporter;

final class ParticipationResource extends Resource
{
    protected static ?string $model = Participation::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-building-storefront';

    protected static bool $shouldRegisterNavigation = false;

    protected static string|\UnitEnum|null $navigationGroup = 'Invest Expo';

    protected static ?int $navigationGroupSort = -1;

    protected static ?int $navigationSort = 2;

    protected static ?string $modelLabel = 'Exhibitor';

    protected static ?string $pluralModelLabel = 'Exhibitors';

    public static function getNavigationBadge(): ?string
    {
        return Cache::remember('nav_badge_participation', 300, function () {
            $count = self::getModel()::count();
            return $count > 0 ? (string) $count : null;
        });
    }

    protected static ?string $slug = 'participations';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('company_id')
                    ->relationship('company', 'name')
                    ->required()
                    ->searchable()
                    ->preload()
                    ->createOptionForm([
                        \Filament\Forms\Components\Select::make('team_id')
                            ->relationship('team', 'name')
                            ->required()
                            ->default(fn() => \App\Models\Team::first()?->id),
                        TextInput::make('name')
                            ->required()
                            ->maxLength(255),
                        TextInput::make('address')
                            ->required()
                            ->maxLength(255),
                        TextInput::make('country')
                            ->maxLength(255),
                        TextInput::make('city')
                            ->maxLength(255),
                        TextInput::make('phone')
                            ->tel()
                            ->required()
                            ->maxLength(255),
                        \Filament\Forms\Components\Select::make('category_id')
                            ->relationship('category', 'name', fn($query) => $query->where('is_active', true))
                            ->label('Category')
                            ->searchable()
                            ->preload()
                            ->placeholder('Select category'),
                        \Filament\Forms\Components\Select::make('data_source_id')
                            ->relationship('dataSource', 'name', fn($query) => $query->where('is_active', true))
                            ->label('Data Source')
                            ->searchable()
                            ->preload()
                            ->placeholder('Select data source'),
                    ])
                    ->createOptionModalHeading('Create New Company'),
                Select::make('event_id')
                    ->relationship('event', 'name')
                    ->getOptionLabelFromRecordUsing(fn($record) => "{$record->name} {$record->year}")
                    ->required()
                    ->searchable()
                    ->preload()
                    ->default(fn() => request()->query('event')),
                \Filament\Schemas\Components\Section::make('Financials')
                    ->schema([
                        TextInput::make('booth_size')
                            ->label('Booth Size (sqm)')
                            ->maxLength(255),
                        TextInput::make('booth_price')
                            ->numeric()
                            ->prefix('$')
                            ->default(0),
                        TextInput::make('discount')
                            ->numeric()
                            ->prefix('$')
                            ->default(0),
                        TextInput::make('final_price')
                            ->numeric()
                            ->prefix('$')
                            ->helperText('Calculated manually or automatically')
                            ->default(0),
                        \Filament\Forms\Components\Select::make('participation_status')
                            ->options(\App\Enums\ParticipationStatus::class)
                            ->default(\App\Enums\ParticipationStatus::RESERVED)
                            ->required(),
                    ])->columns(2),
                \Filament\Schemas\Components\Section::make('Documents Confirmation')
                    ->schema([
                        \Filament\Forms\Components\Toggle::make('logo_received')->inline(false),
                        \Filament\Forms\Components\Toggle::make('catalog_received')->inline(false),
                        \Filament\Forms\Components\Toggle::make('badge_names_received')->inline(false),
                    ])->columns(3),
                TextInput::make('stand_number')
                    ->maxLength(255)
                    ->placeholder('e.g., A12, B05'),
                Textarea::make('notes')
                    ->rows(3)
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('company.name')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('event.name')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('event.year')
                    ->sortable()
                    ->label('Year'),
                TextColumn::make('stand_number')
                    ->searchable(),
                TextColumn::make('participation_status')
                    ->badge()
                    ->sortable(),
                \Filament\Tables\Columns\ToggleColumn::make('logo_received')->label('Logo'),
                \Filament\Tables\Columns\ToggleColumn::make('catalog_received')->label('Catalog'),
                \Filament\Tables\Columns\ToggleColumn::make('badge_names_received')->label('Badges'),
                TextColumn::make('notes')
                    ->limit(50)
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
                SelectFilter::make('company_id')
                    ->relationship('company', 'name')
                    ->label('Company')
                    ->searchable()
                    ->preload(),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
                SendWhatsAppAction::make()
                    ->visible(fn($record) => filled($record->company?->phone)),
            ])
            ->toolbarActions([
                ImportAction::make()
                    ->importer(ParticipationImporter::class),
                BulkActionGroup::make([
                    \Filament\Actions\ExportBulkAction::make()
                        ->exporter(\Relaticle\SystemAdmin\Filament\Exports\ParticipationExporter::class),
                    BulkEmailAction::make(),
                    BulkWhatsAppAction::make(),
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
            'index' => ListParticipations::route('/'),
            'create' => CreateParticipation::route('/create'),
            'view' => ViewParticipation::route('/{record}'),
            'edit' => EditParticipation::route('/{record}/edit'),
        ];
    }
}
