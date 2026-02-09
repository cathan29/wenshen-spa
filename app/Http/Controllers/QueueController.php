<?php

namespace App\Http\Controllers;

use App\Models\Queue;
use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class QueueController extends Controller
{
    // 1. Show the Dashboard
    public function index()
    {
        $services = Service::where('is_active', true)->get();
        
        $todaysQueue = Queue::whereDate('created_at', today())
                            ->whereIn('status', ['waiting', 'serving'])
                            // 👇 MAGIC FIX FOR XAMPP:
                            // Assign "Rank 1" to Serving, "Rank 2" to Waiting.
                            // Sort Smallest Rank (1) to Largest (2).
                            ->orderByRaw("CASE WHEN status = 'serving' THEN 1 ELSE 2 END ASC")
                            ->orderBy('id', 'asc')
                            ->get();

        return view('dashboard', compact('services', 'todaysQueue'));
    }

    // 2. Add New Client
    public function store(Request $request)
    {
        $request->validate([
            'service_id' => 'required|exists:services,id',
            'customer_name' => 'nullable|string|max:255',
        ]);

        $queue = Queue::create([
            'queue_number' => $this->generateQueueNumber(),
            'service_id' => $request->service_id,
            'customer_name' => $request->customer_name ?? 'Guest',
            'qr_token' => Str::random(32),
            'status' => 'waiting',
        ]);

        return redirect()->route('dashboard')->with('success', "Client added! Queue Number: {$queue->queue_number}");
    }

    // 3. Status Page
    public function show($qr_token)
    {
        $queue = Queue::where('qr_token', $qr_token)->firstOrFail();

        if ($queue->status === 'completed' || $queue->status === 'cancelled') {
            return view('queue.expired');
        }

        $peopleAhead = Queue::where('status', 'waiting')
                            ->where('id', '<', $queue->id)
                            ->count();

        return view('queue.status', compact('queue', 'peopleAhead'));
    }

    // 4. TV Monitor
    public function monitor()
    {
        $serving = Queue::where('status', 'serving')->with('service')->first();
        $waiting = Queue::where('status', 'waiting')->orderBy('id', 'asc')->take(5)->get();

        return view('queue.monitor', compact('serving', 'waiting'));
    }

    // 5. Print Ticket
    public function printTicket($id)
    {
        $queue = Queue::with('service')->findOrFail($id);
        return view('queue.ticket', compact('queue'));
    }

    // 6. Update Status
    public function updateStatus(Request $request, $id)
    {
        $queue = Queue::findOrFail($id);
        $queue->update(['status' => $request->status]);

        return redirect()->back()->with('success', "Ticket updated.");
    }

    // 7. Add Follow-Up Service (Chain)
    public function addService(Request $request, $id)
    {
        $currentTicket = Queue::findOrFail($id);
        
        $request->validate([
            'service_id' => 'required|exists:services,id',
        ]);

        // Close Old Ticket
        $currentTicket->update(['status' => 'completed']);

        // Create New Ticket (Starts as SERVING immediately)
        Queue::create([
            'service_id' => $request->service_id,
            'customer_name' => $currentTicket->customer_name,
            'queue_number' => $currentTicket->queue_number, // Same Number
            'status' => 'serving',                          // Immediate Serving
            'qr_token' => Str::random(32),
        ]);

        return redirect()->back()->with('success', "Follow-up service added!");
    }

    // --- HELPER ---
    private function generateQueueNumber()
    {
        $countToday = Queue::whereDate('created_at', today())->count();
        return 'WS-' . str_pad($countToday + 1, 3, '0', STR_PAD_LEFT);
    }
}