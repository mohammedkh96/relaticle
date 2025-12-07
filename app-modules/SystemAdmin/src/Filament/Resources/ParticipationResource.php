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
use Relaticle\SystemAdmin\Filament\Imports\ParticipationImporter;

final class ParticipationResource extends Resource
{
    protected static ?string $model = Participation::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-building-storefront';

    protected static string|\UnitEnum|null $navigationGroup = 'Invest Expo';

    protected static ?int $navigationGroupSort = -1;

    protected static ?int $navigationSort = 2;

    protected static ?string $modelLabel = 'Participation';

    protected static ?string $pluralModelLabel = 'Participations';

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
                    ->preload(),
                Select::make('event_id')
                    ->relationship('event', 'name')
                    ->required()
                    ->searchable()
                    ->preload(),
                TextInput::make('stand_number')
                    ->maxLength(255),
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
