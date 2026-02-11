<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Wenshen Spa Monitor</title>
    <script src="https://cdn.tailwindcss.com"></script>
    
    {{-- 👇 AUTO-REFRESH EVERY 5 SECONDS --}}
    <meta http-equiv="refresh" content="5">
    
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700;900&family=Lato:wght@300;400;700&display=swap');
        .font-serif { font-family: 'Playfair Display', serif; }
        .font-sans { font-family: 'Lato', sans-serif; }
        
        @keyframes fadeUp {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .animate-fade-up { animation: fadeUp 0.8s ease-out forwards; }
    </style>
</head>
<body class="h-screen overflow-hidden flex font-sans" style="background-color: #1A120B;">

    {{-- 👈 LEFT SIDE: NOW SERVING (65% Width) --}}
    <div class="w-[65%] flex flex-col relative border-r-2 border-[#3E2F22] bg-[#23170F]">
        
        <div class="p-8 pb-4 text-center border-b border-[#3E2F22]">
            <h1 class="text-3xl uppercase tracking-[0.4em] font-light text-[#D4AF37] font-serif">Now Serving</h1>
        </div>

        <div class="flex-1 p-8 flex items-center justify-center relative">
            <div class="absolute top-0 left-0 w-32 h-32 border-t-2 border-l-2 border-[#D4AF37] opacity-20 m-4 rounded-tl-3xl"></div>
            <div class="absolute bottom-0 right-0 w-32 h-32 border-b-2 border-r-2 border-[#D4AF37] opacity-20 m-4 rounded-br-3xl"></div>

            @if($serving->isEmpty())
                <div class="text-center opacity-40 animate-pulse">
                    <span class="text-9xl block mb-6 filter drop-shadow-lg">⚜️</span>
                    <span class="text-3xl text-[#F9F3E3] tracking-[0.2em] uppercase font-serif">Please Wait</span>
                </div>
            @else
                {{-- 🛠️ DYNAMIC GRID: Adapts based on how many are being served --}}
                <div class="w-full h-full grid gap-8 
                    {{ $serving->count() === 1 ? 'grid-cols-1' : 'grid-cols-2' }} 
                    {{ $serving->count() > 2 ? 'grid-rows-2' : '' }} place-items-center">
                    
                    @foreach($serving as $s)
                        {{-- 🛡️ NEW MULTI-CALL TRIGGER: Uses class instead of ID --}}
                        @if(isset($recentlyCalled) && $recentlyCalled->contains('id', $s->id))
                            <div class="hidden recent-call-data" 
                                 data-id="{{ $s->id }}" 
                                 data-text="Now serving ticket number {{ $s->queue_number }}, {{ $s->customer_name }}">
                            </div>
                        @endif

                        <div class="animate-fade-up relative w-full h-full bg-[#F9F3E3] rounded-xl shadow-2xl flex flex-col justify-center items-center p-6 border-4 border-[#D4AF37] ring-4 ring-[#23170F] ring-offset-2 ring-offset-[#D4AF37]/30">
                            
                            {{-- Show Badge for ALL recently called clients --}}
                            @if(isset($recentlyCalled) && $recentlyCalled->contains('id', $s->id))
                                <div class="absolute top-0 right-0 bg-[#8B0000] text-white text-xs font-bold px-4 py-1.5 uppercase tracking-widest rounded-bl-xl shadow-md z-10 animate-bounce">
                                    Now Calling
                                </div>
                            @endif

                            <span class="font-black font-serif leading-none tracking-tighter text-[#6B4E31] drop-shadow-sm {{ $serving->count() > 1 ? 'text-7xl mb-2' : 'text-[10rem] mb-4' }}">
                                {{ $s->queue_number }}
                            </span>
                            
                            <div class="w-24 h-1.5 rounded-full bg-[#D4AF37] mb-4"></div>

                            <span class="font-bold uppercase tracking-wide text-[#2C2015] {{ $serving->count() > 1 ? 'text-2xl' : 'text-5xl mb-2' }}">
                                {{ $s->customer_name }}
                            </span>
                            
                            <span class="italic font-serif text-[#8C7B65] {{ $serving->count() > 1 ? 'text-lg' : 'text-2xl' }}">
                                {{ $s->service->service_name }}
                            </span>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>

    {{-- 👉 RIGHT SIDE: UP NEXT --}}
    <div class="w-[35%] flex flex-col bg-[#1A120B] border-l border-[#3E2F22]">
        <div class="p-8 pb-4 text-center border-b border-[#3E2F22]">
            <h2 class="text-xl uppercase tracking-[0.3em] font-light text-[#8C7B65]">Up Next</h2>
        </div>
        
        <div class="flex-1 p-6 overflow-y-auto space-y-4">
            @if($waiting->isEmpty())
                <div class="h-full flex flex-col items-center justify-center text-[#8C7B65] opacity-50">
                    <p class="italic font-serif text-lg">No pending clients.</p>
                </div>
            @else
                @foreach($waiting as $q)
                    <div class="group bg-[#2C2015] rounded-r-lg border-l-4 border-[#D4AF37] p-5 flex justify-between items-center shadow-md transform transition hover:scale-105">
                        <div>
                            <span class="block text-3xl font-bold font-serif text-[#F9F3E3] group-hover:text-[#D4AF37] transition">
                                {{ $q->queue_number }}
                            </span>
                            <span class="text-xs uppercase tracking-wider text-[#8C7B65]">
                                {{ $q->customer_name }}
                            </span>
                        </div>
                        <span class="text-[10px] border border-[#8C7B65] text-[#8C7B65] px-2 py-1 rounded uppercase tracking-widest">
                            Waiting
                        </span>
                    </div>
                @endforeach
            @endif
        </div>
        
        <div class="p-6 text-center border-t border-[#3E2F22] bg-[#150E08]">
             <p class="text-[10px] uppercase tracking-[0.4em] text-[#D4AF37] opacity-60">Wenshen Beauty Spa</p>
        </div>
    </div>

    {{-- 🔊 ADVANCED MULTI-VOICE AUDIO SYSTEM --}}
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const callData = document.querySelectorAll('.recent-call-data');
            
            callData.forEach((el, index) => {
                const id = el.getAttribute('data-id');
                const msg = el.getAttribute('data-text');
                const lastPlayed = localStorage.getItem('announced_' + id);

                // If this specific ticket hasn't been announced yet
                if (!lastPlayed) {
                    // Add a 4-second delay for each person so voices don't overlap
                    setTimeout(() => {
                        playAnnouncement(msg);
                        localStorage.setItem('announced_' + id, Date.now());
                    }, index * 4500); 
                }
            });

            function playAnnouncement(text) {
                let chime = new Audio('https://codeskulptor-demos.commondatastorage.googleapis.com/pang/pop.mp3');
                chime.volume = 1.0;
                
                chime.play().then(() => {
                    setTimeout(() => {
                        if ('speechSynthesis' in window) {
                            let utterance = new SpeechSynthesisUtterance(text);
                            let voices = speechSynthesis.getVoices();
                            let femaleVoice = voices.find(v => v.name.includes('Google US English') || v.name.includes('Female'));
                            
                            if (femaleVoice) utterance.voice = femaleVoice;
                            utterance.rate = 0.85;
                            window.speechSynthesis.speak(utterance);
                        }
                    }, 800);
                }).catch(e => console.log("Click the screen once to enable audio."));
            }

            // Cleanup old localStorage keys once in a while
            if (Math.random() < 0.1) {
                for (let i = 0; i < localStorage.length; i++){
                    let key = localStorage.key(i);
                    if (key.startsWith('announced_')) {
                        let timestamp = localStorage.getItem(key);
                        if (Date.now() - timestamp > 60000) localStorage.removeItem(key);
                    }
                }
            }
        });
    </script>
</body>
</html>