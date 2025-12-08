<?php

declare(strict_types=1);

namespace Relaticle\SystemAdmin\Filament\Pages;

use App\Models\Event;
use Filament\Pages\Page;

class EventParticipantsPage extends Page
{
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-building-storefront';

    protected static string|\UnitEnum|null $navigationGroup = 'Invest Expo';

    protected static ?string $navigationLabel = 'Participations';

    protected static ?int $navigationSort = 2;

    protected static ?string $slug = 'event-participants';

    protected string $view = 'filament.pages.event-participants';

    public ?int $selectedEventId = null;

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

    public function getParticipations()
    {
        if (!$this->selectedEventId) {
            return collect();
        }

        return \App\Models\Participation::with(['company', 'event'])
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
