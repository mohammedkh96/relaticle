<?php

declare(strict_types=1);

namespace Relaticle\SystemAdmin\Filament\Pages;

use App\Models\Event;
use App\Models\Opportunity;
use App\Enums\OpportunityStatus;
use App\Enums\OpportunityTemperature;
use Filament\Pages\Page;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\SelectColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Tables\Table;
use Filament\Actions\EditAction;
use Filament\Actions\CreateAction;
use Relaticle\SystemAdmin\Filament\Resources\OpportunityResource;

class EventOpportunitiesPage extends Page implements HasTable, HasForms
{
    use InteractsWithTable;
    use InteractsWithForms;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-currency-dollar';

    protected static string|\UnitEnum|null $navigationGroup = 'Invest Expo';

    protected static ?string $navigationLabel = 'Opportunities';

    protected static ?int $navigationSort = 4;

    protected static ?string $slug = 'event-opportunities';

    protected string $view = 'filament.pages.event-opportunities';

    public ?int $selectedEventId = null;

    protected $queryString = [
        'selectedEventId' => ['except' => null, 'as' => 'event_id'],
    ];

    public function mount(): void
    {
        $id = request()->query('event_id');
        $this->selectedEventId = $id ? (int) $id : null;
    }

    public function getTitle(): string
    {
        if ($this->selectedEventId) {
            $event = Event::find($this->selectedEventId);
            return $event ? "Sales Pipeline - {$event->name} {$event->year}" : 'Sales Pipeline';
        }
        return 'Select Event for Sales Pipeline';
    }

    public function getEvents()
    {
        return Event::orderBy('year', 'desc')->get();
    }

    public function getOpportunities()
    {
        if (!$this->selectedEventId) {
            return collect();
        }

        return Opportunity::with(['company', 'assignee'])
            ->where('event_id', $this->selectedEventId)
            ->latest()
            ->get();
    }

    public function selectEvent(int $eventId): void
    {
        $this->selectedEventId = $eventId;
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Opportunity::query()
                    ->when(
                        $this->selectedEventId,
                        fn($query) => $query->where('event_id', $this->selectedEventId),
                        fn($query) => $query->whereNull('id')
                    )
            )
            ->columns([
                TextColumn::make('company.name')
                    ->label('Company')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                SelectColumn::make('status')
                    ->options(OpportunityStatus::class)
                    ->selectablePlaceholder(false)
                    ->sortable(),

                SelectColumn::make('temperature')
                    ->options(OpportunityTemperature::class)
                    ->sortable(),

                TextColumn::make('assignee.name')
                    ->label('Assigned To')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('created_at')
                    ->label('Created')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                EditAction::make()
                    ->url(fn(Opportunity $record) => OpportunityResource::getUrl('edit', ['record' => $record])),
            ])
            ->headerActions([
                CreateAction::make('create_opportunity')
                    ->label('Add Opportunity')
                    ->url(fn() => OpportunityResource::getUrl('create', ['event_id' => $this->selectedEventId]))
                    ->hidden(fn() => !$this->selectedEventId),
            ])
            ->groups([
                \Filament\Tables\Grouping\Group::make('status')
                    ->collapsible(),
            ])
            ->defaultGroup('status')
            ->bulkActions([
                //
            ]);
    }

    public static function canViewAny(): bool
    {
        return true;
    }
}
