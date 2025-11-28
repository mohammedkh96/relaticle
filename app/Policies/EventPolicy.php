<?php

namespace App\Policies;

use App\Models\Event;
use App\Models\User;
use Relaticle\SystemAdmin\Models\SystemAdministrator;
use Illuminate\Auth\Access\HandlesAuthorization;

class EventPolicy
{
    use HandlesAuthorization;

    public function viewAny($user): bool
    {
        return true;
    }

    public function view($user, Event $event): bool
    {
        return true;
    }

    public function create($user): bool
    {
        return true;
    }

    public function update($user, Event $event): bool
    {
        return true;
    }

    public function delete($user, Event $event): bool
    {
        return true;
    }

    public function deleteAny($user): bool
    {
        return true;
    }

    public function restore($user, Event $event): bool
    {
        return true;
    }

    public function forceDelete($user, Event $event): bool
    {
        return true;
    }
}
