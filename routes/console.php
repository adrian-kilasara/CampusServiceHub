<?php

use App\Console\Commands\GenerateAIInsight;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Generate AI insights every day at 6 AM
Schedule::command(GenerateAIInsight::class)->dailyAt('06:00');
