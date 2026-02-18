<x-app-layout>
    <x-slot name="header">
        <h2 class="font-serif font-bold text-2xl text-[#6B4E31] uppercase tracking-widest">
            Owner's Office
        </h2>
    </x-slot>

    <div class="py-12 min-h-screen" style="background-color: #F9F3E3;">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            {{-- 📊 ROW 1: FINANCIAL OVERVIEW --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-10">
                
                {{-- Today's Income --}}
                <div class="bg-[#6B4E31] rounded-2xl p-6 shadow-xl text-white relative overflow-hidden transform hover:-translate-y-1 transition duration-300">
                    <div class="absolute right-0 top-0 w-32 h-32 bg-white opacity-10 rounded-full -mr-10 -mt-10 blur-2xl"></div>
                    <p class="text-xs font-bold uppercase tracking-widest text-[#D4AF37] mb-1">Today's Earnings</p>
                    <h3 class="text-4xl font-serif">₱{{ number_format($todayEarnings, 0) }}</h3>
                </div>

                {{-- Monthly Income --}}
                <div class="bg-white rounded-2xl p-6 shadow-xl border-t-4 border-[#D4AF37] transform hover:-translate-y-1 transition duration-300">
                    <p class="text-xs font-bold uppercase tracking-widest text-stone-400 mb-1">Monthly Revenue</p>
                    <h3 class="text-4xl font-serif text-[#6B4E31]">₱{{ number_format($monthEarnings, 0) }}</h3>
                </div>

                {{-- Total Clients --}}
                <div class="bg-white rounded-2xl p-6 shadow-xl border-t-4 border-[#6B4E31] transform hover:-translate-y-1 transition duration-300">
                    <p class="text-xs font-bold uppercase tracking-widest text-stone-400 mb-1">Total Visits Today</p>
                    <h3 class="text-4xl font-serif text-[#6B4E31]">{{ $todayCustomers }}</h3>
                </div>
            </div>

            {{-- 🏆 ROW 2: TOP PERFORMERS --}}
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-10">
                
                {{-- Best Selling Service --}}
                <div class="bg-gradient-to-br from-white to-[#fffcf5] p-8 rounded-2xl shadow-lg border border-[#eaddc5] flex items-center justify-between relative overflow-hidden">
                    <div class="absolute top-0 right-0 p-4 opacity-10">
                        <svg class="w-32 h-32 text-[#D4AF37]" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>
                    </div>
                    <div>
                        <p class="text-xs font-bold uppercase tracking-widest text-[#D4AF37] mb-2">Most Popular Treatment</p>
                        @if($topService)
                            {{-- 👇 Fixed to use the direct service_name and queues_count --}}
                            <h3 class="text-2xl font-serif font-bold text-[#6B4E31] mb-1">{{ $topService->service_name }}</h3>
                            <p class="text-sm text-gray-500">Performed <span class="font-bold text-[#6B4E31]">{{ $topService->queues_count }} times</span> total</p>
                        @else
                            <p class="text-gray-400 italic">No data yet</p>
                        @endif
                    </div>
                    <div class="bg-[#D4AF37] text-white p-3 rounded-full shadow-lg z-10">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z" /></svg>
                    </div>
                </div>

                {{-- VIP Customer --}}
                <div class="bg-gradient-to-br from-white to-[#fffcf5] p-8 rounded-2xl shadow-lg border border-[#eaddc5] flex items-center justify-between relative overflow-hidden">
                    <div class="absolute top-0 right-0 p-4 opacity-10">
                        <svg class="w-32 h-32 text-[#6B4E31]" fill="currentColor" viewBox="0 0 24 24"><path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/></svg>
                    </div>
                    <div>
                        <p class="text-xs font-bold uppercase tracking-widest text-[#D4AF37] mb-2">Top Loyal Client</p>
                        @if($topCustomer)
                            <h3 class="text-2xl font-serif font-bold text-[#6B4E31] mb-1">{{ $topCustomer->customer_name }}</h3>
                            <p class="text-sm text-gray-500">Has visited <span class="font-bold text-[#6B4E31]">{{ $topCustomer->visits }} times</span></p>
                        @else
                            <p class="text-gray-400 italic">No data yet</p>
                        @endif
                    </div>
                    <div class="bg-[#6B4E31] text-white p-3 rounded-full shadow-lg z-10">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" /></svg>
                    </div>
                </div>
            </div>

            {{-- 📜 ROW 3: RECENT ACTIVITY LOG --}}
            <div class="bg-white rounded-2xl shadow-xl overflow-hidden border border-[#eaddc5]">
                <div class="p-6 border-b border-[#F9F3E3] bg-[#fffbf2] flex justify-between items-center">
                    <h3 class="text-lg font-bold text-[#6B4E31]">Recent Transaction History</h3>
                    <span class="text-xs font-bold text-gray-400 uppercase tracking-widest">Last 5 Clients</span>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead class="bg-[#F9F3E3] text-[#6B4E31] text-xs uppercase tracking-widest">
                            <tr>
                                <th class="p-4">Time</th>
                                <th class="p-4">Customer</th>
                                <th class="p-4">Service Rendered</th>
                                <th class="p-4 text-right">Amount</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-[#F9F3E3]">
                            @forelse($recentTransactions as $history)
                                <tr class="hover:bg-[#fffbf2] transition">
                                    <td class="p-4 text-xs font-bold text-gray-400">
                                        {{ $history->updated_at->format('h:i A') }} <br>
                                        <span class="text-[10px] font-normal">{{ $history->updated_at->format('M d, Y') }}</span>
                                    </td>
                                    <td class="p-4 font-bold text-stone-700">{{ $history->customer_name }}</td>
                                    <td class="p-4 text-sm text-gray-600">
                                        {{-- 👇 Loops through all treatments and creates multiple badges --}}
                                        @foreach($history->services as $s)
                                            <span class="inline-block bg-[#F9F3E3] text-[#6B4E31] px-2 py-1 rounded text-[10px] font-bold uppercase tracking-wider mr-1 mb-1">
                                                {{ $s->service_name }}
                                            </span>
                                        @endforeach
                                    </td>
                                    <td class="p-4 text-right font-serif text-[#6B4E31] font-bold">
                                        {{-- 👇 Now uses the correct calculated total price --}}
                                        ₱{{ number_format($history->total_price, 0) }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="p-8 text-center text-gray-400 italic">No completed transactions yet.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>