<?php

namespace App\Filament\SuperAdmin\Widgets;

use Filament\Widgets\Widget;
use Illuminate\Support\Facades\Cache;

class AIInsightWidget extends Widget
{
    protected static string $view = 'filament.super-admin.widgets.ai-insight';

    protected int | string | array $columnSpan = 'full';

    protected static ?int $sort = 5;

    public function getInsight(): array
    {
        return Cache::get('ai_insight', [
            'text'         => 'No AI insight available yet. Run `php artisan insights:generate` to generate one.',
            'generated_at' => null,
            'stats'        => [],
        ]);
    }
}
