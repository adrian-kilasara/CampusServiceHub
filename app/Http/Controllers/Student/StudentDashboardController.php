<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\ServiceRequest;

class StudentDashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        $requests = ServiceRequest::where('student_id', $user->id)
            ->with(['service', 'provider'])
            ->latest()
            ->take(5)
            ->get();

        $stats = [
            'total'       => ServiceRequest::where('student_id', $user->id)->count(),
            'pending'     => ServiceRequest::where('student_id', $user->id)->where('status', 'pending')->count(),
            'in_progress' => ServiceRequest::where('student_id', $user->id)->whereIn('status', ['accepted', 'in_progress'])->count(),
            'completed'   => ServiceRequest::where('student_id', $user->id)->where('status', 'completed')->count(),
        ];

        return view('student.dashboard', compact('user', 'requests', 'stats'));
    }
}
