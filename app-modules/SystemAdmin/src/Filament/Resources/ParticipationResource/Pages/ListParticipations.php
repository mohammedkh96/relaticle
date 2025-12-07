<?php

declare(strict_types=1);

namespace Relaticle\SystemAdmin\Filament\Resources\ParticipationResource\Pages;

use App\Models\Event;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;
use Override;
use Relaticle\SystemAdmin\Filament\Resources\ParticipationResource;

final class ListParticipations extends ListRecords
{
    protected static string $resource = ParticipationResource::class;

    public ?string $eventId = null;

    public function mount(): void
    {
        parent::mount();
        $this->eventId = request()->query('event');
    }

    protected function modifyQueryUsing($query)
    {
        $query = $query->with(['event', 'company']);

        // Filter by event if specified in URL
        if ($this->eventId) {
            $query->where('event_id', $this->eventId);
        }

        return $query;
    }

    public function getTitle(): string
    {
        if ($this->eventId) {
            $event = Event::find($this->eventId);
            if ($event) {
                return "Participations - {$event->name} {$event->year}";
            }
        }
        return 'Participations';
    }

    public function getSubheading(): ?string
    {
        if ($this->eventId) {
            $event = Event::find($this->eventId);
            if ($event) {
                return "Companies participating in {$event->name} {$event->year}";
            }
        }
        return null;
    }

    #[Override]
    protected function getHeaderActions(): array
    {
        $actions = [
            CreateAction::make()
                ->url(
                    fn() => $this->eventId
                    ? ParticipationResource::getUrl('create', ['event' => $this->eventId])
                    : ParticipationResource::getUrl('create')
                ),
        ];

        return $actions;
    }
}
