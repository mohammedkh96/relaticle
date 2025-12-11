<?php

declare(strict_types=1);

namespace Relaticle\SystemAdmin\Filament\Resources;

use App\Models\DataSource;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Override;
use Relaticle\SystemAdmin\Filament\Resources\DataSourceResource\Pages\CreateDataSource;
use Relaticle\SystemAdmin\Filament\Resources\DataSourceResource\Pages\EditDataSource;
use Relaticle\SystemAdmin\Filament\Resources\DataSourceResource\Pages\ListDataSources;

final class DataSourceResource extends Resource
{
    protected static ?string $model = DataSource::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-circle-stack';

    protected static string|\UnitEnum|null $navigationGroup = 'Content';

    protected static ?int $navigationSort = 2;

    protected static ?string $modelLabel = 'Data Source';

    protected static ?string $pluralModelLabel = 'Data Sources';

    protected static ?string $slug = 'data-sources';

    // Bypass strict authorization for this resource
    public static function canViewAny(): bool
    {
        return true;
    }

    public static function canCreate(): bool
    {
        return true;
    }

    public static function canEdit($record): bool
    {
        return true;
    }

    public static function canDelete($record): bool
    {
        return true;
    }

    public static function canView($record): bool
    {
        return true;
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required()
                    ->maxLength(255)
                    ->placeholder('e.g., Website, Exhibition, Referral, Import'),
                Textarea::make('description')
                    ->rows(2)
                    ->maxLength(500)
                    ->placeholder('Optional description'),
                Toggle::make('is_active')
                    ->label('Active')
                    ->default(true)
                    ->helperText('Inactive sources won\'t appear in dropdowns'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('description')
                    ->limit(50)
                    ->toggleable(),
                TextColumn::make('companies_count')
                    ->counts('companies')
                    ->label('Companies')
                    ->sortable(),
                IconColumn::make('is_active')
                    ->boolean()
                    ->label('Active'),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->headerActions([
                \Filament\Actions\ExportAction::make()
                    ->exporter(\Relaticle\SystemAdmin\Filament\Exports\DataSourceExporter::class)
                    ->label('Export'),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                \Filament\Actions\ExportBulkAction::make()
                    ->exporter(\Relaticle\SystemAdmin\Filament\Exports\DataSourceExporter::class),
                DeleteBulkAction::make(),
            ])
            ->defaultSort('name');
    }

    #[Override]
    public static function getRelations(): array
    {
        return [];
    }

    #[Override]
    public static function getPages(): array
    {
        return [
            'index' => ListDataSources::route('/'),
            'create' => CreateDataSource::route('/create'),
            'edit' => EditDataSource::route('/{record}/edit'),
        ];
    }
}
