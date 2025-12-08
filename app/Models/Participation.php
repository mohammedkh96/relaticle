<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Participation extends Model
{
    use HasFactory;

    protected $table = 'participations';

    protected $fillable = [
        'company_id',
        'event_id',
        'stand_number',
        'notes',
        'booth_size',
        'booth_price',
        'discount',
        'final_price',
        'participation_status',
        'logo_received',
        'catalog_received',
        'badge_names_received',
        'confirmed_at',
        'confirmed_by',
    ];

    protected function casts(): array
    {
        return [
            'booth_price' => 'decimal:2',
            'discount' => 'decimal:2',
            'final_price' => 'decimal:2',
            'participation_status' => \App\Enums\ParticipationStatus::class,
            'logo_received' => 'boolean',
            'catalog_received' => 'boolean',
            'badge_names_received' => 'boolean',
            'confirmed_at' => 'datetime',
        ];
    }

    public function payments(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function invoices(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Invoice::class);
    }

    public function confirmer(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(\Relaticle\SystemAdmin\Models\SystemAdministrator::class, 'confirmed_by');
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }
}
