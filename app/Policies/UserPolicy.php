<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class UserPolicy
{
    use HandlesAuthorization;

    public function viewAny($user): bool
    {
        return true;
    }

    public function view($user, User $model): bool
    {
        return true;
    }

    public function create($user): bool
    {
        return true;
    }

    public function update($user, User $model): bool
    {
        return true;
    }

    public function delete($user, User $model): bool
    {
        return true;
    }

    public function deleteAny($user): bool
    {
        return true;
    }

    public function restore($user, User $model): bool
    {
        return true;
    }

    public function forceDelete($user, User $model): bool
    {
        return true;
    }
}
