<?php

namespace App\Policies;

use App\Models\TicketsAccepted;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class TicketsAcceptedPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->isEquipmentSuperAdmin() || $user->isFacilitySuperAdmin() || $user->isFacilityAdmin() || $user->isEquipmentAdmin() || $user->isRegularUser();
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, TicketsAccepted $ticketsAccepted): bool
    {
        return $user->isEquipmentSuperAdmin() || $user->isFacilitySuperAdmin() || $user->isFacilityAdmin() || $user->isEquipmentAdmin() || $user->isRegularUser();
    }
  
    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->isEquipmentSuperAdmin() || $user->isFacilitySuperAdmin() || $user->isFacilityAdmin() || $user->isEquipmentAdmin() ;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, TicketsAccepted $ticketsAccepted): bool
    {
        return $user->isEquipmentSuperAdmin() || $user->isFacilitySuperAdmin() || $user->isFacilityAdmin() || $user->isEquipmentAdmin() || $user->isRegularUser();
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, TicketsAccepted $ticketsAccepted): bool
    {
        return $user->isEquipmentSuperAdmin() || $user->isFacilitySuperAdmin() || $user->isFacilityAdmin() || $user->isEquipmentAdmin() || $user->isRegularUser();
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, TicketsAccepted $ticketsAccepted): bool
    {
        return $user->isEquipmentSuperAdmin() || $user->isFacilitySuperAdmin() || $user->isFacilityAdmin() || $user->isEquipmentAdmin() || $user->isRegularUser();
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, TicketsAccepted $ticketsAccepted): bool
    {
        return $user->isEquipmentSuperAdmin() || $user->isFacilitySuperAdmin() || $user->isFacilityAdmin() || $user->isEquipmentAdmin() || $user->isRegularUser();
    }
}
