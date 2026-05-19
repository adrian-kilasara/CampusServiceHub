<?php

namespace App\Http\Controllers\Provider;

use App\Http\Controllers\Controller;
use App\Models\ServiceRequest;

class ProviderDashboardController extends Controller
{
    public function index()
    {
        $user     = auth()->user();
        $provider = $user->provider;

        if (!$provider) {
            return redirect()->route('register.provider');
        }

        $activeJobs = ServiceRequest::where('provider_id', $provider->id)
            ->whereIn('status', ['accepted', 'in_progress'])
            ->with('service', 'student')
            ->latest()
            ->get();

        $pendingJobs = ServiceRequest::whereNull('provider_id')
            ->where('status', 'pending')
            ->whereHas('service', fn ($q) =>
                $q->whereIn('id', $provider->services()->pluck('services.id'))
            )
            ->with('service', 'student')
            ->latest()
            ->take(10)
            ->get();

        $stats = [
            'completed'   => ServiceRequest::where('provider_id', $provider->id)->where('status', 'completed')->count(),
            'active'      => $activeJobs->count(),
            'rating'      => $provider->rating_avg,
            'earnings'    => \App\Models\Payment::whereHas('serviceRequest', fn ($q) =>
                $q->where('provider_id', $provider->id)
            )->where('status', 'paid')->sum('amount'),
        ];

        return view('provider.dashboard', compact('user', 'provider', 'activeJobs', 'pendingJobs', 'stats'));
    }
}
