<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\CreationSource;
use App\Enums\OpportunityStatus;
use App\Enums\OpportunityTemperature;
use App\Models\Concerns\HasCreator;
use App\Models\Concerns\HasNotes;
use App\Models\Concerns\HasTeam;
use App\Observers\OpportunityObserver;
use Database\Factories\OpportunityFactory;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;
use Relaticle\CustomFields\Models\Concerns\UsesCustomFields;
use Relaticle\CustomFields\Models\Contracts\HasCustomFields;
use Relaticle\SystemAdmin\Models\SystemAdministrator;
use Spatie\EloquentSortable\SortableTrait;

/**
 * @property Carbon|null $deleted_at
 * @property CreationSource $creation_source
 * @property OpportunityStatus|null $status
 * @property OpportunityTemperature|null $temperature
 * @property int|null $event_id
 * @property int|null $assigned_to
 */
#[ObservedBy(OpportunityObserver::class)]
final class Opportunity extends Model implements HasCustomFields
{
    use HasCreator;

    /** @use HasFactory<OpportunityFactory> */
    use HasFactory;

    use HasNotes;
    use HasTeam;
    use SoftDeletes;
    use SortableTrait;
    use UsesCustomFields;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'team_id',
        'company_id',
        'contact_id',
        'creation_source',
        'event_id',
        'status',
        'temperature',
        'assigned_to',
    ];

    /**
     * @var array<string, mixed>
     */
    protected $attributes = [
        'creation_source' => CreationSource::WEB,
        'status' => OpportunityStatus::New ,
        'temperature' => OpportunityTemperature::Cold,
    ];

    /**
     * The attributes that should be cast.
     *
     * @return array<string, string|class-string>
     */
    protected function casts(): array
    {
        return [
            'creation_source' => CreationSource::class,
            'status' => OpportunityStatus::class,
            'temperature' => OpportunityTemperature::class,
        ];
    }

    /**
     * @return BelongsTo<Company, $this>
     */
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * @return BelongsTo<People, $this>
     */
    public function contact(): BelongsTo
    {
        return $this->belongsTo(People::class);
    }

    /**
     * @return BelongsTo<Event, $this>
     */
    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    /**
     * @return BelongsTo<SystemAdministrator, $this>
     */
    public function assignee(): BelongsTo
    {
        return $this->belongsTo(\Relaticle\SystemAdmin\Models\SystemAdministrator::class, 'assigned_to');
    }

    /**
     * @return MorphToMany<Task, $this>
     */
    public function tasks(): MorphToMany
    {
        return $this->morphToMany(Task::class, 'taskable');
    }
}
