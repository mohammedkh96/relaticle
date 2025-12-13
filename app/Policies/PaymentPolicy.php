<?php

namespace App\Policies;

use App\Models\Payment;
use Relaticle\SystemAdmin\Models\SystemAdministrator;

class PaymentPolicy
{
    public function viewAny(SystemAdministrator $user): bool
    {
        return $user->role->isSuperAdmin() || $user->hasPermission('view_payments');
    }

    public function view(SystemAdministrator $user, Payment $payment): bool
    {
        return $user->role->isSuperAdmin() || $user->hasPermission('view_payments');
    }

    public function create(SystemAdministrator $user): bool
    {
        return $user->role->isSuperAdmin() || $user->hasPermission('create_payments');
    }

    public function update(SystemAdministrator $user, Payment $payment): bool
    {
        return $user->role->isSuperAdmin() || $user->hasPermission('edit_payments');
    }

    public function delete(SystemAdministrator $user, Payment $payment): bool
    {
        return $user->role->isSuperAdmin() || $user->hasPermission('delete_payments');
    }

    public function deleteAny(SystemAdministrator $user): bool
    {
        return $user->role->isSuperAdmin() || $user->hasPermission('delete_payments');
    }

    public function restore(SystemAdministrator $user, Payment $payment): bool
    {
        return $user->role->isSuperAdmin() || $user->hasPermission('delete_payments');
    }

    public function forceDelete(SystemAdministrator $user, Payment $payment): bool
    {
        return $user->role->isSuperAdmin();
    }
}
