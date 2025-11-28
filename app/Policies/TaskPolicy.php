<?php

namespace App\Policies;

use App\Models\Task;
use Illuminate\Auth\Access\HandlesAuthorization;

class TaskPolicy
{
    use HandlesAuthorization;

    public function viewAny($user): bool
    {
        return true;
    }

    public function view($user, Task $task): bool
    {
        return true;
    }

    public function create($user): bool
    {
        return true;
    }

    public function update($user, Task $task): bool
    {
        return true;
    }

    public function delete($user, Task $task): bool
    {
        return true;
    }

    public function deleteAny($user): bool
    {
        return true;
    }

    public function restore($user, Task $task): bool
    {
        return true;
    }

    public function forceDelete($user, Task $task): bool
    {
        return true;
    }
}
