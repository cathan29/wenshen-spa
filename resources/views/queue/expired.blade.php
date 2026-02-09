<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Ticket Expired - Wenshen Spa</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700&display=swap');
        .luxury-font { font-family: 'Playfair Display', serif; }
    </style>
</head>
<body class="min-h-screen flex flex-col items-center justify-center p-6" style="background-color: #F9F3E3;">

    <div class="bg-white shadow-xl rounded-sm p-8 w-full max-w-sm text-center border-t-8 border-gray-300 relative">
        
        {{-- Disabled/Lock Icon --}}
        <div class="mx-auto w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mb-6">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
            </svg>
        </div>

        <h1 class="text-2xl font-bold text-gray-700 mb-2 luxury-font">Ticket Expired</h1>
        <p class="text-sm text-gray-500 mb-8">This queue number has already been served and is no longer valid.</p>

        <div class="border-t border-gray-200 pt-6">
            <p class="text-xs uppercase tracking-widest text-gray-400 mb-2">Thank you for visiting</p>
            <p class="font-bold text-gray-600 luxury-font text-lg">Wenshen Beauty Spa</p>
        </div>

        {{-- Watermark --}}
        <div class="absolute inset-0 flex items-center justify-center opacity-5 pointer-events-none">
            <span class="text-9xl font-black transform -rotate-45">USED</span>
        </div>
    </div>

</body>
</html>