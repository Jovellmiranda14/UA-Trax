<?php

namespace App\Policies;

use App\Models\User;

class FacilityDashboardPolicy
{
    /**
     * Create a new policy instance.
     */
    public function viewAny(User $user): bool
    {
        return $user ->isEquipmentSuperAdmin() || 
        $user ->isFacilitySuperAdmin() ||$user -> isFacilityAdmin() || 
        $user -> isEquipmentAdmin() || $user ->isRegularUser();
    }
    /**
     * Determine whether the user can view the ticket.
     */
    public function view(User $user, Ticket $ticket): bool
    {
        return $user ->isEquipmentSuperAdmin() || $user ->isFacilitySuperAdmin() || $user -> isFacilityAdmin() || $user -> isEquipmentAdmin() || $user ->isRegularUser();
    }

    public function update(User $user, Post $post): bool
    {
    /**
     * Determine whether the user can update the ticket.
     */   
      return $user ->isFacilitySuperAdmin() || $user -> isFacilityAdmin();
    }
    public function delete(User $user, Ticket $ticket): bool
    {
        return $user ->isFacilitySuperAdmin() || $user -> isFacilityAdmin();
    }

}
