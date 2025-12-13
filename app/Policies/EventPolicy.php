<?php

namespace App\Policies;

use App\Models\Event;
use App\Models\User;
use Relaticle\SystemAdmin\Models\SystemAdministrator;
use Illuminate\Auth\Access\HandlesAuthorization;

class EventPolicy
{
    use HandlesAuthorization;

    public function viewAny(SystemAdministrator $user): bool
    {
        return $user->role->isSuperAdmin() || $user->hasPermission('view_events');
    }

    public function view(SystemAdministrator $user, Event $event): bool
    {
        return $user->role->isSuperAdmin() || $user->hasPermission('view_events');
    }

    public function create(SystemAdministrator $user): bool
    {
        return $user->role->canCreate();
    }

    public function update(SystemAdministrator $user, Event $event): bool
    {
        return $user->role->canEdit();
    }

    public function delete(SystemAdministrator $user, Event $event): bool
    {
        return $user->role->canDelete();
    }

    public function deleteAny(SystemAdministrator $user): bool
    {
        return $user->role->canDelete();
    }

    public function restore(SystemAdministrator $user, Event $event): bool
    {
        return $user->role->canDelete();
    }

    public function forceDelete(SystemAdministrator $user, Event $event): bool
    {
        return $user->role->isSuperAdmin();
    }
}
