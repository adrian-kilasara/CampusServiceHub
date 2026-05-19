<?php

namespace App\Http\Controllers\Provider;

use App\Http\Controllers\Controller;
use App\Models\ServiceRequest;
use App\Notifications\RequestStatusUpdated;
use App\Notifications\NewJobAssigned;
use Illuminate\Http\Request;

class ProviderJobController extends Controller
{
    private function getProvider()
    {
        return auth()->user()->provider ?? abort(403, 'Provider profile not found.');
    }

    public function index()
    {
        $provider = $this->getProvider();

        $jobs = ServiceRequest::where('provider_id', $provider->id)
            ->with('service', 'student')
            ->latest()
            ->paginate(15);

        return view('provider.jobs.index', compact('jobs', 'provider'));
    }

    public function accept(ServiceRequest $request)
    {
        $provider = $this->getProvider();
        abort_unless($provider->isApproved(), 403, 'Your account is not yet approved.');
        abort_unless($request->isPending(), 403);

        $request->update([
            'provider_id'  => $provider->id,
            'status'       => 'accepted',
            'accepted_at'  => now(),
            'quoted_price' => $request->service->base_price,
        ]);

        $request->student->notify(new RequestStatusUpdated($request));
        auth()->user()->notify(new NewJobAssigned($request));

        return back()->with('success', 'Job accepted! Contact the student to arrange.');
    }

    public function decline(ServiceRequest $request)
    {
        $provider = $this->getProvider();
        abort_unless($request->provider_id === $provider->id, 403);

        $request->update(['provider_id' => null, 'status' => 'pending']);

        return back()->with('info', 'Job declined and returned to queue.');
    }

    public function start(ServiceRequest $request)
    {
        $provider = $this->getProvider();
        abort_unless($request->provider_id === $provider->id && $request->status === 'accepted', 403);

        $request->update(['status' => 'in_progress']);
        $request->student->notify(new RequestStatusUpdated($request));
        return back()->with('success', 'Job marked as in progress.');
    }

    public function complete(ServiceRequest $request)
    {
        $provider = $this->getProvider();
        abort_unless($request->provider_id === $provider->id && $request->status === 'in_progress', 403);

        $request->update([
            'status'       => 'completed',
            'completed_at' => now(),
            'final_price'  => $request->quoted_price,
        ]);

        $provider->increment('completed_jobs');
        $request->student->notify(new RequestStatusUpdated($request));

        return back()->with('success', 'Job completed! Great work.');
    }
}
