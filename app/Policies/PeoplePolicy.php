<?php

namespace App\Policies;

use App\Models\People;
use Illuminate\Auth\Access\HandlesAuthorization;

class PeoplePolicy
{
    use HandlesAuthorization;

    public function viewAny($user): bool
    {
        return true;
    }

    public function view($user, People $people): bool
    {
        return true;
    }

    public function create($user): bool
    {
        return true;
    }

    public function update($user, People $people): bool
    {
        return true;
    }

    public function delete($user, People $people): bool
    {
        return true;
    }

    public function deleteAny($user): bool
    {
        return true;
    }

    public function restore($user, People $people): bool
    {
        return true;
    }

    public function forceDelete($user, People $people): bool
    {
        return true;
    }
}
