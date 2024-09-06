<?php

namespace App\Policies;

use App\Models\Ticket;
use App\Models\User;

class TicketPolicy
{
    /**
     * Determine whether the user can view any tickets.
     */
    public function viewAny(User $user): bool
    {
        return $user ->isFacilityAdmin() || $user ->isEquipmentSuperAdmin() || $user ->isEquipmentAdmin() || $user ->isFacilitySuperAdmin() ||
        $user ->isEquipmentAdminOmiss() || $user -> isEquipmentAdminlabcustodian() || $user->isRegularUser();
    }
    
    /**
     * Determine whether the user can view the ticket.
     */
    public function view(User $user, Ticket $ticket): bool
    {
        return $user ->isFacilityAdmin() || $user ->isEquipmentSuperAdmin() || $user ->isEquipmentAdmin() || $user ->isFacilitySuperAdmin() ||
        $user ->isEquipmentAdminOmiss() || $user -> isEquipmentAdminlabcustodian() || $user->isRegularUser();
        // $ticket->email === $user->email;
    }

    /**
     * Determine whether the user can create tickets.
     */
    public function create(User $user): bool
    {
        return $user ->isFacilityAdmin() || $user ->isEquipmentSuperAdmin() || $user ->isEquipmentAdmin() || $user ->isFacilitySuperAdmin() ||
        $user ->isEquipmentAdminOmiss() || $user -> isEquipmentAdminlabcustodian() || $user->isRegularUser();
    }
    
    /**
     * Determine whether the user can update the ticket.
     */
    public function update(User $user, Ticket $ticket): bool
    {
        return $user ->isEquipmentSuperAdmin() || $user ->isFacilitySuperAdmin() ||
        $user ->isEquipmentAdminOmiss() || $user -> isEquipmentAdminlabcustodian();
    }
    /**
     * Determine whether the user can delete the ticket.
     */
    public function delete(User $user, Ticket $ticket): bool
    {
        return $user ->isEquipmentSuperAdmin() || $user ->isFacilitySuperAdmin() ||
        $user ->isEquipmentAdminOmiss() || $user -> isEquipmentAdminlabcustodian();
    }

    // Add more methods as needed...
}