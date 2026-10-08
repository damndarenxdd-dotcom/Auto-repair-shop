<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Invoice;

class InvoicePolicy
{
    public function view(User $user, Invoice $invoice): bool
    {
        return $user->id === $invoice->customer_id ||
               $user->hasRole('admin') ||
               $user->hasRole('manager');
    }

    public function update(User $user, Invoice $invoice): bool
    {
        return $user->hasRole('admin') ||
               $user->hasRole('manager');
    }
}
