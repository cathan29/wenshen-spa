<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Queue;
use App\Models\Service;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB; 

class AdminController extends Controller
{
    // 🏠 Dashboard Overview (The Owner's Office)
    public function index()
    {
        // 1. Calculate Today's Earnings
        $todayEarnings = Queue::whereDate('updated_at', today())
            ->where('status', 'completed')
            ->with('service')
            ->get()
            ->sum(fn($q) => $q->service->price);

        // 2. Calculate Monthly Revenue
        $monthEarnings = Queue::whereMonth('updated_at', Carbon::now()->month)
            ->where('status', 'completed')
            ->with('service')
            ->get()
            ->sum(fn($q) => $q->service->price);

        // 3. Count Clients Today
        $todayCustomers = Queue::whereDate('created_at', today())->count();

        // 4. 👑 Most Popular Service
        $topService = Queue::where('status', 'completed')
            ->select('service_id', DB::raw('count(*) as total'))
            ->groupBy('service_id')
            ->orderByDesc('total')
            ->with('service')
            ->first();

        // 5. 💎 VIP Client
        $topCustomer = Queue::where('status', 'completed')
            ->select('customer_name', DB::raw('count(*) as visits'))
            ->groupBy('customer_name')
            ->orderByDesc('visits')
            ->first();

        // 6. Recent History
        $recentTransactions = Queue::where('status', 'completed')
            ->with('service')
            ->latest('updated_at')
            ->take(5)
            ->get();

        return view('admin.dashboard', compact(
            'todayEarnings', 
            'monthEarnings', 
            'todayCustomers', 
            'topService', 
            'topCustomer', 
            'recentTransactions'
        ));
    }

    // 📈 Analytics & Reports (The Charts Page)
    public function reports()
    {
        // 1. Get Revenue Data for the Chart (Last 7 Days)
        $revenueData = Queue::where('status', 'completed')
            ->where('updated_at', '>=', now()->subDays(7)) // Look back 7 days
            ->with('service')
            ->get()
            ->groupBy(function($data) {
                // Group transactions by Date (e.g., "Feb 12")
                return $data->updated_at->format('M d');
            })
            ->map(function ($dayRows) {
                // Sum the price for that specific day
                return $dayRows->sum(fn($q) => $q->service->price);
            });

        // Prepare simple arrays for Chart.js
        $chartLabels = $revenueData->keys();
        $chartValues = $revenueData->values();

        // 2. Total Lifetime Earnings (Since the beginning)
        $totalLifetime = Queue::where('status', 'completed')
            ->with('service')
            ->get()
            ->sum(fn($q) => $q->service->price);

        return view('admin.reports', compact('chartLabels', 'chartValues', 'totalLifetime'));
    }
}