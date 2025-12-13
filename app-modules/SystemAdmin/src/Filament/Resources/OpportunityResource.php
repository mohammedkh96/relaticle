<?php

declare(strict_types=1);

namespace Relaticle\SystemAdmin\Filament\Resources;

use App\Enums\CreationSource;
use App\Filament\Actions\BulkEmailAction;
use App\Filament\Actions\BulkWhatsAppAction;
use App\Models\Opportunity;
use Filament\Facades\Filament;
use Illuminate\Support\Facades\Cache;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\Select;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Override;
use Relaticle\SystemAdmin\Filament\Resources\OpportunityResource\Pages\CreateOpportunity;
use Relaticle\SystemAdmin\Filament\Resources\OpportunityResource\Pages\EditOpportunity;
use Relaticle\SystemAdmin\Filament\Resources\OpportunityResource\Pages\ListOpportunities;
use Relaticle\SystemAdmin\Filament\Resources\OpportunityResource\Pages\ViewOpportunity;

final class OpportunityResource extends Resource
{
    protected static ?string $model = Opportunity::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-currency-dollar';

    protected static string|\UnitEnum|null $navigationGroup = 'CRM';

    protected static ?int $navigationSort = 3;

    protected static ?string $modelLabel = 'Opportunity';

    protected static ?string $pluralModelLabel = 'Opportunities';

    public static function getNavigationBadge(): ?string
    {
        $tenant = Filament::getTenant();
        $cacheKey = 'nav_badge_opportunity_' . ($tenant?->id ?? 'global');

        return Cache::remember($cacheKey, 300, function () use ($tenant) {
            $query = self::getModel()::query();
            if ($tenant)
                $query->where('team_id', $tenant->id);
            $count = $query->count();
            return $count > 0 ? (string) $count : null;
        });
    }

    protected static ?string $slug = 'opportunities';

    #[Override]
    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                \Filament\Schemas\Components\Section::make()
                    ->schema([
                        \Filament\Forms\Components\TextInput::make('name')
                            ->required()
                            ->maxLength(255)
                            ->columnSpanFull(),
                        Select::make('company_id')
                            ->relationship('company', 'name')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->rules([
                                fn($get, ?\Illuminate\Database\Eloquent\Model $record) => function (string $attribute, $value, \Closure $fail) use ($get, $record) {
                                    $eventId = $get('event_id');
                                    if (!$eventId)
                                        return;

                                    $query = \App\Models\Opportunity::where('event_id', $eventId)
                                        ->where('company_id', $value);

                                    if ($record) {
                                        $query->where('id', '!=', $record->id);
                                    }

                                    $existing = $query->with('assignee')->first();

                                    if ($existing) {
                                        $assigneeName = $existing->assignee?->name ?? 'Unknown';
                                        $fail("This company is already being managed by {$assigneeName}.");
                                    }
                                },
                            ]),
                        Select::make('event_id')
                            ->relationship('event', 'name')
                            ->default(request()->query('event_id'))
                            ->searchable(),
                        Select::make('status')
                            ->options(\App\Enums\OpportunityStatus::class)
                            ->default(\App\Enums\OpportunityStatus::New)
                            ->required(),
                        Select::make('temperature')
                            ->options(\App\Enums\OpportunityTemperature::class)
                            ->default(\App\Enums\OpportunityTemperature::Cold)
                            ->required(),
                        Select::make('assigned_to')
                            ->relationship('assignee', 'name')
                            ->searchable()
                            ->preload(),
                        Select::make('team_id')
                            ->relationship('team', 'name')
                            ->required()
                            ->default(auth()->user()->team_id ?? optional(\App\Models\Team::first())->id),
                        Select::make('creation_source')
                            ->options(CreationSource::class)
                            ->default(CreationSource::WEB)
                            ->disabled()
                            ->dehydrated(),
                    ])
                    ->columns(2),
            ]);
    }

    #[Override]
    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),
                TextColumn::make('company.name')
                    ->label('Company')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('status')
                    ->badge()
                    ->sortable(),
                TextColumn::make('temperature')
                    ->badge()
                    ->sortable(),
                TextColumn::make('assignee.name')
                    ->label('Assigned To')
                    ->toggleable(),
                TextColumn::make('team.name')
                    ->numeric()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('creation_source')
                    ->badge()
                    ->color(fn(CreationSource $state): string => match ($state) {
                        CreationSource::WEB => 'info',
                        CreationSource::SYSTEM => 'warning',
                        CreationSource::IMPORT => 'success',
                    })
                    ->label('Source')
                    ->toggleable(isToggledHiddenByDefault: true),
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
                SelectFilter::make('status')
                    ->options(\App\Enums\OpportunityStatus::class)
                    ->multiple(),
                SelectFilter::make('temperature')
                    ->options(\App\Enums\OpportunityTemperature::class)
                    ->multiple(),
                SelectFilter::make('event_id')
                    ->relationship('event', 'name')
                    ->label('Event')
                    ->searchable()
                    ->preload(),
                SelectFilter::make('assigned_to')
                    ->relationship('assignee', 'name')
                    ->label('Assigned To')
                    ->searchable()
                    ->preload(),
                SelectFilter::make('creation_source')
                    ->label('Creation Source')
                    ->options(CreationSource::class)
                    ->multiple(),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->toolbarActions([
                \Filament\Actions\ExportAction::make()
                    ->exporter(\Relaticle\SystemAdmin\Filament\Exports\OpportunityExporter::class)
                    ->label('Export'),
                BulkActionGroup::make([
                    \Filament\Actions\ExportBulkAction::make()
                        ->exporter(\Relaticle\SystemAdmin\Filament\Exports\OpportunityExporter::class),
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
            'index' => ListOpportunities::route('/'),
            'create' => CreateOpportunity::route('/create'),
            'view' => ViewOpportunity::route('/{record}'),
            'edit' => EditOpportunity::route('/{record}/edit'),
        ];
    }
}


