<?php

declare(strict_types=1);

namespace Relaticle\SystemAdmin\Filament\Resources;

use App\Enums\CreationSource;
use App\Filament\Actions\BulkEmailAction;
use App\Filament\Actions\BulkWhatsAppAction;
use App\Models\Task;
use Filament\Facades\Filament;
use Illuminate\Support\Facades\Cache;
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
use Relaticle\SystemAdmin\Filament\Resources\TaskResource\Pages\CreateTask;
use Relaticle\SystemAdmin\Filament\Resources\TaskResource\Pages\EditTask;
use Relaticle\SystemAdmin\Filament\Resources\TaskResource\Pages\ListTasks;
use Relaticle\SystemAdmin\Filament\Resources\TaskResource\Pages\ViewTask;

final class TaskResource extends Resource
{
    protected static ?string $model = Task::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-clipboard-document-check';

    protected static string|\UnitEnum|null $navigationGroup = 'Task Management';

    protected static ?int $navigationSort = 1;

    protected static ?string $modelLabel = 'Task';

    protected static ?string $pluralModelLabel = 'Tasks';

    public static function getNavigationBadge(): ?string
    {
        $tenant = Filament::getTenant();
        $cacheKey = 'nav_badge_task_' . ($tenant?->id ?? 'global');

        return Cache::remember($cacheKey, 300, function () use ($tenant) {
            $query = self::getModel()::query();
            if ($tenant)
                $query->where('team_id', $tenant->id);
            $count = $query->count();
            return $count > 0 ? (string) $count : null;
        });
    }

    protected static ?string $slug = 'tasks';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('team_id')
                    ->relationship('team', 'name')
                    ->required(),
                Select::make('creator_id')
                    ->relationship('creator', 'name')
                    ->label('Creator')
                    ->searchable()
                    ->preload(),
                Select::make('assignees')
                    ->relationship('assignees', 'name')
                    ->multiple()
                    ->searchable()
                    ->preload()
                    ->label('Assignees'),
                TextInput::make('title')
                    ->required()
                    ->maxLength(255),
                TextInput::make('order_column')
                    ->numeric()
                    ->label('Order'),
                Select::make('creation_source')
                    ->options(CreationSource::class)
                    ->default(CreationSource::WEB),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('team.name')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('creator.name')
                    ->label('Creator')
                    ->sortable()
                    ->searchable()
                    ->toggleable(),
                TextColumn::make('assignees.name')
                    ->label('Assignees')
                    ->badge()
                    ->separator(',')
                    ->searchable(),
                TextColumn::make('title')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('order_column')
                    ->label('Order')
                    ->numeric()
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('creation_source')
                    ->badge()
                    ->color(fn(CreationSource $state): string => match ($state) {
                        CreationSource::WEB => 'info',
                        CreationSource::SYSTEM => 'warning',
                        CreationSource::IMPORT => 'success',
                    })
                    ->label('Source')
                    ->toggleable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('deleted_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->headerActions([
                \Filament\Actions\ExportAction::make()
                    ->exporter(\Relaticle\SystemAdmin\Filament\Exports\TaskExporter::class)
                    ->label('Export'),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                SelectFilter::make('assignees')
                    ->relationship('assignees', 'name'),
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
                BulkActionGroup::make([
                    \Filament\Actions\ExportBulkAction::make()
                        ->exporter(\Relaticle\SystemAdmin\Filament\Exports\TaskExporter::class),
                    BulkEmailAction::make(),
                    BulkWhatsAppAction::make(),
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListTasks::route('/'),
            'create' => CreateTask::route('/create'),
            'view' => ViewTask::route('/{record}'),
            'edit' => EditTask::route('/{record}/edit'),
        ];
    }
}


