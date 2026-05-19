<?php

namespace App\Filament\SuperAdmin\Widgets;

use App\Models\Payment;
use Filament\Widgets\ChartWidget;

class RevenueTrendChart extends ChartWidget
{
    protected static ?string $heading = 'Revenue — Last 12 Months (KES)';
    protected static ?int $sort = 3;
    protected int|string|array $columnSpan = 'full';

    protected function getData(): array
    {
        $data = collect(range(11, 0))->map(function ($monthsAgo) {
            $date = now()->subMonths($monthsAgo);
            return [
                'month' => $date->format('M Y'),
                'revenue' => Payment::where('status', 'paid')
                    ->whereYear('paid_at', $date->year)
                    ->whereMonth('paid_at', $date->month)
                    ->sum('amount'),
            ];
        });

        return [
            'datasets' => [
                [
                    'label' => 'Revenue (KES)',
                    'data' => $data->pluck('revenue')->toArray(),
                    'backgroundColor' => [
                        'rgba(16,185,129,0.6)', 'rgba(16,185,129,0.6)', 'rgba(16,185,129,0.6)',
                        'rgba(16,185,129,0.6)', 'rgba(16,185,129,0.6)', 'rgba(16,185,129,0.6)',
                        'rgba(16,185,129,0.6)', 'rgba(16,185,129,0.6)', 'rgba(16,185,129,0.6)',
                        'rgba(16,185,129,0.6)', 'rgba(16,185,129,0.6)', 'rgba(99,102,241,0.8)',
                    ],
                    'borderRadius' => 6,
                ],
            ],
            'labels' => $data->pluck('month')->toArray(),
        ];
    }

    protected function getType(): string { return 'bar'; }
}
