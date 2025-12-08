<?php

declare(strict_types=1);

namespace Relaticle\SystemAdmin\Filament\Pages;

use App\Models\Event;
use Filament\Pages\Page;

class EventVisitorsPage extends Page
{
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-users';

    protected static string|\UnitEnum|null $navigationGroup = 'Invest Expo';

    protected static ?string $navigationLabel = 'Visitors';

    protected static ?int $navigationSort = 3;

    protected static ?string $slug = 'event-visitors';

    protected string $view = 'filament.pages.event-visitors';

    public ?int $selectedEventId = null;

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

    public function getVisitors()
    {
        if (!$this->selectedEventId) {
            return collect();
        }

        return \App\Models\Visitor::with(['event'])
            ->where('event_id', $this->selectedEventId)
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function selectEvent(int $eventId): void
    {
        $this->selectedEventId = $eventId;
    }

    public static function canViewAny(): bool
    {
        return true;
    }
}
