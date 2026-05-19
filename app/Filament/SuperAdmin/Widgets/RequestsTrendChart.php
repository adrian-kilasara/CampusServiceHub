<?php

namespace App\Filament\SuperAdmin\Widgets;

use App\Models\ServiceRequest;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Carbon;

class RequestsTrendChart extends ChartWidget
{
    protected static ?string $heading = 'Service Requests — Last 12 Months';
    protected static ?int $sort = 2;
    protected int|string|array $columnSpan = 'full';

    protected function getData(): array
    {
        $data = collect(range(11, 0))->map(function ($monthsAgo) {
            $date = now()->subMonths($monthsAgo);
            return [
                'month' => $date->format('M Y'),
                'count' => ServiceRequest::whereYear('created_at', $date->year)
                    ->whereMonth('created_at', $date->month)->count(),
            ];
        });

        return [
            'datasets' => [
                [
                    'label' => 'Requests',
                    'data' => $data->pluck('count')->toArray(),
                    'backgroundColor' => 'rgba(99,102,241,0.15)',
                    'borderColor' => 'rgba(99,102,241,1)',
                    'fill' => true,
                    'tension' => 0.4,
                ],
            ],
            'labels' => $data->pluck('month')->toArray(),
        ];
    }

    protected function getType(): string { return 'line'; }
}
