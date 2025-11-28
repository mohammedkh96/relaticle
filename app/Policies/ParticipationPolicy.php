<?php

namespace App\Policies;

use App\Models\Participation;
use Illuminate\Auth\Access\HandlesAuthorization;

class ParticipationPolicy
{
    use HandlesAuthorization;

    public function viewAny($user): bool
    {
        return true;
    }

    public function view($user, Participation $participation): bool
    {
        return true;
    }

    public function create($user): bool
    {
        return true;
    }

    public function update($user, Participation $participation): bool
    {
        return true;
    }

    public function delete($user, Participation $participation): bool
    {
        return true;
    }

    public function deleteAny($user): bool
    {
        return true;
    }

    public function restore($user, Participation $participation): bool
    {
        return true;
    }

    public function forceDelete($user, Participation $participation): bool
    {
        return true;
    }
}
