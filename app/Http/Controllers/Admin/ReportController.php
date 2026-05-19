<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\Provider;
use App\Models\ServiceRequest;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function requestsPdf(Request $request)
    {
        $requests = ServiceRequest::with(['student', 'service.category', 'payment'])
            ->when($request->status, fn ($q) => $q->where('status', $request->status))
            ->when($request->start_date, fn ($q) => $q->whereDate('created_at', '>=', $request->start_date))
            ->when($request->end_date, fn ($q) => $q->whereDate('created_at', '<=', $request->end_date))
            ->latest()
            ->take(100)
            ->get();

        $stats = [
            'total_requests' => ServiceRequest::count(),
            'completed'      => ServiceRequest::where('status', 'completed')->count(),
            'active_providers' => Provider::where('status', 'approved')->count(),
            'revenue'        => Payment::where('status', 'paid')->sum('amount'),
        ];

        $period = 'All time';
        if ($request->start_date && $request->end_date) {
            $period = $request->start_date . ' to ' . $request->end_date;
        }

        $pdf = Pdf::loadView('pdf.activity_report', compact('requests', 'stats', 'period'))
            ->setPaper('a4', 'landscape');

        return $pdf->stream('campushub-report-' . now()->format('Y-m-d') . '.pdf');
    }
}
