<x-app-layout>
    <x-slot name="header">
        <h2 class="font-serif font-bold text-2xl text-[#6B4E31] uppercase tracking-widest">
            Analytics & Reports
        </h2>
    </x-slot>

    {{-- Import Chart.js Library --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <div class="py-12 min-h-screen" style="background-color: #F9F3E3;">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            {{-- 📅 DATE FILTER ENGINE --}}
            <div class="bg-white p-6 rounded-2xl shadow-xl border-t-4 border-[#D4AF37] mb-10 relative overflow-hidden">
                
                {{-- Subtle Watermark --}}
                <div class="absolute -right-4 -top-4 text-[#F9F3E3] opacity-30 pointer-events-none">
                    <svg class="w-32 h-32" fill="currentColor" viewBox="0 0 24 24"><path d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                </div>

                <div class="relative z-10">
                    <div class="flex items-center gap-2 mb-4">
                        <h3 class="font-bold text-lg text-[#6B4E31]">Sales & Queue Filter</h3>
                        <span class="text-[10px] bg-[#F9F3E3] text-[#6B4E31] px-2 py-1 rounded uppercase tracking-widest font-bold">Query Engine</span>
                    </div>

                    <form action="{{ route('admin.reports') }}" method="GET" class="flex flex-col sm:flex-row items-end gap-4">
                        
                        {{-- Start Date --}}
                        <div class="w-full sm:w-auto flex-1">
                            <label for="start_date" class="block text-xs font-bold uppercase text-gray-400 tracking-widest mb-2">From Date</label>
                            <input type="date" id="start_date" name="start_date" value="{{ request('start_date') }}" 
                                   class="w-full bg-[#F9F3E3] border border-[#eaddc5] text-[#6B4E31] rounded-lg py-3 px-4 focus:ring-[#D4AF37] focus:border-[#D4AF37] transition font-bold">
                        </div>

                        {{-- End Date --}}
                        <div class="w-full sm:w-auto flex-1">
                            <label for="end_date" class="block text-xs font-bold uppercase text-gray-400 tracking-widest mb-2">To Date</label>
                            <input type="date" id="end_date" name="end_date" value="{{ request('end_date') }}" 
                                   class="w-full bg-[#F9F3E3] border border-[#eaddc5] text-[#6B4E31] rounded-lg py-3 px-4 focus:ring-[#D4AF37] focus:border-[#D4AF37] transition font-bold">
                        </div>

                        {{-- Buttons --}}
                        <div class="w-full sm:w-auto flex gap-2">
                            <button type="submit" class="flex-1 sm:flex-none bg-[#6B4E31] text-white font-bold py-3 px-8 rounded-lg shadow-md hover:bg-[#5a422a] transition uppercase text-xs tracking-wider flex items-center justify-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path></svg>
                                Apply Filter
                            </button>
                            
                            {{-- Only show the Clear button if dates are currently applied --}}
                            @if(request()->has('start_date') || request()->has('end_date'))
                                <a href="{{ route('admin.reports') }}" class="flex-1 sm:flex-none bg-white border border-gray-300 text-gray-600 font-bold py-3 px-6 rounded-lg shadow-sm hover:bg-gray-50 transition uppercase text-xs tracking-wider flex items-center justify-center">
                                    Clear
                                </a>
                            @endif
                        </div>
                    </form>
                </div>
            </div>

            {{-- 1. Lifetime/Filtered Stats & Export Buttons --}}
            <div class="bg-[#6B4E31] text-white rounded-2xl p-8 shadow-xl mb-10 flex flex-col md:flex-row items-start md:items-center justify-between relative overflow-hidden gap-6">
                <div class="absolute -left-10 -bottom-10 opacity-10 text-white">
                    <svg class="w-64 h-64" fill="currentColor" viewBox="0 0 24 24"><path d="M16 6l2.29 2.29-4.88 4.88-4-4L2 16.59 3.41 18l6-6 4 4 6.3-6.29L22 12V6z"/></svg>
                </div>
                <div class="relative z-10">
                    <h3 class="text-xs font-bold uppercase tracking-[0.2em] text-[#D4AF37] mb-2">
                        {{ request('start_date') && request('end_date') ? 'Selected Period Revenue' : 'Total Lifetime Revenue' }}
                    </h3>
                    <p class="font-serif text-5xl">₱{{ number_format($totalLifetime, 0) }}</p>
                </div>
                
                {{-- 📥 DOWNLOAD BUTTONS --}}
                <div class="relative z-10 w-full md:w-auto flex flex-col sm:flex-row gap-3">
                    {{-- 🟢 EXCEL BUTTON --}}
                    <a href="{{ route('admin.reports.excel', request()->all()) }}" 
                       class="flex flex-1 items-center justify-center gap-2 bg-[#1D6F42] text-white px-6 py-3 rounded-lg text-xs font-bold uppercase tracking-widest hover:bg-[#155331] transition shadow-lg border border-[#1D6F42]">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        Excel
                    </a>

                    {{-- ⚪ PDF BUTTON --}}
                    <a href="{{ route('admin.reports.download', request()->all()) }}" 
                       class="flex flex-1 items-center justify-center gap-2 bg-white text-[#6B4E31] px-6 py-3 rounded-lg text-xs font-bold uppercase tracking-widest hover:bg-[#D4AF37] hover:text-white transition shadow-lg">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        PDF
                    </a>
                </div>
            </div>

            {{-- 2. The Revenue Chart --}}
            <div class="bg-white rounded-2xl shadow-xl p-8 border border-[#eaddc5] mb-10">
                <div class="flex justify-between items-end mb-6">
                    <div>
                        <h3 class="font-serif font-bold text-xl text-[#6B4E31]">Revenue Trend</h3>
                        <p class="text-xs text-gray-400 uppercase tracking-widest">
                            {{ request('start_date') && request('end_date') ? 'Filtered Custom Range' : 'Last 7 Days Performance' }}
                        </p>
                    </div>
                </div>
                
                {{-- Canvas for Chart.js --}}
                <div class="w-full h-[400px]">
                    <canvas id="revenueChart"></canvas>
                </div>
            </div>

            {{-- 3. THE LEADERBOARDS (Most Sales & VIP Clients) --}}
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-10">
                
                {{-- 🏆 LEADERBOARD 1: Top Treatments --}}
                <div class="bg-white rounded-2xl shadow-xl p-8 border-t-8 border-[#D4AF37] relative overflow-hidden">
                    <div class="absolute -right-4 -top-4 text-[#F9F3E3] opacity-50 pointer-events-none">
                        <svg class="w-24 h-24" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>
                    </div>
                    <div class="relative z-10">
                        <h3 class="font-serif font-bold text-xl text-[#6B4E31]">Top Performing Treatments</h3>
                        <p class="text-xs text-gray-400 uppercase tracking-widest mb-6">By Revenue & Popularity</p>
                        
                        <div class="space-y-4">
                            @forelse($topTreatments ?? [] as $treatment)
                                <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg border border-gray-100 hover:border-[#D4AF37] transition">
                                    <div class="flex items-center gap-3">
                                        <div class="bg-[#F9F3E3] text-[#6B4E31] font-black text-lg w-8 h-8 flex items-center justify-center rounded-full shadow-sm">
                                            {{ $loop->iteration }}
                                        </div>
                                        <div>
                                            <p class="font-bold text-sm text-gray-800">{{ $treatment->service_name }}</p>
                                            <p class="text-[10px] text-gray-500 uppercase tracking-wider">{{ $treatment->total_bookings }} Bookings</p>
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <p class="font-serif font-bold text-[#6B4E31]">₱{{ number_format($treatment->total_revenue, 0) }}</p>
                                    </div>
                                </div>
                            @empty
                                <p class="text-xs text-gray-400 italic text-center py-4">No data available for this period.</p>
                            @endforelse
                        </div>
                    </div>
                </div>

                {{-- 👑 LEADERBOARD 2: VIP Clients --}}
                <div class="bg-white rounded-2xl shadow-xl p-8 border-t-8 border-[#6B4E31] relative overflow-hidden">
                    <div class="absolute -right-4 -top-4 text-[#F9F3E3] opacity-50 pointer-events-none">
                        <svg class="w-24 h-24" fill="currentColor" viewBox="0 0 24 24"><path d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                    </div>
                    <div class="relative z-10">
                        <h3 class="font-serif font-bold text-xl text-[#6B4E31]">VIP Clients</h3>
                        <p class="text-xs text-gray-400 uppercase tracking-widest mb-6">Most Frequent Visitors</p>
                        
                        <div class="space-y-4">
                            @forelse($topClients ?? [] as $client)
                                <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg border border-gray-100 hover:border-[#6B4E31] transition">
                                    <div class="flex items-center gap-3">
                                        <div class="bg-gray-200 text-gray-600 font-black text-lg w-8 h-8 flex items-center justify-center rounded-full shadow-sm">
                                            {{ $loop->iteration }}
                                        </div>
                                        <div>
                                            <p class="font-bold text-sm text-gray-800">{{ $client->customer_name }}</p>
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <span class="bg-[#D4AF37] text-white text-[10px] font-bold uppercase tracking-widest px-3 py-1 rounded-full shadow-sm">
                                            {{ $client->total_visits }} Visits
                                        </span>
                                    </div>
                                </div>
                            @empty
                                <p class="text-xs text-gray-400 italic text-center py-4">No data available for this period.</p>
                            @endforelse
                        </div>
                    </div>
                </div>

            </div>

        </div>
    </div>

    {{-- 3. The Chart Script --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const ctx = document.getElementById('revenueChart').getContext('2d');
            
            // Data from Controller
            const labels = @json($chartLabels);
            const data = @json($chartValues);

            new Chart(ctx, {
                type: 'line', // Trend Line
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Daily Revenue (₱)',
                        data: data,
                        borderColor: '#6B4E31',       // Brown Line
                        backgroundColor: '#D4AF37',   // Gold Dots
                        borderWidth: 3,
                        pointRadius: 6,
                        pointBackgroundColor: '#ffffff',
                        pointBorderColor: '#6B4E31',
                        pointBorderWidth: 2,
                        tension: 0.4, // Smooth curves
                        fill: false
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: { color: '#f3f3f3' },
                            ticks: {
                                callback: function(value) { return '₱' + value; },
                                font: { family: 'serif' }
                            }
                        },
                        x: {
                            grid: { display: false },
                            ticks: { font: { family: 'sans-serif' } }
                        }
                    },
                    plugins: {
                        legend: { display: false }, // Hide legend for cleaner look
                        tooltip: {
                            backgroundColor: '#6B4E31',
                            titleFont: { family: 'serif', size: 14 },
                            bodyFont: { family: 'sans-serif', size: 12 },
                            padding: 10,
                            displayColors: false,
                            callbacks: {
                                label: function(context) {
                                    return 'Revenue: ₱' + context.parsed.y.toLocaleString();
                                }
                            }
                        }
                    }
                }
            });
        });
    </script>
</x-app-layout>