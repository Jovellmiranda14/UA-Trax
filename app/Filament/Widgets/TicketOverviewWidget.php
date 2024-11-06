<?php

namespace App\Filament\Widgets;

use App\Models\Ticket; // Ensure this is included
use App\Models\TicketQueue;
use App\Models\TicketsAccepted;
use App\Models\TicketResolved;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Auth;

class TicketOverviewWidget extends BaseWidget
{
    protected function getStats(): array
    {
        // Get the current authenticated user
        $user = Auth::user();

        // Initialize statistics array
        $stats = [];

        if ($user->isFacilitySuperAdmin() || $user->isEquipmentSuperAdmin() || $user->isFacilityAdmin() || $user->isEquipmentAdminOmiss() || $user->isEquipmentAdminlabcustodian()) {
            // For admins and superadmins based on their dept_roles
            $stats[] = Stat::make('Today\'s Latest Ticket Queues', TicketQueue::whereDate('created_at', now())->count());
            $stats[] = Stat::make('Today\'s Latest Accepted Tickets', TicketsAccepted::whereDate('accepted_at', now())->count());
            $stats[] = Stat::make('Today\'s Latest Resolved Tickets', TicketResolved::whereDate('resolved_at', now())->count());
        } else {
            // For regular users, count tickets based on their ID or other relevant field
            $stats[] = Stat::make('Today\'s Tickets Created', Ticket::where('name', $user->name)->whereDate('created_at', now())->count());
            $stats[] = Stat::make('Today\'s Tickets Accepted', TicketsAccepted::where('name', $user->name)->whereDate('accepted_at', now())->count());
            $stats[] = Stat::make('Today\'s Tickets Resolved', TicketResolved::where('name', $user->name)->whereDate('resolved_at', now())->count());
        }

        return $stats;
    }
}
