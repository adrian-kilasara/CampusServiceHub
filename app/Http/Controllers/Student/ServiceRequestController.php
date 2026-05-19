<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Review;
use App\Models\Service;
use App\Models\ServiceCategory;
use App\Models\ServiceRequest;
use Illuminate\Http\Request;

class ServiceRequestController extends Controller
{
    public function index()
    {
        $requests = ServiceRequest::where('student_id', auth()->id())
            ->with(['service.category', 'provider'])
            ->latest()
            ->paginate(10);

        return view('student.requests.index', compact('requests'));
    }

    public function create()
    {
        $categories = ServiceCategory::where('is_active', true)
            ->with(['services' => fn ($q) => $q->where('is_active', true)])
            ->orderBy('sort_order')
            ->get();

        return view('student.requests.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'service_id'  => 'required|exists:services,id',
            'title'       => 'required|string|max:255',
            'description' => 'required|string',
            'urgency'     => 'required|in:low,medium,high,urgent',
            'files.*'     => 'nullable|file|max:10240',
        ]);

        $files = [];
        if ($request->hasFile('files')) {
            foreach ($request->file('files') as $file) {
                $files[] = $file->store('request-files', 'public');
            }
        }

        ServiceRequest::create([
            'student_id'  => auth()->id(),
            'service_id'  => $data['service_id'],
            'title'       => $data['title'],
            'description' => $data['description'],
            'urgency'     => $data['urgency'],
            'files'       => $files ?: null,
            'status'      => 'pending',
        ]);

        return redirect()->route('student.requests.index')
            ->with('success', 'Request submitted! A provider will respond shortly.');
    }

    public function show(ServiceRequest $request)
    {
        abort_unless($request->student_id === auth()->id(), 403);
        $request->load(['service.category', 'provider.user', 'payment', 'review']);
        return view('student.requests.show', compact('request'));
    }

    public function cancel(ServiceRequest $request)
    {
        abort_unless($request->student_id === auth()->id(), 403);
        abort_unless($request->isPending(), 403, 'Only pending requests can be cancelled.');

        $request->update(['status' => 'cancelled', 'cancellation_reason' => 'Cancelled by student']);

        return back()->with('success', 'Request cancelled.');
    }

    public function submitReview(Request $request, ServiceRequest $serviceRequest)
    {
        abort_unless($serviceRequest->student_id === auth()->id(), 403);
        abort_unless($serviceRequest->isCompleted(), 403);
        abort_if($serviceRequest->review()->exists(), 403, 'Review already submitted.');

        $data = $request->validate([
            'rating'  => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:1000',
        ]);

        Review::create([
            'service_request_id' => $serviceRequest->id,
            'student_id'         => auth()->id(),
            'provider_id'        => $serviceRequest->provider_id,
            'rating'             => $data['rating'],
            'comment'            => $data['comment'] ?? null,
        ]);

        return back()->with('success', 'Review submitted. Thank you!');
    }
}
