<?php

namespace App\Filament\Admin\Widgets;

use App\Models\Provider;
use App\Models\ServiceRequest;
use App\Models\Ticket;
use App\Models\User;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class AdminStatsWidget extends BaseWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        return [
            Stat::make('New Requests Today', ServiceRequest::whereDate('created_at', today())->count())
                ->description(ServiceRequest::where('status', 'pending')->count().' total pending')
                ->descriptionIcon('heroicon-m-inbox')
                ->color('blue'),

            Stat::make('Pending Provider Approvals', Provider::where('status', 'pending')->count())
                ->descriptionIcon('heroicon-m-clock')
                ->color('amber'),

            Stat::make('Open Tickets', Ticket::where('status', 'open')->count())
                ->description(Ticket::where('status', 'in_progress')->count().' in progress')
                ->descriptionIcon('heroicon-m-ticket')
                ->color('red'),

            Stat::make('Active Students', User::role('student')->where('status', 'active')->count())
                ->descriptionIcon('heroicon-m-academic-cap')
                ->color('emerald'),
        ];
    }
}
