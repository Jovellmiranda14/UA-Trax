<?php

namespace App\Policies;

use App\Models\User;

class EquipmentDashboardPolicy
{
    /**
     * Create a new policy instance.
     */
    public function viewAny(User $user): bool
    {
        return $user ->isEquipmentSuperAdmin() || 
        $user ->isFacilitySuperAdmin() ||$user -> isFaciltyAdmin() || 
        $user -> isEquipmentAdmin() || $user ->isRegularUser();
    }
    /**
     * Determine whether the user can view the ticket.
     */
    public function view(User $user, Ticket $ticket): bool
    {
        return $user ->isEquipmentSuperAdmin() || $user ->isFacilitySuperAdmin() ||$user -> isFaciltyAdmin() || $user -> isEquipmentAdmin() || $user ->isRegularUser();
    }
    public function update(User $user, Post $post): bool
    {
    /**
     * Determine whether the user can update the ticket.
     */   
      return $user ->isEquipmentSuperAdmin() || $user -> isEquipmentAdmin();
    }
    /**
     * Determine whether the user can delete the ticket.
     */
    public function delete(User $user, Ticket $ticket): bool
    {
        return $user ->isEquipmentSuperAdmin() || $user -> isEquipmentAdmin();
    }
}
