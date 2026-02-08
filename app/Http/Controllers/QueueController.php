<?php

namespace App\Http\Controllers;

use App\Models\Queue;
use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Str; // For generating the random QR token

class QueueController extends Controller
{
    // 1. Show the Dashboard with the Queue Form and Active List
    public function index()
    {
        $services = Service::where('is_active', true)->get();
        
        // Get everyone who is currently waiting or being served
        $todaysQueue = Queue::whereDate('created_at', today())
                            ->whereIn('status', ['waiting', 'serving'])
                            ->orderBy('id', 'asc')
                            ->get();

        return view('dashboard', compact('services', 'todaysQueue'));
    }

    // 2. Handle the "Add to Queue" Form Submit
    public function store(Request $request)
    {
        $request->validate([
            'service_id' => 'required|exists:services,id',
            'customer_name' => 'nullable|string|max:255',
        ]);

        // A. Generate the Queue Number (e.g., WS-001)
        // Count how many people joined today to find the next number
        $countToday = Queue::whereDate('created_at', today())->count();
        $nextNumber = 'WS-' . str_pad($countToday + 1, 3, '0', STR_PAD_LEFT); 

        // B. Create the Record
        Queue::create([
            'queue_number' => $nextNumber,
            'service_id' => $request->service_id,
            'customer_name' => $request->customer_name ?? 'Guest', // Default to "Guest" if empty
            'qr_token' => Str::random(32), // Unique key for their QR code
            'status' => 'waiting',
        ]);

        return redirect()->route('dashboard')->with('success', "Client added! Queue Number: $nextNumber");
    }

    // 3. Show the Client their specific Status Page
    public function show($qr_token)
    {
        // Find the ticket by the secure token
        $queue = Queue::where('qr_token', $qr_token)->firstOrFail();

        // We will build the 'queue.status' view in the next step
        return view('queue.status', compact('queue'));
    }
}