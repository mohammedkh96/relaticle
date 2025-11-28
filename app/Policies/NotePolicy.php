<?php

namespace App\Policies;

use App\Models\Note;
use Illuminate\Auth\Access\HandlesAuthorization;

class NotePolicy
{
    use HandlesAuthorization;

    public function viewAny($user): bool
    {
        return true;
    }

    public function view($user, Note $note): bool
    {
        return true;
    }

    public function create($user): bool
    {
        return true;
    }

    public function update($user, Note $note): bool
    {
        return true;
    }

    public function delete($user, Note $note): bool
    {
        return true;
    }

    public function deleteAny($user): bool
    {
        return true;
    }

    public function restore($user, Note $note): bool
    {
        return true;
    }

    public function forceDelete($user, Note $note): bool
    {
        return true;
    }
}
