<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>My Queue Status - Wenshen Spa</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        /* Pulse animation for the "Serving" status */
        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: .5; }
        }
        .animate-pulse-slow {
            animation: pulse 3s cubic-bezier(0.4, 0, 0.6, 1) infinite;
        }
    </style>
</head>
<body class="bg-gray-100 min-h-screen flex flex-col items-center justify-center p-4">

    <div class="bg-white shadow-xl rounded-2xl p-8 w-full max-w-md text-center">
        
        {{-- Header --}}
        <h1 class="text-gray-500 font-medium tracking-widest uppercase text-sm mb-4">Wenshen Beauty Spa</h1>
        
        {{-- Queue Number --}}
        <div class="mb-6">
            <span class="block text-6xl font-black text-blue-600 tracking-tighter">
                {{ $queue->queue_number }}
            </span>
            <span class="text-gray-400 text-sm mt-1">Your Ticket Number</span>
        </div>

        {{-- Dynamic Status Box --}}
        @if($queue->status === 'waiting')
            <div class="bg-yellow-100 text-yellow-800 p-4 rounded-xl mb-6">
                <p class="font-bold text-lg">You are in Line</p>
                <p class="text-sm">Please wait for your number to be called.</p>
            </div>
        @elseif($queue->status === 'serving')
            <div class="bg-green-100 text-green-800 p-4 rounded-xl mb-6 animate-pulse-slow">
                <p class="font-bold text-lg">🎉 Now Serving!</p>
                <p class="text-sm">Please proceed to the treatment room.</p>
            </div>
        @elseif($queue->status === 'completed')
            <div class="bg-blue-100 text-blue-800 p-4 rounded-xl mb-6">
                <p class="font-bold text-lg">Completed</p>
                <p class="text-sm">Thank you for visiting us!</p>
            </div>
        @endif

        {{-- Service Details --}}
        <div class="border-t pt-4 text-left">
            <div class="flex justify-between items-center mb-2">
                <span class="text-gray-500">Service:</span>
                <span class="font-semibold text-gray-800">{{ $queue->service->service_name }}</span>
            </div>
            <div class="flex justify-between items-center">
                <span class="text-gray-500">Date:</span>
                <span class="font-semibold text-gray-800">{{ $queue->created_at->format('M d, h:i A') }}</span>
            </div>
        </div>

        {{-- Auto-Refresh Note --}}
        <p class="text-xs text-gray-400 mt-8">
            Refresh this page to see updates.
        </p>
    </div>

</body>
</html>