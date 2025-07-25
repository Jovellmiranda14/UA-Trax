<?php

namespace App\Filament\Widgets;

use App\Models\Ticket;
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
        $stats = [];

        if ($user->isFacilitySuperAdmin() || $user->isEquipmentSuperAdmin() || $user->isFacilityAdmin() || $user->isEquipmentAdminOmiss() || $user->isEquipmentAdminlabcustodian()) {
            // For admins and superadmins based on their dept_roles
            $stats[] = Stat::make('Ticket Queues', TicketQueue::whereDate('created_at', now())->count())
                ->description('Today');
            $stats[] = Stat::make('Accepted Tickets', TicketsAccepted::whereDate('accepted_at', now())->count())
                ->description('Today');
            $stats[] = Stat::make('Resolved Tickets', TicketResolved::whereDate('resolved_at', now())->count())
                ->description('Today');
        } else {
            // For regular users, count tickets based on their ID or other relevant field
            $stats[] = Stat::make('Tickets Created', Ticket::where('name', $user->name)->whereDate('created_at', now())->count())
                ->description('Today');
            $stats[] = Stat::make('Tickets Accepted', TicketsAccepted::where('name', $user->name)->whereDate('accepted_at', now())->count())
                ->description('Today');
            $stats[] = Stat::make('Tickets Resolved', TicketResolved::where('name', $user->name)->whereDate('resolved_at', now())->count())
                ->description('Today');
        }

        return $stats;
    }
}
