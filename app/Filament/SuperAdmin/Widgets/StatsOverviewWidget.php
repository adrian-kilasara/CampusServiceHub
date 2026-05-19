<?php

namespace App\Filament\SuperAdmin\Widgets;

use App\Models\Payment;
use App\Models\Provider;
use App\Models\ServiceRequest;
use App\Models\User;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverviewWidget extends BaseWidget
{
    protected static ?int $sort = 1;
    protected function getStats(): array
    {
        $totalRevenue = Payment::where('status', 'paid')->sum('amount');
        $revenueThisMonth = Payment::where('status', 'paid')
            ->whereMonth('paid_at', now()->month)->sum('amount');

        return [
            Stat::make('Total Users', User::count())
                ->description(User::whereDate('created_at', today())->count().' joined today')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('violet')
                ->chart(
                    User::selectRaw('COUNT(*) as count')
                        ->whereDate('created_at', '>=', now()->subDays(7))
                        ->groupByRaw('DATE(created_at)')
                        ->pluck('count')->toArray()
                ),

            Stat::make('Active Providers', Provider::where('status', 'approved')->count())
                ->description(Provider::where('status', 'pending')->count().' pending approval')
                ->descriptionIcon('heroicon-m-clock')
                ->color('amber'),

            Stat::make('Service Requests', ServiceRequest::count())
                ->description(ServiceRequest::where('status', 'pending')->count().' pending')
                ->descriptionIcon('heroicon-m-inbox')
                ->color('blue'),

            Stat::make('Total Revenue', 'KES '.number_format($totalRevenue, 2))
                ->description('KES '.number_format($revenueThisMonth, 2).' this month')
                ->descriptionIcon('heroicon-m-banknotes')
                ->color('emerald'),
        ];
    }
}
