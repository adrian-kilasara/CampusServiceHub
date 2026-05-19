<?php

namespace App\Console\Commands;

use App\Models\Payment;
use App\Models\Provider;
use App\Models\ServiceRequest;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use OpenAI\Laravel\Facades\OpenAI;

class GenerateAIInsight extends Command
{
    protected $signature   = 'insights:generate';
    protected $description = 'Generate AI-powered weekly platform insight and cache it for the Super Admin dashboard';

    public function handle(): int
    {
        $this->info('Gathering platform statistics...');

        $weekAgo = now()->subDays(7);

        $stats = [
            'new_users'        => User::where('created_at', '>=', $weekAgo)->count(),
            'new_requests'     => ServiceRequest::where('created_at', '>=', $weekAgo)->count(),
            'completed'        => ServiceRequest::where('status', 'completed')->where('updated_at', '>=', $weekAgo)->count(),
            'cancelled'        => ServiceRequest::where('status', 'cancelled')->where('updated_at', '>=', $weekAgo)->count(),
            'pending_count'    => ServiceRequest::where('status', 'pending')->count(),
            'active_providers' => Provider::where('status', 'approved')->count(),
            'revenue_week'     => Payment::where('status', 'paid')->where('paid_at', '>=', $weekAgo)->sum('amount'),
            'total_revenue'    => Payment::where('status', 'paid')->sum('amount'),
        ];

        $topServices = ServiceRequest::where('created_at', '>=', $weekAgo)
            ->select('service_id', \DB::raw('count(*) as count'))
            ->groupBy('service_id')
            ->with('service:id,name')
            ->orderByDesc('count')
            ->take(3)
            ->get()
            ->map(fn ($r) => "{$r->service?->name} ({$r->count})")
            ->implode(', ');

        $prompt = <<<PROMPT
You are an analytics assistant for CampusHub, a campus service marketplace platform.
Generate a concise, actionable weekly insight summary (3-4 sentences max) based on this data:

- New users this week: {$stats['new_users']}
- New service requests this week: {$stats['new_requests']}
- Completed requests this week: {$stats['completed']}
- Cancelled requests this week: {$stats['cancelled']}
- Currently pending (unassigned) requests: {$stats['pending_count']}
- Active approved providers: {$stats['active_providers']}
- Revenue this week: ₵{$stats['revenue_week']}
- Total platform revenue: ₵{$stats['total_revenue']}
- Top services requested this week: {$topServices}

Highlight notable trends, flag concerns (e.g. high pending count, cancellations), and suggest one actionable recommendation for the admin. Be concise and professional.
PROMPT;

        $this->info('Calling OpenAI API...');

        try {
            $response = OpenAI::chat()->create([
                'model'    => 'gpt-4o-mini',
                'messages' => [
                    ['role' => 'user', 'content' => $prompt],
                ],
                'max_tokens' => 300,
            ]);

            $insight = $response->choices[0]->message->content;
        } catch (\Throwable $e) {
            $this->error("OpenAI API error: {$e->getMessage()}");
            $insight = "Unable to generate AI insight at this time. Stats: {$stats['new_requests']} new requests, {$stats['completed']} completed, ₵{$stats['revenue_week']} revenue this week.";
        }

        Cache::put('ai_insight', [
            'text'         => $insight,
            'generated_at' => now()->toDateTimeString(),
            'stats'        => $stats,
        ], now()->addHours(24));

        $this->info('AI insight generated and cached for 24 hours.');
        $this->line($insight);

        return self::SUCCESS;
    }
}
