<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>My Queue Status - Wenshen Spa</title>
    
    {{-- 👇 LIVE UPDATE: Auto-refreshes every 10 seconds --}}
    <meta http-equiv="refresh" content="10">
    
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700;900&display=swap');
        .luxury-font { font-family: 'Playfair Display', serif; }

        @keyframes pulse-gold {
            0%, 100% { box-shadow: 0 0 0 0 rgba(212, 175, 55, 0.4); }
            70% { box-shadow: 0 0 0 15px rgba(212, 175, 55, 0); }
        }
        .animate-pulse-gold {
            animation: pulse-gold 2s infinite;
        }
    </style>
</head>
<body class="min-h-screen flex flex-col items-center justify-center p-6" style="background-color: #F9F3E3;">

    <div class="bg-white shadow-2xl rounded-sm p-8 w-full max-w-sm text-center border-t-8 relative overflow-hidden" style="border-color: #D4AF37;">
        
        {{-- Background Watermark --}}
        <div class="absolute -right-10 -top-10 text-9xl opacity-5 pointer-events-none select-none" style="color: #D4AF37;">⚜️</div>

        {{-- Header --}}
        <h1 class="font-bold tracking-[0.2em] uppercase text-xs mb-8" style="color: #6B4E31;">Wenshen Beauty Spa</h1>
        
        {{-- Queue Number --}}
        <div class="mb-8 relative z-10">
            <span class="block text-7xl font-black tracking-tighter luxury-font" style="color: #2C2015;">
                {{ $queue->queue_number }}
            </span>
            <span class="text-xs uppercase tracking-widest font-semibold mt-2 block" style="color: #D4AF37;">Ticket Number</span>
        </div>

        {{-- Dynamic Status Box --}}
        @if($queue->status === 'waiting')
            
            {{-- CASE 1: THEY ARE NEXT (0 People Ahead) --}}
            @if(isset($peopleAhead) && $peopleAhead === 0)
                <div class="p-6 rounded border mb-8 animate-pulse" style="background-color: #FFF3CD; border-color: #FFC107; color: #856404;">
                    <div class="flex justify-center mb-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                    </div>
                    <p class="font-bold text-lg mb-1 uppercase tracking-wide">Heads Up!</p>
                    <p class="text-sm font-bold">You are the NEXT person in line.</p>
                    <p class="text-xs mt-2">Please return to the waiting area immediately.</p>
                </div>

            {{-- CASE 2: STILL WAITING (1+ People Ahead) --}}
            @else
                <div class="p-6 rounded border mb-8" style="background-color: #FDFBF7; border-color: #E5E5E5; color: #6B4E31;">
                    <p class="font-bold text-xl mb-1">You are in Line</p>
                    <p class="text-sm opacity-80 mb-4">Please relax and wait for your turn.</p>
                    
                    {{-- People Ahead Counter --}}
                    @if(isset($peopleAhead))
                        <div class="inline-flex items-center px-4 py-2 rounded-full border text-xs font-bold uppercase tracking-wider" style="background-color: #F9F3E3; border-color: #D4AF37; color: #6B4E31;">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                            </svg>
                            <span class="text-lg mr-1 font-black">{{ $peopleAhead }}</span> people ahead
                        </div>
                    @endif
                </div>
            @endif

        @elseif($queue->status === 'serving')
            <div class="p-6 rounded border mb-8 animate-pulse-gold" style="background-color: #2C2015; border-color: #D4AF37; color: #D4AF37;">
                <p class="font-bold text-2xl uppercase tracking-wider mb-2">Now Serving</p>
                <p class="text-xs text-white opacity-90">It is your turn! Please proceed.</p>
            </div>

        @elseif($queue->status === 'completed')
            <div class="p-6 rounded border mb-8" style="background-color: #E8F5E9; border-color: #C8E6C9; color: #2E7D32;">
                <p class="font-bold text-xl">Completed</p>
                <p class="text-xs opacity-80">Thank you for visiting.</p>
            </div>

        @elseif($queue->status === 'cancelled')
             <div class="p-6 rounded border mb-8 bg-red-50 text-red-800 border-red-200">
                <p class="font-bold text-xl">Cancelled</p>
                <p class="text-xs opacity-80">Please see the receptionist.</p>
            </div>
        @endif

        {{-- Service Details --}}
        <div class="border-t pt-6 text-left relative z-10" style="border-color: #F3E5AB;">
            <div class="flex justify-between items-center mb-3">
                <span class="text-xs uppercase tracking-wide opacity-60" style="color: #6B4E31;">Service</span>
                <span class="font-bold text-sm" style="color: #2C2015;">{{ $queue->service->service_name }}</span>
            </div>
            <div class="flex justify-between items-center">
                <span class="text-xs uppercase tracking-wide opacity-60" style="color: #6B4E31;">Time</span>
                <span class="font-bold text-sm" style="color: #2C2015;">{{ $queue->created_at->format('h:i A') }}</span>
            </div>
        </div>

        {{-- Auto-Refresh Note (No Emoji) --}}
        <div class="mt-8 flex items-center justify-center text-[10px] uppercase tracking-widest" style="color: #D4AF37;">
            <span class="w-2 h-2 rounded-full mr-2 animate-ping" style="background-color: #D4AF37;"></span>
            Live Updates Active
        </div>
    </div>

</body>
</html>