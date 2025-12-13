<?php

namespace App\Policies;

use App\Models\Invoice;
use Relaticle\SystemAdmin\Models\SystemAdministrator;

class InvoicePolicy
{
    public function viewAny(SystemAdministrator $user): bool
    {
        return $user->role->isSuperAdmin() || $user->hasPermission('view_invoices');
    }

    public function view(SystemAdministrator $user, Invoice $invoice): bool
    {
        return $user->role->isSuperAdmin() || $user->hasPermission('view_invoices');
    }

    public function create(SystemAdministrator $user): bool
    {
        return $user->role->isSuperAdmin() || $user->hasPermission('create_invoices');
    }

    public function update(SystemAdministrator $user, Invoice $invoice): bool
    {
        return $user->role->isSuperAdmin() || $user->hasPermission('edit_invoices');
    }

    public function delete(SystemAdministrator $user, Invoice $invoice): bool
    {
        return $user->role->isSuperAdmin() || $user->hasPermission('delete_invoices');
    }

    public function deleteAny(SystemAdministrator $user): bool
    {
        return $user->role->isSuperAdmin() || $user->hasPermission('delete_invoices');
    }

    public function restore(SystemAdministrator $user, Invoice $invoice): bool
    {
        return $user->role->isSuperAdmin() || $user->hasPermission('delete_invoices');
    }

    public function forceDelete(SystemAdministrator $user, Invoice $invoice): bool
    {
        return $user->role->isSuperAdmin();
    }
}
