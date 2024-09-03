<?php

namespace App\Policies;

use App\Models\TicketHistory;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class TicketHistoryPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user ->isFacilityAdmin() || $user ->isEquipmentSuperAdmin() || $user ->isEquipmentAdmin() || $user ->isFacilitySuperAdmin() ||
        $user ->isEquipmentAdminOmiss() || $user -> isEquipmentAdminlabcustodian();
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, TicketHistory $ticketHistory): bool
    {
        return $user ->isFacilityAdmin() || $user ->isEquipmentSuperAdmin() || $user ->isEquipmentAdmin() || $user ->isFacilitySuperAdmin() ||
        $user ->isEquipmentAdminOmiss() || $user -> isEquipmentAdminlabcustodian();
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user ->isFacilityAdmin() || $user ->isEquipmentSuperAdmin() || $user ->isEquipmentAdmin() || $user ->isFacilitySuperAdmin() ||
        $user ->isEquipmentAdminOmiss() || $user -> isEquipmentAdminlabcustodian();
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, TicketHistory $ticketHistory): bool
    {
        return $user ->isFacilityAdmin() || $user ->isEquipmentSuperAdmin() || $user ->isEquipmentAdmin() || $user ->isFacilitySuperAdmin() ||
        $user ->isEquipmentAdminOmiss() || $user -> isEquipmentAdminlabcustodian();
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, TicketHistory $ticketHistory): bool
    {
        return $user ->isFacilityAdmin() || $user ->isEquipmentSuperAdmin() || $user ->isEquipmentAdmin() || $user ->isFacilitySuperAdmin() ||
        $user ->isEquipmentAdminOmiss() || $user -> isEquipmentAdminlabcustodian();
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, TicketHistory $ticketHistory): bool
    {
        return $user ->isFacilityAdmin() || $user ->isEquipmentSuperAdmin() || $user ->isEquipmentAdmin() || $user ->isFacilitySuperAdmin() ||
        $user ->isEquipmentAdminOmiss() || $user -> isEquipmentAdminlabcustodian();
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, TicketHistory $ticketHistory): bool
    {
        return $user ->isFacilityAdmin() || $user ->isEquipmentSuperAdmin() || $user ->isEquipmentAdmin() || $user ->isFacilitySuperAdmin() ||
        $user ->isEquipmentAdminOmiss() || $user -> isEquipmentAdminlabcustodian();
    }
}