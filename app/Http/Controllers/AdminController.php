<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Queue;
use App\Models\Service;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB; 
use Barryvdh\DomPDF\Facade\Pdf;

class AdminController extends Controller
{
    // 🏠 Dashboard Overview (The Owner's Office)
    public function index()
    {
        // 1. Calculate Today's Earnings (Using new Multiple Services logic)
        $todayEarnings = Queue::whereDate('updated_at', today())
            ->where('status', 'completed')
            ->with('services') // 👈 Changed to plural
            ->get()
            ->sum(fn($q) => $q->total_price); // 👈 Uses your new Model helper!

        // 2. Calculate Monthly Revenue
        $monthEarnings = Queue::whereMonth('updated_at', Carbon::now()->month)
            ->where('status', 'completed')
            ->with('services') // 👈 Changed to plural
            ->get()
            ->sum(fn($q) => $q->total_price);

        // 3. Count Clients Today
        $todayCustomers = Queue::whereDate('created_at', today())->count();

        // 4. 👑 Most Popular Service (Updated for Pivot Table)
        $topService = Service::withCount(['queues' => function($q) {
            $q->where('status', 'completed');
        }])->orderByDesc('queues_count')->first();

        // 5. 💎 VIP Client
        $topCustomer = Queue::where('status', 'completed')
            ->select('customer_name', DB::raw('count(*) as visits'))
            ->groupBy('customer_name')
            ->orderByDesc('visits')
            ->first();

        // 6. Recent History
        $recentTransactions = Queue::where('status', 'completed')
            ->with('services') // 👈 Changed to plural
            ->latest('updated_at')
            ->take(5)
            ->get();

        // Format for Dashboard view backwards compatibility
        $formattedTopService = $topService ? (object)['total' => $topService->queues_count, 'service' => $topService] : null;

        return view('admin.dashboard', [
            'todayEarnings' => $todayEarnings, 
            'monthEarnings' => $monthEarnings, 
            'todayCustomers' => $todayCustomers, 
            'topService' => $formattedTopService, 
            'topCustomer' => $topCustomer, 
            'recentTransactions' => $recentTransactions
        ]);
    }

    // 📈 Analytics & Reports (The Filter Engine & Leaderboards)
    public function reports(Request $request)
    {
        // 👈 START THE BASE QUERY with 'services'
        $baseQuery = Queue::where('status', 'completed')->with('services');

        // Determine the date boundaries
        if ($request->filled('start_date') && $request->filled('end_date')) {
            $start = Carbon::parse($request->start_date)->startOfDay();
            $end = Carbon::parse($request->end_date)->endOfDay();
            
            $baseQuery->whereBetween('updated_at', [$start, $end]);
            $chartQuery = clone $baseQuery; 
        } else {
            $start = now()->subDays(6)->startOfDay();
            $end = now()->endOfDay();
            
            $chartQuery = clone $baseQuery;
            $chartQuery->whereBetween('updated_at', [$start, $end]);
        }

        $allData = $baseQuery->get(); 
        $chartData = $chartQuery->get(); 

        // 3. CALCULATE REVENUE 
        $totalLifetime = $allData->sum(fn($q) => $q->total_price);

        // 4. CALCULATE CHART DATA WITH ZERO-FILLING
        $revenueData = collect();
        $currentDate = clone $start;
        
        while ($currentDate->lte($end)) {
            $revenueData->put($currentDate->format('M d'), 0);
            $currentDate->addDay();
        }

        // Pour actual sales into the skeleton
        foreach ($chartData as $data) {
            $dateKey = $data->updated_at->format('M d');
            if ($revenueData->has($dateKey)) {
                $revenueData[$dateKey] += $data->total_price; // 👈 Fixed for multiple services!
            }
        }

        $chartLabels = $revenueData->keys();
        $chartValues = $revenueData->values();

        // 5. 🏆 LEADERBOARD 1: TOP TREATMENTS (Flattened for pivot table)
        $treatmentStats = [];
        foreach($allData as $queue) {
            foreach($queue->services as $service) {
                if(!isset($treatmentStats[$service->id])) {
                    $treatmentStats[$service->id] = (object) [
                        'service_name' => $service->service_name,
                        'total_bookings' => 0,
                        'total_revenue' => 0,
                    ];
                }
                $treatmentStats[$service->id]->total_bookings += 1;
                $treatmentStats[$service->id]->total_revenue += $service->price;
            }
        }
        $topTreatments = collect($treatmentStats)->sortByDesc('total_revenue')->take(5)->values();

        // 6. 👑 LEADERBOARD 2: VIP CLIENTS
        $topClients = $allData->groupBy(fn($q) => strtolower(trim($q->customer_name)))
            ->map(function ($group) {
                return (object) [
                    'customer_name' => ucwords(strtolower(trim($group->first()->customer_name))),
                    'total_visits' => $group->count(),
                ];
            })->sortByDesc('total_visits')->take(5)->values(); 

        return view('admin.reports', compact(
            'chartLabels', 'chartValues', 'totalLifetime', 'topTreatments', 'topClients'
        ));
    }

    // 🖨️ Generate & Download PDF Report
    public function downloadReport(Request $request)
    {
        $baseQuery = Queue::where('status', 'completed')->with('services');
        $period = "All-Time Lifetime Report";

        if ($request->filled('start_date') && $request->filled('end_date')) {
            $start = Carbon::parse($request->start_date)->startOfDay();
            $end = Carbon::parse($request->end_date)->endOfDay();
            $baseQuery->whereBetween('updated_at', [$start, $end]);
            $period = $start->format('M d, Y') . ' to ' . $end->format('M d, Y');
        }

        $allData = $baseQuery->orderBy('updated_at', 'desc')->get();
        $totalRevenue = $allData->sum(fn($q) => $q->total_price);
        $totalClients = $allData->count();

        // Format services as a comma-separated string for the PDF
        foreach($allData as $data) {
            $data->service_list = $data->services->pluck('service_name')->join(', ');
        }

        $pdf = Pdf::loadView('admin.pdf_report', compact('allData', 'totalRevenue', 'totalClients', 'period'));
        return $pdf->download('Wenshen_Report_' . Carbon::now()->format('Y_m_d') . '.pdf');
    }

    // 📊 Generate & Download Excel (CSV) Report
    public function downloadExcel(Request $request)
    {
        $baseQuery = Queue::where('status', 'completed')->with('services');

        if ($request->filled('start_date') && $request->filled('end_date')) {
            $start = Carbon::parse($request->start_date)->startOfDay();
            $end = Carbon::parse($request->end_date)->endOfDay();
            $baseQuery->whereBetween('updated_at', [$start, $end]);
        }

        $allData = $baseQuery->orderBy('updated_at', 'desc')->get();
        $fileName = 'Wenshen_Sales_Report_' . Carbon::now()->format('Y_m_d') . '.csv';

        $headers = [
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$fileName",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $columns = ['Date & Time', 'Client Name', 'Treatments', 'Amount (Php)'];

        $callback = function() use($allData, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns); 

            foreach ($allData as $data) {
                $row = [
                    $data->updated_at->format('M d, Y h:i A'),
                    $data->customer_name,
                    $data->services->pluck('service_name')->join(', '), // 👈 Lists all treatments!
                    $data->total_price // 👈 Math is perfect
                ];
                fputcsv($file, $row);
            }

            $totalRevenue = $allData->sum(fn($q) => $q->total_price);
            fputcsv($file, ['', '', 'TOTAL REVENUE:', $totalRevenue]);

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}