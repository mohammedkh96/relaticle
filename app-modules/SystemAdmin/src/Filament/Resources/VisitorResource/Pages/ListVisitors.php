<?php

declare(strict_types=1);

namespace Relaticle\SystemAdmin\Filament\Resources\VisitorResource\Pages;

use App\Models\Event;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Override;
use Relaticle\SystemAdmin\Filament\Resources\VisitorResource;

final class ListVisitors extends ListRecords
{
    protected static string $resource = VisitorResource::class;

    public ?string $eventId = null;

    public function mount(): void
    {
        parent::mount();
        $this->eventId = request()->query('event');
    }

    protected function modifyQueryUsing($query)
    {
        $query = $query->with(['event']);

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
                return "Visitors - {$event->name} {$event->year}";
            }
        }
        return 'Visitors';
    }

    public function getSubheading(): ?string
    {
        if ($this->eventId) {
            $event = Event::find($this->eventId);
            if ($event) {
                return "Registered visitors for {$event->name} {$event->year}";
            }
        }
        return null;
    }

    #[Override]
    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->url(
                    fn() => $this->eventId
                    ? VisitorResource::getUrl('create', ['event' => $this->eventId])
                    : VisitorResource::getUrl('create')
                ),
        ];
    }
}
