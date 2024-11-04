<?php

namespace App\Filament\Widgets;

use App\Models\TicketQueue;
use App\Models\TicketsAccepted;
use App\Models\TicketResolved;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon; // Import Carbon for date handling

class TicketOverviewWidget extends BaseWidget
{
    protected function getStats(): array
    {
        $user = Auth::user();

        // Get today's date for comparison
        $today = Carbon::today();

        // Initialize stats array
        $stats = [];

        // For admins and superadmins, filter based on dept_role
        if ($user->isFacilitySuperAdmin() || $user->isEquipmentSuperAdmin() || $user->isFacilityAdmin()) {
            // Get today's ticket queues based on dept_role
            $stats[] = Stat::make('Today\'s Ticket Queues', TicketQueue::where('dept_role', $user->dept_role)
                ->whereDate('created_at', $today)
                ->count());

            // Get today's accepted tickets based on dept_role
            $stats[] = Stat::make('Today\'s Accepted Tickets', TicketsAccepted::where('dept_role', $user->dept_role)
                ->whereDate('accepted_at', $today)
                ->count());

            // Get today's resolved tickets based on dept_role
            $stats[] = Stat::make('Today\'s Resolved Tickets', TicketResolved::where('dept_role', $user->dept_role)
                ->whereDate('resolved_at', $today)
                ->count());
        } else {
            // For regular users, filter based on their ID
            // Get today's tickets created by the user
            $stats[] = Stat::make('Today\'s Tickets Created', $user->ticketsCreated()
                ->whereDate('created_at', $today)
                ->count());

            // Get today's tickets accepted by the user
            $stats[] = Stat::make('Today\'s Tickets Accepted', $user->ticketsAssigned()
                ->whereDate('accepted_at', $today)
                ->count());

            // Get today's tickets resolved by the user (assuming there's a relationship set up for resolved tickets)
            $stats[] = Stat::make('Today\'s Tickets Resolved', TicketResolved::where('assigned', $user->id)
                ->whereDate('resolved_at', $today)
                ->count());
        }

        return $stats;
    }
}
