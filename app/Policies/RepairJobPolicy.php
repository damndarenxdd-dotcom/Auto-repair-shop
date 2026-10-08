<?php

namespace App\Policies;

use App\Models\User;
use App\Models\RepairJob;

class RepairJobPolicy
{
    public function view(User $user, RepairJob $repairJob): bool
    {
        return $user->id === $repairJob->customer_id ||
               $user->id === $repairJob->mechanic_id ||
               $user->id === $repairJob->manager_id ||
               $user->hasRole('admin');
    }

    public function update(User $user, RepairJob $repairJob): bool
    {
        return $user->id === $repairJob->mechanic_id ||
               $user->id === $repairJob->manager_id ||
               $user->hasRole('admin');
    }

    public function manage(User $user, RepairJob $repairJob): bool
    {
        return $user->id === $repairJob->manager_id ||
               $user->hasRole('admin');
    }

    public function delete(User $user, RepairJob $repairJob): bool
    {
        return $user->hasRole('admin') || $user->id === $repairJob->manager_id;
    }
}
