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
        // 1. Calculate Today's Earnings
        $todayEarnings = Queue::whereDate('updated_at', today())
            ->where('status', 'completed')
            ->with('service')
            ->get()
            ->sum(fn($q) => $q->service ? $q->service->price : 0);

        // 2. Calculate Monthly Revenue
        $monthEarnings = Queue::whereMonth('updated_at', Carbon::now()->month)
            ->where('status', 'completed')
            ->with('service')
            ->get()
            ->sum(fn($q) => $q->service ? $q->service->price : 0);

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

    // 📈 Analytics & Reports (The Filter Engine & Leaderboards)
    public function reports(Request $request)
    {
        // 1. START THE BASE QUERY
        $baseQuery = Queue::where('status', 'completed')->with('service');

        // Determine the date boundaries
        if ($request->filled('start_date') && $request->filled('end_date')) {
            $start = Carbon::parse($request->start_date)->startOfDay();
            $end = Carbon::parse($request->end_date)->endOfDay();
            
            $baseQuery->whereBetween('updated_at', [$start, $end]);
            $chartQuery = clone $baseQuery; 
        } else {
            // Default: Last 7 Days (including today)
            $start = now()->subDays(6)->startOfDay();
            $end = now()->endOfDay();
            
            $chartQuery = clone $baseQuery;
            $chartQuery->whereBetween('updated_at', [$start, $end]);
        }

        // Execute the queries
        $allData = $baseQuery->get(); // All-time OR Filtered data
        $chartData = $chartQuery->get(); // 7-Days OR Filtered data

        // 3. CALCULATE REVENUE (Card #1)
        $totalLifetime = $allData->sum(fn($q) => $q->service ? $q->service->price : 0);

        // 4. CALCULATE CHART DATA WITH ZERO-FILLING
        $revenueData = collect();
        
        // Create an empty skeleton of dates from Start to End, initialized to 0
        $currentDate = clone $start;
        while ($currentDate->lte($end)) {
            $revenueData->put($currentDate->format('M d'), 0);
            $currentDate->addDay();
        }

        // Pour the actual sales into the skeleton
        foreach ($chartData as $data) {
            $dateKey = $data->updated_at->format('M d');
            if ($revenueData->has($dateKey)) {
                $revenueData[$dateKey] += $data->service ? $data->service->price : 0;
            }
        }

        // Format for Chart.js
        $chartLabels = $revenueData->keys();
        $chartValues = $revenueData->values();

        // 5. 🏆 LEADERBOARD 1: TOP TREATMENTS
        $topTreatments = $allData->groupBy('service_id')->map(function ($group) {
            return (object) [
                'service_name' => $group->first()->service ? $group->first()->service->service_name : 'Deleted Service',
                'total_bookings' => $group->count(),
                'total_revenue' => $group->sum(fn($q) => $q->service ? $q->service->price : 0),
            ];
        })->sortByDesc('total_revenue')->take(5)->values(); // Top 5 by Revenue

        // 6. 👑 LEADERBOARD 2: VIP CLIENTS
        $topClients = $allData->groupBy(fn($q) => strtolower(trim($q->customer_name)))
            ->map(function ($group) {
                return (object) [
                    // Capitalize names nicely (e.g. "juan dela cruz" -> "Juan Dela Cruz")
                    'customer_name' => ucwords(strtolower(trim($group->first()->customer_name))),
                    'total_visits' => $group->count(),
                ];
            })->sortByDesc('total_visits')->take(5)->values(); // Top 5 by Visits

        // Send everything to the Blade view!
        return view('admin.reports', compact(
            'chartLabels', 
            'chartValues', 
            'totalLifetime', 
            'topTreatments', 
            'topClients'
        ));
    }

    // 🖨️ Generate & Download PDF Report
    public function downloadReport(Request $request)
    {
        $baseQuery = Queue::where('status', 'completed')->with('service');
        $period = "All-Time Lifetime Report";

        // Apply Date Filter if present
        if ($request->filled('start_date') && $request->filled('end_date')) {
            $start = Carbon::parse($request->start_date)->startOfDay();
            $end = Carbon::parse($request->end_date)->endOfDay();
            $baseQuery->whereBetween('updated_at', [$start, $end]);
            
            $period = $start->format('M d, Y') . ' to ' . $end->format('M d, Y');
        }

        $allData = $baseQuery->orderBy('updated_at', 'desc')->get();
        $totalRevenue = $allData->sum(fn($q) => $q->service ? $q->service->price : 0);
        $totalClients = $allData->count();

        // Pass data to a specific PDF Blade view
        $pdf = Pdf::loadView('admin.pdf_report', compact('allData', 'totalRevenue', 'totalClients', 'period'));

        // Name the downloaded file
        $fileName = 'Wenshen_Report_' . Carbon::now()->format('Y_m_d') . '.pdf';

        // Download it!
        return $pdf->download($fileName);
    }

    // 📊 Generate & Download Excel (CSV) Report
    public function downloadExcel(Request $request)
    {
        $baseQuery = Queue::where('status', 'completed')->with('service');

        // Apply Date Filter if present
        if ($request->filled('start_date') && $request->filled('end_date')) {
            $start = Carbon::parse($request->start_date)->startOfDay();
            $end = Carbon::parse($request->end_date)->endOfDay();
            $baseQuery->whereBetween('updated_at', [$start, $end]);
        }

        $allData = $baseQuery->orderBy('updated_at', 'desc')->get();
        $fileName = 'Wenshen_Sales_Report_' . Carbon::now()->format('Y_m_d') . '.csv';

        // Set the headers to tell the browser this is an Excel/CSV file
        $headers = [
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$fileName",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        // Define the columns for the top of the Excel sheet
        $columns = ['Date & Time', 'Client Name', 'Treatment', 'Amount (Php)'];

        // Build the file stream
        $callback = function() use($allData, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns); // Write the header row

            // Loop through the data and write each row
            foreach ($allData as $data) {
                $row = [
                    $data->updated_at->format('M d, Y h:i A'),
                    $data->customer_name,
                    $data->service ? $data->service->service_name : 'Deleted Service',
                    $data->service ? $data->service->price : '0'
                ];
                fputcsv($file, $row);
            }

            // Add a final "Total Revenue" row at the bottom
            $totalRevenue = $allData->sum(fn($q) => $q->service ? $q->service->price : 0);
            fputcsv($file, ['', '', 'TOTAL REVENUE:', $totalRevenue]);

            fclose($file);
        };

        // Return the native Laravel file download
        return response()->stream($callback, 200, $headers);
    }
}