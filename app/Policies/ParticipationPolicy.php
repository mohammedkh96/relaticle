<?php

namespace App\Policies;

use App\Models\Participation;
use Illuminate\Auth\Access\HandlesAuthorization;

use Relaticle\SystemAdmin\Models\SystemAdministrator;

class ParticipationPolicy
{
    use HandlesAuthorization;

    public function viewAny(SystemAdministrator $user): bool
    {
        return $user->role->isSuperAdmin() || $user->hasPermission('view_participations');
    }

    public function view(SystemAdministrator $user, Participation $participation): bool
    {
        return $user->role->isSuperAdmin() || $user->hasPermission('view_participations');
    }

    public function create(SystemAdministrator $user): bool
    {
        return $user->role->isSuperAdmin() || $user->hasPermission('create_participations');
    }

    public function update(SystemAdministrator $user, Participation $participation): bool
    {
        return $user->role->isSuperAdmin() || $user->hasPermission('edit_participations');
    }

    public function delete(SystemAdministrator $user, Participation $participation): bool
    {
        return $user->role->isSuperAdmin() || $user->hasPermission('delete_participations');
    }

    public function deleteAny(SystemAdministrator $user): bool
    {
        return $user->role->isSuperAdmin() || $user->hasPermission('delete_participations');
    }

    public function restore(SystemAdministrator $user, Participation $participation): bool
    {
        return $user->role->isSuperAdmin() || $user->hasPermission('delete_participations');
    }

    public function forceDelete(SystemAdministrator $user, Participation $participation): bool
    {
        return $user->role->isSuperAdmin();
    }
}
