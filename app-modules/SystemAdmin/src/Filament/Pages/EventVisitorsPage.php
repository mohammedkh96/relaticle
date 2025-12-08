<?php

declare(strict_types=1);

namespace Relaticle\SystemAdmin\Filament\Pages;

use App\Models\Event;
use App\Models\Visitor;
use Filament\Pages\Page;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Tables\Table;
use Filament\Actions\EditAction; // Correct namespace for this project
use Filament\Actions\CreateAction; // Correct namespace for this project
use Relaticle\SystemAdmin\Filament\Resources\VisitorResource;

class EventVisitorsPage extends Page implements HasTable, HasForms
{
    use InteractsWithTable;
    use InteractsWithForms;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-users';

    protected static string|\UnitEnum|null $navigationGroup = 'Invest Expo';

    protected static ?string $navigationLabel = 'Visitors';

    protected static ?int $navigationSort = 3;

    protected static ?string $slug = 'event-visitors';

    protected string $view = 'filament.pages.event-visitors';

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
            return $event ? "Visitors - {$event->name} {$event->year}" : 'Visitors';
        }
        return 'Select Event for Visitors';
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
                Visitor::query()
                    ->when(
                        $this->selectedEventId,
                        fn($query) => $query->where('event_id', $this->selectedEventId),
                        fn($query) => $query->whereNull('id') // Start empty if no event selected
                    )
            )
            ->columns([
                TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('job')
                    ->label('Job Title')
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('email')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('phone')
                    ->searchable(),
            ])
            ->filters([
                // Add filters if needed
            ])
            ->actions([
                EditAction::make()
                    ->url(fn(Visitor $record) => VisitorResource::getUrl('edit', ['record' => $record])),
            ])
            ->headerActions([
                CreateAction::make('create_visitor')
                    ->label('Add Visitor')
                    ->url(fn() => VisitorResource::getUrl('create', ['event' => $this->selectedEventId]))
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
