<?php

declare(strict_types=1);

namespace Relaticle\SystemAdmin\Filament\Resources;

use App\Enums\CreationSource;
use App\Models\Company;
use Filament\Facades\Filament;
use Illuminate\Support\Facades\Cache;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ImportAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Override;
use App\Filament\Actions\SendWhatsAppAction;
use App\Filament\Actions\BulkEmailAction;
use App\Filament\Actions\BulkWhatsAppAction;
use Relaticle\SystemAdmin\Filament\Resources\CompanyResource\Pages\CreateCompany;
use Relaticle\SystemAdmin\Filament\Resources\CompanyResource\Pages\EditCompany;
use Relaticle\SystemAdmin\Filament\Resources\CompanyResource\Pages\ListCompanies;
use Relaticle\SystemAdmin\Filament\Resources\CompanyResource\Pages\ViewCompany;
use Relaticle\SystemAdmin\Filament\Imports\CompanyImporter;

final class CompanyResource extends Resource
{
    protected static ?string $model = Company::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-building-office';

    protected static string|\UnitEnum|null $navigationGroup = 'CRM';

    protected static ?int $navigationSort = 1;

    protected static ?string $modelLabel = 'Company';

    protected static ?string $pluralModelLabel = 'Companies';

    public static function getNavigationBadge(): ?string
    {
        // Cache for 5 minutes to improve performance
        $tenant = Filament::getTenant();
        $cacheKey = 'nav_badge_company_' . ($tenant?->id ?? 'global');

        return Cache::remember($cacheKey, 300, function () use ($tenant) {
            $query = self::getModel()::query();

            // Scope to current tenant if available
            if ($tenant && method_exists(self::getModel(), 'team')) {
                $query->where('team_id', $tenant->id);
            }

            $count = $query->count();
            return $count > 0 ? (string) $count : null;
        });
    }

    protected static ?string $slug = 'companies';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('team_id')
                    ->relationship('team', 'name')
                    ->required(),
                Select::make('account_owner_id')
                    ->relationship('accountOwner', 'name')
                    ->label('Account Owner')
                    ->searchable()
                    ->preload(),
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
                Select::make('category_id')
                    ->relationship('category', 'name', fn($query) => $query->where('is_active', true))
                    ->label('Category')
                    ->searchable()
                    ->preload()
                    ->placeholder('Select category (e.g., Construction, Architect)')
                    ->createOptionForm([
                        \Filament\Forms\Components\TextInput::make('name')
                            ->required()
                            ->maxLength(255),
                        \Filament\Forms\Components\Textarea::make('description')
                            ->rows(2),
                    ])
                    ->createOptionModalHeading('Create New Category'),
                Select::make('data_source_id')
                    ->relationship('dataSource', 'name', fn($query) => $query->where('is_active', true))
                    ->label('Data Source')
                    ->searchable()
                    ->preload()
                    ->placeholder('Select data source')
                    ->createOptionForm([
                        \Filament\Forms\Components\TextInput::make('name')
                            ->required()
                            ->maxLength(255),
                        \Filament\Forms\Components\Textarea::make('description')
                            ->rows(2),
                    ])
                    ->createOptionModalHeading('Create New Data Source'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('team.name')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('accountOwner.name')
                    ->label('Account Owner')
                    ->sortable()
                    ->searchable()
                    ->toggleable(),
                TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('participation_count')
                    ->label('Events')
                    ->badge()
                    ->color(fn($state) => match (true) {
                        $state >= 5 => 'success',
                        $state >= 3 => 'warning',
                        $state >= 1 => 'info',
                        default => 'gray',
                    })
                    ->sortable(query: function ($query, string $direction) {
                        return $query->withCount('participations')
                            ->orderBy('participations_count', $direction);
                    }),
                TextColumn::make('participation_years_display')
                    ->label('Years Participated')
                    ->wrap()
                    ->searchable(query: function ($query, string $search) {
                        return $query->whereHas('events', function ($q) use ($search) {
                            $q->where('year', 'like', "%{$search}%");
                        });
                    })
                    ->toggleable(),
                TextColumn::make('address')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('country')
                    ->searchable()
                    ->toggleable(),
                TextColumn::make('city')
                    ->searchable()
                    ->toggleable(),
                TextColumn::make('phone')
                    ->searchable(),
                TextColumn::make('deleted_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                SelectFilter::make('creation_source')
                    ->label('Creation Source')
                    ->options(CreationSource::class)
                    ->multiple(),
                SelectFilter::make('participation_year')
                    ->label('Participated in Year')
                    ->options(fn() => \App\Models\Event::orderBy('year', 'desc')
                        ->pluck('year', 'year')
                        ->unique()
                        ->toArray())
                    ->query(fn($query, array $data) => $query->when(
                        $data['value'],
                        fn($q) => $q->whereHas('events', fn($e) => $e->where('year', $data['value']))
                    )),
                SelectFilter::make('participation_count')
                    ->label('Number of Events')
                    ->options([
                        '1' => '1 event',
                        '2' => '2 events',
                        '3' => '3+ events',
                        '5' => '5+ events',
                    ])
                    ->query(fn($query, array $data) => $query->when(
                        $data['value'],
                        fn($q) => $q->has('participations', '>=', (int) $data['value'])
                    )),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
                SendWhatsAppAction::make()
                    ->visible(fn($record) => filled($record->phone)),
            ])
            ->toolbarActions([
                ImportAction::make()
                    ->importer(CompanyImporter::class),
                BulkActionGroup::make([
                    \Filament\Actions\ExportBulkAction::make()
                        ->exporter(\Relaticle\SystemAdmin\Filament\Exports\CompanyExporter::class),
                    BulkEmailAction::make(),
                    BulkWhatsAppAction::make(),
                    DeleteBulkAction::make(),
                ]),
            ]);
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
            'index' => ListCompanies::route('/'),
            'create' => CreateCompany::route('/create'),
            'view' => ViewCompany::route('/{record}'),
            'edit' => EditCompany::route('/{record}/edit'),
        ];
    }
}

