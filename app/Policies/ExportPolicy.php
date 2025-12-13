<?php

namespace App\Policies;

use Filament\Actions\Exports\Models\Export;
use App\Models\User;
use Relaticle\SystemAdmin\Models\SystemAdministrator;
use Illuminate\Auth\Access\HandlesAuthorization;

class ExportPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view the model.
     */
    public function view($user, Export $export): bool
    {
        // Allow if the user is a System Administrator
        if ($user instanceof SystemAdministrator) {
            return true;
        }

        // Fallback to standard check (ID match)
        return $export->user_id === $user->id;
    }
}
