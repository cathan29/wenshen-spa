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
        
        // Active Queue (Waiting, Serving, AND Cancel Requested)
        $todaysQueue = Queue::whereDate('created_at', today())
                            ->whereIn('status', ['waiting', 'serving', 'cancel_requested'])
                            // Sort Priority: Serving (1) -> Cancel Req (2) -> Waiting (3)
                            ->orderByRaw("CASE WHEN status = 'serving' THEN 1 WHEN status = 'cancel_requested' THEN 2 ELSE 3 END ASC")
                            ->orderBy('id', 'asc')
                            ->get();

        // History (Completed, Cancelled, No Show)
        $completedQueue = Queue::whereDate('created_at', today())
                               ->whereIn('status', ['completed', 'cancelled', 'no_show'])
                               ->orderBy('updated_at', 'desc')
                               ->get();

        // Calculate Total Earnings (Sums up ALL services attached to completed tickets)
        $totalEarnings = Queue::whereDate('created_at', today())
                              ->where('status', 'completed')
                              ->with('services') // 👈 CHANGED to plural
                              ->get()
                              ->sum(function($q) {
                                  return $q->total_price; // 👈 Uses the new helper from your Model!
                              });

        return view('dashboard', compact('services', 'todaysQueue', 'completedQueue', 'totalEarnings'));
    }

    // 2. Add New Client
    public function store(Request $request)
    {
        // 👈 CHANGED: Now validates an array of multiple service IDs
        $request->validate([
            'service_ids'   => 'required|array',
            'service_ids.*' => 'exists:services,id',
            'customer_name' => 'nullable|string|max:255',
        ]);

        // Create the Ticket first (Notice: no service_id here anymore!)
        $queue = Queue::create([
            'queue_number'  => $this->generateQueueNumber(),
            'customer_name' => $request->customer_name ?? 'Guest',
            'qr_token'      => Str::random(32),
            'status'        => 'waiting',
        ]);

        // ✨ THE MAGIC STEP: Attach all selected treatments to this ticket!
        $queue->services()->attach($request->service_ids);

        return redirect()->route('dashboard')->with('success', "Client added! Queue Number: {$queue->queue_number}");
    }

    // 3. Status Page
    public function show($qr_token)
    {
        $queue = Queue::where('qr_token', $qr_token)->firstOrFail();

        // If ticket is already done or gone, show expired
        if ($queue->status === 'completed' || $queue->status === 'cancelled') {
            return view('queue.expired', compact('queue'));
        }

        // Counts only those WAITING who joined BEFORE this specific ticket ID
        $peopleAhead = Queue::where('status', 'waiting')
                            ->whereDate('created_at', today())
                            ->where('id', '<', $queue->id) 
                            ->count();

        return view('queue.status', compact('queue', 'peopleAhead'));
    }

    // 4. The Public Monitor Screen (Big TV)
    public function monitor()
    {
        // Get everyone currently being served
        $serving = Queue::where('status', 'serving')
                        ->whereDate('created_at', today())
                        ->with('services') // 👈 CHANGED to plural
                        ->orderBy('updated_at', 'desc') 
                        ->take(4)
                        ->get();

        // Identify the "Recently Called" group
        $recentlyCalled = $serving->where('updated_at', '>=', now()->subSeconds(20));

        $waiting = Queue::where('status', 'waiting')
                        ->whereDate('created_at', today())
                        ->orderBy('id', 'asc')
                        ->take(5)
                        ->get();

        return view('queue.monitor', compact('serving', 'waiting', 'recentlyCalled'));
    }

    // 5. Print Ticket
    public function printTicket($id)
    {
        $queue = Queue::with('services')->findOrFail($id); // 👈 CHANGED to plural
        return view('queue.ticket', compact('queue'));
    }

    // 6. Update Status
    public function updateStatus(Request $request, $id)
    {
        $queue = Queue::findOrFail($id);
        $queue->update([
            'status' => $request->status,
            'remarks'=> $request->remarks ?? null,
        ]);

        return redirect()->back()->with('success', "Ticket #{$queue->queue_number} updated.");
    }

    // 7. Add Follow-Up Service (Chain)
    public function addService(Request $request, $id)
    {
        $currentTicket = Queue::findOrFail($id);
        
        // 👈 CHANGED: Now validates an array
        $request->validate([
            'service_ids'   => 'required|array',
            'service_ids.*' => 'exists:services,id',
        ]);

        $currentTicket->update(['status' => 'completed']);

        $newTicket = Queue::create([
            'customer_name' => $currentTicket->customer_name,
            'queue_number'  => $currentTicket->queue_number, 
            'status'        => 'serving',                            
            'qr_token'      => Str::random(32),
        ]);

        // Attach the new follow-up treatments!
        $newTicket->services()->attach($request->service_ids);

        return redirect()->back()->with('success', "Follow-up service added!");
    }

    // 8. Client Requests Cancellation
    public function requestCancel(Request $request, $id)
    {
        $queue = Queue::findOrFail($id);
        
        $queue->update([
            'status'  => 'cancel_requested',
            'remarks' => $request->remarks ?? 'Client requested cancellation',
        ]);

        return redirect()->back()->with('success', "Cancellation requested. waiting for admin approval.");
    }

    // --- HELPER ---
    private function generateQueueNumber()
    {
        // Counts all tickets created today to generate the next number (e.g., WS-001)
        $countToday = Queue::whereDate('created_at', today())->count();
        return 'WS-' . str_pad($countToday + 1, 3, '0', STR_PAD_LEFT);
    }
}