<?php

namespace App\Filament\Admin\Widgets;

use App\Models\ServiceCategory;
use Filament\Widgets\ChartWidget;

class ServiceCategoryBreakdownChart extends ChartWidget
{
    protected static ?string $heading = 'Requests by Service Category';
    protected static ?int $sort = 2;

    protected function getData(): array
    {
        $data = ServiceCategory::withCount(['services as request_count' => function ($q) {
            $q->join('service_requests', 'services.id', '=', 'service_requests.service_id');
        }])->having('request_count', '>', 0)->get();

        return [
            'datasets' => [[
                'data' => $data->pluck('request_count')->toArray(),
                'backgroundColor' => [
                    '#6366f1', '#f59e0b', '#10b981', '#ec4899', '#3b82f6', '#14b8a6',
                ],
            ]],
            'labels' => $data->pluck('name')->toArray(),
        ];
    }

    protected function getType(): string { return 'doughnut'; }
}
