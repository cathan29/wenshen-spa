<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Wenshen Spa Pass</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <meta http-equiv="refresh" content="10">
    
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700;900&family=Lato:wght@300;400;700&display=swap');
        .font-serif { font-family: 'Playfair Display', serif; }
        .font-sans { font-family: 'Lato', sans-serif; }
        
        .text-gold {
            background: linear-gradient(to right, #bf953f, #fcf6ba, #b38728, #fbf5b7, #aa771c);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
    </style>
</head>
<body class="bg-stone-900 h-screen w-full overflow-hidden flex flex-col items-center justify-center relative">

    {{-- Ambient Glow --}}
    <div class="absolute top-[-20%] left-[-20%] w-[150vw] h-[150vw] bg-amber-900/20 rounded-full blur-[120px] pointer-events-none"></div>

    {{-- MAIN CARD --}}
    <div class="w-[85%] max-w-sm bg-stone-800/80 backdrop-blur-xl border border-stone-600/50 rounded-3xl shadow-2xl p-8 flex flex-col items-center justify-between relative z-10 aspect-[3/5]">
        
        {{-- Top: Logo & Status --}}
        <div class="text-center w-full">
            <h1 class="text-white text-[10px] uppercase tracking-[0.4em] mb-6 opacity-60 font-sans">Wenshen Beauty Spa</h1>
            
            @if($queue->status === 'serving')
                <div class="inline-block px-4 py-2 rounded-full border border-green-500/30 bg-green-500/10 backdrop-blur-md">
                    <span class="text-green-400 text-xs font-bold uppercase tracking-widest animate-pulse">Now Serving</span>
                </div>
            @elseif($queue->status === 'cancel_requested')
                <div class="inline-block px-4 py-2 rounded-full border border-red-500/30 bg-red-500/10 backdrop-blur-md">
                    <span class="text-red-400 text-xs font-bold uppercase tracking-widest">Cancelling...</span>
                </div>
            @else
                <div class="inline-block px-4 py-2 rounded-full border border-amber-500/30 bg-amber-500/10 backdrop-blur-md">
                    <span class="text-amber-400 text-xs font-bold uppercase tracking-widest">Waiting</span>
                </div>
            @endif
        </div>

        {{-- Center: The Number --}}
        <div class="text-center">
            <span class="block text-gold text-7xl font-black font-serif tracking-tighter drop-shadow-lg mb-2">
                {{ $queue->queue_number }}
            </span>
            <p class="text-stone-300 text-sm font-sans font-light uppercase tracking-wide border-t border-stone-600/50 pt-4 mt-2">
                {{ $queue->service->service_name }}
            </p>
        </div>

        {{-- Bottom: Position & Actions --}}
        <div class="w-full text-center">
            @if($queue->status === 'waiting')
                <div class="mb-8">
                    {{-- 🛡️ IMPROVED LOGIC: SHOWS "YOU ARE NEXT" INSTEAD OF "0 PEOPLE" --}}
                    @if($peopleAhead === 0)
                        <div class="flex flex-col items-center">
                            <span class="text-[#D4AF37] text-3xl font-serif mb-1">You are Next!</span>
                            <p class="text-stone-500 text-[10px] uppercase tracking-widest">Prepare to be called</p>
                        </div>
                    @else
                        <p class="text-stone-500 text-[10px] uppercase tracking-widest mb-1">People Ahead</p>
                        <p class="text-2xl text-white font-serif">{{ $peopleAhead }}</p>
                    @endif
                </div>
                
                {{-- Cancel Button --}}
                <div x-data="{ showModal: false }">
                    <button @click="showModal = true" class="text-stone-500 hover:text-red-400 text-[10px] uppercase tracking-[0.2em] transition-colors pb-2 border-b border-transparent hover:border-red-400/50 cursor-pointer">
                        Cancel Ticket
                    </button>

                    <div x-show="showModal" 
                         style="display: none;" 
                         class="fixed inset-0 z-[999] overflow-y-auto" 
                         aria-labelledby="modal-title" role="dialog" aria-modal="true">
                        
                        <div x-show="showModal" 
                             x-transition:enter="ease-out duration-300"
                             x-transition:enter-start="opacity-0" 
                             x-transition:enter-end="opacity-100"
                             x-transition:leave="ease-in duration-200"
                             x-transition:leave-start="opacity-100" 
                             x-transition:leave-end="opacity-0"
                             class="fixed inset-0 bg-black/80 backdrop-blur-sm transition-opacity"
                             @click="showModal = false"></div>

                        <div class="fixed inset-0 z-10 overflow-y-auto">
                            <div class="flex min-h-full items-center justify-center p-4 text-center">
                                <div x-show="showModal" 
                                     x-transition:enter="ease-out duration-300"
                                     x-transition:enter-start="opacity-0 scale-95" 
                                     x-transition:enter-end="opacity-100 scale-100"
                                     x-transition:leave="ease-in duration-200"
                                     x-transition:leave-start="opacity-100 scale-100" 
                                     x-transition:leave-end="opacity-0 scale-95"
                                     @click.away="showModal = false"
                                     class="relative transform overflow-hidden rounded-2xl bg-stone-900 border border-amber-900/50 text-left shadow-2xl transition-all w-full max-w-xs p-6 ring-1 ring-white/10 mx-auto">
                                    
                                    <div class="text-center mb-6">
                                        <h3 class="text-white font-serif text-lg mb-2">Leave the Queue?</h3>
                                        <p class="text-stone-400 text-xs">
                                            You will lose your spot for <span class="text-amber-500 font-bold">{{ $queue->queue_number }}</span>.
                                        </p>
                                    </div>
                                    
                                    <form action="{{ route('queue.requestCancel', $queue->id) }}" method="POST">
                                        @csrf @method('PATCH')
                                        <div class="mb-4">
                                            <input type="text" name="remarks" 
                                                   placeholder="Reason (Optional)" 
                                                   class="w-full bg-stone-800 border border-stone-700 rounded-lg text-white text-sm px-4 py-3 focus:outline-none focus:border-amber-500 focus:ring-1 focus:ring-amber-500 transition placeholder-stone-600 text-center">
                                        </div>
                                        
                                        <div class="grid grid-cols-2 gap-3">
                                            <button type="button" @click="showModal = false" class="py-3 rounded-lg bg-stone-800 text-stone-300 text-xs uppercase font-bold tracking-wider hover:bg-stone-700 transition">
                                                Back
                                            </button>
                                            <button type="submit" class="py-3 rounded-lg bg-red-900/80 text-red-200 text-xs uppercase font-bold tracking-wider hover:bg-red-800 transition shadow-lg border border-red-800/50">
                                                Confirm
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @elseif($queue->status === 'serving')
                <div class="flex flex-col items-center">
                    <p class="text-[#D4AF37] text-2xl font-serif mb-2 animate-bounce">It's Your Turn!</p>
                    <p class="text-stone-400 text-[10px] uppercase tracking-widest animate-pulse">Please proceed to the counter</p>
                </div>
            @endif
        </div>

    </div>

    {{-- Decorative Bottom Text --}}
    <p class="absolute bottom-6 text-stone-700 text-[8px] uppercase tracking-[0.3em]">Excellence in every detail</p>

</body>
</html>