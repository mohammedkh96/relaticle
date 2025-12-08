<?php

declare(strict_types=1);

namespace Relaticle\SystemAdmin\Filament\Pages;

use App\Models\Event;
use App\Models\Participation;
use Filament\Pages\Page;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Tables\Table;
use Filament\Actions\EditAction;
use Filament\Actions\CreateAction;
use Relaticle\SystemAdmin\Filament\Resources\ParticipationResource;

class EventParticipantsPage extends Page implements HasTable, HasForms
{
    use InteractsWithTable;
    use InteractsWithForms;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-building-storefront';

    protected static string|\UnitEnum|null $navigationGroup = 'Invest Expo';

    protected static ?string $navigationLabel = 'Participations';

    protected static ?int $navigationSort = 2;

    protected static ?string $slug = 'event-participants';

    protected string $view = 'filament.pages.event-participants';

    public ?int $selectedEventId = null;

    protected $queryString = [
        'selectedEventId' => ['except' => null, 'as' => 'event_id'],
    ];

    public function mount(): void
    {
        $this->selectedEventId = request()->query('event_id');
    }

    public function getTitle(): string
    {
        if ($this->selectedEventId) {
            $event = Event::find($this->selectedEventId);
            return $event ? "Participations - {$event->name} {$event->year}" : 'Participations';
        }
        return 'Select Event for Participations';
    }

    public function getEvents()
    {
        return Event::orderBy('year', 'desc')->get();
    }

    public function selectEvent(int $eventId): void
    {
        $this->selectedEventId = $eventId;
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Participation::query()
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
                    ->sortable(),
                TextColumn::make('stand_number')
                    ->label('Stand Number')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('notes')
                    ->limit(50)
                    ->tooltip(function (TextColumn $column): ?string {
                        $state = $column->getState();
                        if (strlen($state) <= 50) {
                            return null;
                        }
                        return $state;
                    })
                    ->searchable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                EditAction::make()
                    ->url(fn(Participation $record) => ParticipationResource::getUrl('edit', ['record' => $record])),
            ])
            ->headerActions([
                CreateAction::make('create_participation')
                    ->label('Add Participation')
                    ->url(fn() => ParticipationResource::getUrl('create', ['event' => $this->selectedEventId]))
                    ->hidden(fn() => !$this->selectedEventId),
            ])
            ->bulkActions([
                //
            ]);
    }

    public static function canViewAny(): bool
    {
        return true;
    }
}
