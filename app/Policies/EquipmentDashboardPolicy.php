<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Ticket;

class EquipmentDashboardPolicy
{
    /**
     * Determine whether the user can view any tickets (or the dashboard).
     */
    public function viewAny(User $user): bool
    {
        return $user->isEquipmentSuperAdmin() || 
               $user->isEquipmentAdmin() || 
               $user->isRegularUser();
    }

    /**
     * Determine whether the user can view a specific ticket.
     */
    public function view(User $user, Ticket $ticket): bool
    {
        return $user->isEquipmentSuperAdmin() || 
               $user->isEquipmentAdmin() || 
               $user->isRegularUser();
    }

    /**
     * Determine whether the user can update the ticket.
     */
    public function update(User $user, Ticket $ticket): bool
    {
        return $user->isEquipmentSuperAdmin() || 
               $user->isEquipmentAdmin();
    }

    /**
     * Determine whether the user can delete the ticket.
     */
    public function delete(User $user, Ticket $ticket): bool
    {
        return $user->isEquipmentSuperAdmin() || 
               $user->isEquipmentAdmin();
    }
}
