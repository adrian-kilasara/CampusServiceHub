<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Ticket;
use Illuminate\Http\Request;

class TicketController extends Controller
{
    public function index()
    {
        $tickets = Ticket::where('user_id', auth()->id())->latest()->paginate(10);
        return view('student.tickets.index', compact('tickets'));
    }

    public function create()
    {
        return view('student.tickets.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'subject'     => 'required|string|max:255',
            'description' => 'required|string',
            'priority'    => 'required|in:low,medium,high,urgent',
        ]);

        Ticket::create([
            'user_id'     => auth()->id(),
            'subject'     => $data['subject'],
            'description' => $data['description'],
            'priority'    => $data['priority'],
            'status'      => 'open',
        ]);

        return redirect()->route('student.tickets.index')->with('success', 'Ticket submitted! We\'ll respond shortly.');
    }

    public function show(Ticket $ticket)
    {
        abort_unless($ticket->user_id === auth()->id(), 403);
        $ticket->load('replies.user');
        return view('student.tickets.show', compact('ticket'));
    }
}
