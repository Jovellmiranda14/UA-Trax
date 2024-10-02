<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use App\Filament\Resources\TicketResource\Widgets\TicketVolumeChart;
use App\Filament\Resources\TicketsAcceptedResource\Widgets\TicketAcceptedChart;
use App\Filament\Resources\TicketResolvedResource\Widgets\TicketResolvedChart;
use App\Filament\Resources\TicketResource\Widgets\ConcernTypeChart;
use App\Filament\Resources\TicketResource\Widgets\IssueTypeChart;
use Illuminate\Support\Facades\Gate;


class AnalyticsChart extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static string $view = 'filament.pages.analytics-chart';

    public function getHeaderWidgets(): array
    {
        return [
            TicketVolumeChart::class,
            TicketAcceptedChart::class,
            TicketResolvedChart::class,
            ConcernTypeChart::class,
            IssueTypeChart::class,
        ];
    }

    public static function canView(): bool
    {
        // Allow access only to admin roles
        return Gate::allows('viewAny', AnalyticsChartPolicy::class);
    }

    public function mount()
    {
        // Deny access if the user is not an admin
        if (!self::canView()) {
            abort(403); // Deny access for non-admin users
        }
    }

    public static function getNavigationGroup(): ?string
    {
        // Only show in navigation if the user can view the page
        return self::canView() ? 'Analytics' : null;
    }

    public static function getNavigationLabel(): string
    {
        // Always return this label as the page is for admins only
        return 'Analytics Chart';
    }
}
