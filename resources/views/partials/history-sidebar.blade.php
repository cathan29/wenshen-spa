<div x-data="{ showHistory: false }" 
     @open-history.window="showHistory = true" 
     @keydown.escape.window="showHistory = false"
     x-show="showHistory" 
     style="display: none;" 
     class="fixed inset-0 z-[9999]" 
     aria-labelledby="slide-over-title" 
     role="dialog" 
     aria-modal="true">
    
    {{-- 1. BACKDROP (Dark Overlay) --}}
    <div x-show="showHistory" 
         x-transition:enter="ease-in-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="ease-in-out duration-300"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="absolute inset-0 bg-stone-900/60 backdrop-blur-sm transition-opacity"
         @click="showHistory = false">
    </div>

    {{-- 2. SIDEBAR PANEL --}}
    <div class="fixed inset-y-0 right-0 flex max-w-full pl-10 pointer-events-none">
        
        <div x-show="showHistory" 
             x-transition:enter="transform transition ease-in-out duration-500 sm:duration-700"
             x-transition:enter-start="translate-x-full"
             x-transition:enter-end="translate-x-0"
             x-transition:leave="transform transition ease-in-out duration-500 sm:duration-700"
             x-transition:leave-start="translate-x-0"
             x-transition:leave-end="translate-x-full"
             class="pointer-events-auto w-screen max-w-md h-full relative bg-white shadow-2xl">
            
            {{-- 🟢 A. FIXED HEADER (Top 0) --}}
            <div class="absolute top-0 left-0 right-0 h-20 px-6 py-6 bg-stone-900 text-white border-b border-stone-700 z-20 flex items-start justify-between shadow-md">
                <div>
                    <h2 class="text-lg font-bold leading-6 uppercase tracking-widest text-amber-500">History</h2>
                    <p class="mt-1 text-xs text-stone-400">Click 'Restore' to undo.</p>
                </div>
                <button @click="showHistory = false" type="button" class="text-stone-400 hover:text-white">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            {{-- 🟡 B. SCROLLABLE CONTENT (Locked between Header & Footer) --}}
            <div class="absolute top-20 bottom-24 left-0 right-0 overflow-y-auto bg-stone-50 p-4">
                @if($completedQueue->isEmpty())
                    <div class="flex flex-col items-center justify-center h-full opacity-40">
                        <p class="text-stone-400 text-sm italic font-bold uppercase tracking-widest">No history found</p>
                    </div>
                @else
                    <div class="space-y-3">
                        @foreach($completedQueue as $h)
                            <div class="bg-white p-4 rounded-lg border border-stone-200 shadow-sm flex flex-col gap-2">
                                <div class="flex justify-between items-center">
                                    <span class="font-black text-2xl text-stone-800">{{ $h->queue_number }}</span>
                                    @if($h->status == 'completed')
                                        <span class="text-[10px] uppercase px-2 py-1 rounded bg-green-100 text-green-800 font-bold">Done</span>
                                    @elseif($h->status == 'cancelled')
                                        <span class="text-[10px] uppercase px-2 py-1 rounded bg-red-100 text-red-800 font-bold">Cancelled</span>
                                    @else
                                        <span class="text-[10px] uppercase px-2 py-1 rounded bg-gray-100 text-gray-600 font-bold">No Show</span>
                                    @endif
                                </div>
                                <div class="flex justify-between items-center border-t border-stone-100 pt-2">
                                    <div class="flex flex-col">
                                        <span class="text-sm font-bold text-stone-700">{{ $h->customer_name }}</span>
                                        <span class="text-[10px] font-bold text-stone-500 uppercase">{{ $h->service->service_name }}</span>
                                    </div>
                                    
                                    {{-- Restore Button --}}
                                    <form action="{{ route('queue.updateStatus', $h->id) }}" method="POST">
                                        @csrf @method('PATCH')
                                        <input type="hidden" name="status" value="waiting">
                                        <button type="submit" class="bg-blue-50 text-blue-600 hover:bg-blue-600 hover:text-white text-[10px] font-bold uppercase px-3 py-2 rounded transition border border-blue-200">
                                            Restore
                                        </button>
                                    </form>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            {{-- 🔴 C. FIXED FOOTER (Bottom 0) --}}
            <div class="absolute bottom-0 left-0 right-0 h-24 p-4 bg-white border-t border-stone-200 z-20 flex items-center shadow-[0_-4px_6px_-1px_rgba(0,0,0,0.1)]">
                <button @click="showHistory = false" class="w-full h-12 bg-stone-800 text-white font-bold uppercase text-sm tracking-widest rounded-lg hover:bg-stone-700 transition shadow-lg flex justify-center items-center gap-2">
                    Close Panel
                </button>
            </div>

        </div>
    </div>
</div>