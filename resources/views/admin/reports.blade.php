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
            
            {{-- 1. Lifetime Stats --}}
            <div class="bg-[#6B4E31] text-white rounded-2xl p-8 shadow-xl mb-10 flex items-center justify-between relative overflow-hidden">
                <div class="absolute -left-10 -bottom-10 opacity-10 text-white">
                    <svg class="w-64 h-64" fill="currentColor" viewBox="0 0 24 24"><path d="M16 6l2.29 2.29-4.88 4.88-4-4L2 16.59 3.41 18l6-6 4 4 6.3-6.29L22 12V6z"/></svg>
                </div>
                <div class="relative z-10">
                    <h3 class="text-xs font-bold uppercase tracking-[0.2em] text-[#D4AF37] mb-2">Total Lifetime Revenue</h3>
                    <p class="font-serif text-5xl">₱{{ number_format($totalLifetime, 0) }}</p>
                </div>
                <div class="relative z-10 text-right">
                    <button class="bg-white text-[#6B4E31] px-6 py-3 rounded-lg text-xs font-bold uppercase tracking-widest hover:bg-[#D4AF37] hover:text-white transition">
                        Download PDF Report
                    </button>
                </div>
            </div>

            {{-- 2. The Revenue Chart --}}
            <div class="bg-white rounded-2xl shadow-xl p-8 border border-[#eaddc5] mb-10">
                <div class="flex justify-between items-end mb-6">
                    <div>
                        <h3 class="font-serif font-bold text-xl text-[#6B4E31]">Revenue Trend</h3>
                        <p class="text-xs text-gray-400 uppercase tracking-widest">Last 7 Days Performance</p>
                    </div>
                </div>
                
                {{-- Canvas for Chart.js --}}
                <div class="w-full h-[400px]">
                    <canvas id="revenueChart"></canvas>
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