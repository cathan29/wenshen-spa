<x-app-layout>
    <x-slot name="header">
        <h2 class="font-serif font-bold text-2xl leading-tight uppercase tracking-[0.2em]" 
            style="background: linear-gradient(to right, #bf953f, #b38728, #aa771c); -webkit-background-clip: text; -webkit-text-fill-color: transparent;">
            {{ __('Receptionist Console') }}
        </h2>
    </x-slot>

    {{-- 🟢 MAIN WRAPPER --}}
    <div x-data="{ 
            showHistory: false,
            activeDropdown: null, 
            confirm: {
                open: false,
                title: '',
                message: '',
                btnText: 'Confirm',
                type: 'primary',
                action: null
            },
            ask(title, message, btnText, type, triggerEl) {
                // 1. Reset Modal State
                this.confirm.open = false; 
                this.confirm.action = null;

                // 2. Set Content
                this.confirm.title = title;
                this.confirm.message = message;
                this.confirm.btnText = btnText;
                this.confirm.type = type;

                // 3. Bind Action (Submit the specific form connected to the clicked button)
                let form = triggerEl.closest('form');
                if (form) {
                    this.confirm.action = () => form.submit();
                } else {
                    console.warn('No form found for confirmation');
                    return;
                }

                // 4. Open Modal using nextTick for reliable re-render
                this.$nextTick(() => {
                    this.confirm.open = true;
                });
            }
         }" 
         class="min-h-screen font-sans relative"
         style="background-color: #F9F3E3;">
        
        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                
                {{-- TOP TOOLBAR --}}
                <div class="flex justify-end mb-6">
                    <button @click="showHistory = true" 
                            class="flex items-center gap-2 bg-white border border-[#D4AF37] text-[#6B4E31] px-6 py-3 rounded-lg shadow-sm hover:shadow-md hover:bg-[#fff9ee] transition uppercase text-xs font-bold tracking-wider">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-[#D4AF37]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        History & Restore
                    </button>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                    
                    {{-- 👈 LEFT SIDE: REGISTER --}}
                    <div class="bg-white shadow-xl sm:rounded-2xl p-8 border-t-8 h-fit relative overflow-hidden" 
                         style="border-color: #6B4E31;">
                        
                        <div class="absolute top-0 right-0 -mt-4 -mr-4 w-24 h-24 bg-[#F9F3E3] rounded-full opacity-50 blur-xl"></div>

                        <h3 class="text-xl font-bold mb-8 flex items-center border-b pb-4 relative z-10" style="color: #6B4E31; border-color: #F9F3E3;">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mr-3 text-[#D4AF37]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                            </svg>
                            New Client Registration
                        </h3>
                        
                        <form action="{{ route('queue.store') }}" method="POST" class="relative z-10">
                            @csrf
                            
                            {{-- 🔄 MULTI-SELECT TREATMENTS --}}
                            <div class="mb-6">
                                <label class="block font-bold mb-2 text-xs uppercase tracking-widest text-[#6B4E31]">
                                    Select Services <span class="text-[9px] text-gray-400 normal-case">(Hold Ctrl/Cmd to select multiple)</span>
                                </label>
                                <div class="relative">
                                    <select name="service_ids[]" multiple class="block w-full bg-[#F9F3E3] border border-[#eaddc5] text-[#4a3b2a] py-3 px-4 rounded leading-tight focus:outline-none focus:bg-white focus:border-[#D4AF37] transition h-32" required>
                                        @foreach($services as $service)
                                            <option value="{{ $service->id }}" class="p-2 border-b border-[#eaddc5] hover:bg-[#D4AF37] hover:text-white transition cursor-pointer">
                                                {{ $service->service_name }} (₱{{ number_format($service->price, 0) }})
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="mb-8">
                                <label class="block font-bold mb-2 text-xs uppercase tracking-widest text-[#6B4E31]">Customer Name</label>
                                <input type="text" name="customer_name" class="appearance-none block w-full bg-[#F9F3E3] text-[#4a3b2a] border border-[#eaddc5] rounded py-3 px-4 leading-tight focus:outline-none focus:bg-white focus:border-[#D4AF37] transition" placeholder="e.g. Ma'am Jane">
                            </div>

                            <button type="submit" class="w-full text-white font-bold py-4 rounded shadow-lg hover:shadow-xl transition duration-200 uppercase tracking-widest text-sm flex justify-center items-center gap-2" style="background-color: #6B4E31;">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                </svg>
                                Add to Queue
                            </button>
                        </form>
                    </div>

                    {{-- 👉 RIGHT SIDE: QUEUE LIST --}}
                    <div class="bg-white shadow-xl sm:rounded-2xl p-8 border-t-8 min-h-[600px] relative" style="border-color: #D4AF37;">
                        
                        {{-- Cancellation Requests --}}
                        @php $requests = $todaysQueue->where('status', 'cancel_requested'); @endphp
                        @if($requests->count() > 0)
                            <div class="mb-8 border-l-4 border-red-500 bg-red-50 p-6 rounded shadow-sm">
                                <div class="flex items-center mb-4">
                                    <div class="bg-red-100 p-2 rounded-full mr-3"><svg class="w-5 h-5 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" /></svg></div>
                                    <h4 class="text-red-900 font-bold text-sm uppercase tracking-widest">Requests ({{ $requests->count() }})</h4>
                                </div>
                                <div class="space-y-3">
                                    @foreach($requests as $req)
                                        <div class="bg-white p-4 rounded-lg shadow border border-red-100 flex flex-col sm:flex-row justify-between items-center gap-4">
                                            <div class="flex items-center gap-4 w-full sm:w-auto">
                                                <span class="text-2xl font-black text-[#6B4E31]">{{ $req->queue_number }}</span>
                                                <div class="border-l pl-4 border-gray-200">
                                                    <p class="text-[10px] font-bold text-gray-500 uppercase">Reason:</p>
                                                    <p class="text-sm font-medium text-red-600 italic">"{{ $req->remarks ?? 'No reason' }}"</p>
                                                </div>
                                            </div>
                                            <div class="flex gap-2 w-full sm:w-auto">
                                                <form action="{{ route('queue.updateStatus', $req->id) }}" method="POST" class="flex-1">
                                                    @csrf @method('PATCH')
                                                    <input type="hidden" name="status" value="waiting">
                                                    <button type="submit" class="w-full px-4 py-2 bg-white border border-stone-300 rounded text-gray-600 text-xs font-bold uppercase hover:bg-gray-50 transition">Reject</button>
                                                </form>
                                                <form action="{{ route('queue.updateStatus', $req->id) }}" method="POST" class="flex-1">
                                                    @csrf @method('PATCH')
                                                    <input type="hidden" name="status" value="cancelled">
                                                    <input type="hidden" name="remarks" value="{{ $req->remarks }}">
                                                    <button type="submit" class="w-full px-4 py-2 bg-red-600 rounded text-white text-xs font-bold uppercase hover:bg-red-700 shadow-md transition">Approve</button>
                                                </form>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        <div class="flex justify-between items-center mb-6 border-b pb-4" style="border-color: #F9F3E3;">
                            <h3 class="text-xl font-bold flex items-center" style="color: #6B4E31;">Queue Status</h3>
                            <span class="text-xs font-bold text-white px-4 py-1 rounded-full uppercase tracking-wider" style="background-color: #D4AF37;">
                                {{ $todaysQueue->whereIn('status', ['waiting', 'serving'])->count() }} Active
                            </span>
                        </div>
                        
                        @if($todaysQueue->whereIn('status', ['waiting', 'serving'])->isEmpty())
                            <div class="flex flex-col items-center justify-center h-64 border-2 border-dashed border-[#eaddc5] rounded-lg bg-[#F9F3E3]">
                                <p class="text-[#6B4E31] font-medium uppercase tracking-wide text-sm opacity-60">No active clients</p>
                            </div>
                        @else
                            <ul class="space-y-4">
                                @foreach($todaysQueue as $queue)
                                    @continue($queue->status == 'cancel_requested')
                                    
                                    <li :class="activeDropdown === {{ $queue->id }} ? '!z-50' : ''"
                                        class="p-5 rounded-lg border hover:shadow-md transition duration-150 relative group {{ $queue->status === 'serving' ? 'z-20 border-[#D4AF37] ring-1 ring-[#D4AF37] bg-white' : 'border-[#F3E5AB] bg-[#FFFCF5]' }}">
                                        
                                        <div class="flex flex-col sm:flex-row justify-between items-center gap-4">
                                            
                                            {{-- Info --}}
                                            <div class="flex items-center space-x-5 w-full sm:w-auto">
                                                <div class="bg-white p-2 border border-[#F3E5AB] rounded shadow-sm">
                                                    {!! QrCode::size(45)->generate(route('queue.show', $queue->qr_token)) !!}
                                                </div>
                                                <div>
                                                    <div class="flex items-center gap-3 mb-1">
                                                        <span class="text-2xl font-black leading-none" style="color: #6B4E31;">{{ $queue->queue_number }}</span>
                                                        @if($queue->status === 'serving')
                                                            <span class="px-2 py-1 rounded text-[10px] font-bold uppercase tracking-widest bg-green-100 text-green-800 border border-green-200 flex items-center gap-1">Serving</span>
                                                        @else
                                                            <span class="px-2 py-1 rounded text-[10px] font-bold uppercase tracking-widest bg-[#F9F3E3] text-[#6B4E31] border border-[#eaddc5] flex items-center gap-1">Waiting</span>
                                                        @endif
                                                    </div>
                                                    <span class="text-sm font-bold block text-gray-800">{{ $queue->customer_name }}</span>
                                                    
                                                    {{-- 🔄 DISPLAY MULTIPLE SERVICES AS TAGS --}}
                                                    <div class="text-xs text-gray-500 block uppercase tracking-wide mt-1">
                                                        @foreach($queue->services as $s)
                                                            <span class="inline-block bg-[#F9F3E3] border border-[#eaddc5] px-2 py-0.5 rounded text-[10px] mr-1 mb-1 shadow-sm">{{ $s->service_name }}</span>
                                                        @endforeach
                                                    </div>
                                                </div>
                                            </div>

                                            {{-- Buttons --}}
                                            <div class="flex items-center space-x-2">
                                                @if($queue->status === 'waiting')
                                                    {{-- CALL BUTTON --}}
                                                    <form action="{{ route('queue.updateStatus', $queue->id) }}" method="POST">
                                                        @csrf @method('PATCH')
                                                        <input type="hidden" name="status" value="serving">
                                                        <button type="button" 
                                                                @click="ask('Start Serving?', @js('Are you sure you want to call ' . $queue->customer_name . '?'), 'Call Client', 'primary', $el)"
                                                                class="text-white text-xs font-bold py-2 px-4 rounded shadow hover:opacity-90 transition uppercase tracking-wider flex items-center gap-2" style="background-color: #6B4E31;">
                                                            Call
                                                        </button>
                                                    </form>

                                                    {{-- NO SHOW BUTTON --}}
                                                    <form action="{{ route('queue.updateStatus', $queue->id) }}" method="POST">
                                                        @csrf @method('PATCH')
                                                        <input type="hidden" name="status" value="no_show">
                                                        <button type="button" 
                                                                @click="ask('Mark No Show?', @js('Remove ' . $queue->customer_name . '?'), 'Confirm', 'danger', $el)"
                                                                class="text-gray-400 hover:text-gray-600 p-2" title="Mark as No Show">
                                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" /></svg>
                                                        </button>
                                                    </form>
                                                @endif

                                                @if($queue->status === 'serving')
                                                    {{-- DONE BUTTON --}}
                                                    <form action="{{ route('queue.updateStatus', $queue->id) }}" method="POST">
                                                        @csrf @method('PATCH')
                                                        <input type="hidden" name="status" value="completed">
                                                        <button type="button" 
                                                                @click="ask('Complete Service?', @js('Mark ' . $queue->customer_name . ' as Done?'), 'Complete', 'warning', $el)"
                                                                class="text-white text-xs font-bold py-2 px-4 rounded shadow hover:opacity-90 transition uppercase tracking-wider flex items-center gap-2" style="background-color: #D4AF37;">
                                                            Done
                                                        </button>
                                                    </form>

                                                    {{-- DROPDOWN TOGGLE --}}
                                                    <div class="relative z-50">
                                                        <button @click="activeDropdown = (activeDropdown === {{ $queue->id }} ? null : {{ $queue->id }})" 
                                                                class="text-white text-xs font-bold py-2 px-3 rounded shadow hover:opacity-90 transition uppercase tracking-wider flex items-center gap-2" style="background-color: #4A5568;" title="Add another service">
                                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" style="width: 16px; height: 16px;" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>Add
                                                        </button>
                                                        
                                                        <div x-show="activeDropdown === {{ $queue->id }}" 
                                                             @click.away="activeDropdown = null" 
                                                             class="absolute right-0 mt-2 bg-white rounded-lg shadow-2xl z-[100] border border-gray-200 p-5 ring-1 ring-black ring-opacity-5" 
                                                             style="width: 300px; display: none;">
                                                            
                                                            <form action="{{ route('queue.addService', $queue->id) }}" method="POST">
                                                                @csrf
                                                                <div class="flex items-center mb-3">
                                                                    <label class="block text-xs uppercase font-bold text-gray-600 tracking-wide whitespace-nowrap">Next Services</label>
                                                                </div>
                                                                {{-- 🔄 MULTI-SELECT FOR FOLLOW-UP --}}
                                                                <select name="service_ids[]" multiple class="w-full text-sm border-gray-300 rounded-md mb-4 py-2 px-3 h-24" required>
                                                                    @foreach($services as $service)
                                                                        <option value="{{ $service->id }}">{{ $service->service_name }} (₱{{ number_format($service->price, 0) }})</option>
                                                                    @endforeach
                                                                </select>
                                                                <p class="text-[9px] text-gray-400 -mt-2 mb-3 leading-tight">* Hold Ctrl/Cmd for multiple</p>
                                                                <button type="submit" class="w-full text-white text-sm font-bold py-3 rounded-md uppercase tracking-wider bg-stone-700 hover:bg-stone-800 transition">Confirm Add</button>
                                                            </form>
                                                        </div>
                                                    </div>
                                                @endif

                                                <a href="{{ route('queue.print', $queue->id) }}" target="_blank" class="bg-white text-gray-600 hover:text-black border border-gray-300 font-bold py-2 px-3 rounded shadow-sm hover:shadow transition" title="Print Ticket"><svg class="w-4 h-4" style="width: 16px; height: 16px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg></a>
                                                
                                                {{-- Inline Cancel Logic --}}
                                                <div x-data="{ showModal: false }">
                                                    <button @click="showModal = true" class="text-red-300 hover:text-red-500 font-bold px-2 transition duration-150" title="Cancel Ticket"><svg class="w-5 h-5" style="width: 20px; height: 20px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg></button>
                                                    <div x-show="showModal" style="display: none;" class="relative z-50">
                                                        <div x-show="showModal" x-transition.opacity class="fixed inset-0 bg-[#6B4E31]/50 backdrop-blur-sm transition-opacity" @click="showModal = false"></div>
                                                        <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
                                                            <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
                                                                <div class="relative transform overflow-hidden rounded-lg bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-md p-6">
                                                                    <form action="{{ route('queue.updateStatus', $queue->id) }}" method="POST">
                                                                        @csrf @method('PATCH')
                                                                        <input type="hidden" name="status" value="cancelled">
                                                                        <div class="text-center mb-4">
                                                                            <h3 class="text-lg font-medium text-[#6B4E31]">Cancel Ticket?</h3>
                                                                            <input type="text" name="remarks" class="mt-4 w-full border-gray-300 rounded text-sm" placeholder="Reason (e.g. Changed Mind)" required>
                                                                        </div>
                                                                        <div class="flex gap-2">
                                                                            <button type="button" @click="showModal = false" class="w-full bg-white border border-gray-300 rounded-md py-2 text-gray-700 font-bold uppercase text-xs">Back</button>
                                                                            <button type="submit" class="w-full bg-red-600 rounded-md py-2 text-white font-bold uppercase text-xs">Confirm</button>
                                                                        </div>
                                                                    </form>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                            </div>
                                        </div>
                                    </li>
                                @endforeach
                            </ul>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        {{-- 🛑 1. SIDEBAR HISTORY --}}
        <div x-show="showHistory" 
             style="display: none; position: fixed; top: 0; right: 0; bottom: 0; left: 0; z-index: 9999;" 
             aria-modal="true">
            
            <div @click="showHistory = false" class="fixed inset-0 bg-[#4a3b2a]/60 backdrop-blur-sm transition-opacity"></div>

            <div class="absolute top-0 right-0 bottom-0 w-full max-w-md bg-[#F9F3E3] shadow-2xl flex flex-col h-full border-l border-[#D4AF37]"
                 x-show="showHistory"
                 x-transition:enter="transform transition ease-in-out duration-300"
                 x-transition:enter-start="translate-x-full"
                 x-transition:enter-end="translate-x-0"
                 x-transition:leave="transform transition ease-in-out duration-300"
                 x-transition:leave-start="translate-x-0"
                 x-transition:leave-end="translate-x-full">
                
                <div class="flex-none px-6 py-6 bg-[#6B4E31] text-white border-b border-[#D4AF37] flex justify-between items-center shadow-md z-10">
                    <div>
                        <h2 class="text-xl font-bold uppercase tracking-widest text-[#D4AF37]">History</h2>
                        <p class="text-xs text-[#eaddc5] mt-1">Review completed or cancelled tickets.</p>
                    </div>
                    <button @click="showHistory = false" class="text-[#D4AF37] hover:text-white p-2">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <div class="flex-1 overflow-y-auto bg-[#F9F3E3] p-4">
                    @if($completedQueue->isEmpty())
                        <div class="flex flex-col items-center justify-center h-full opacity-40">
                            <p class="text-[#6B4E31] font-bold uppercase tracking-widest text-xs">No history found</p>
                        </div>
                    @else
                        <div class="space-y-3 pb-6">
                            @foreach($completedQueue as $h)
                                <div class="bg-white p-4 rounded-lg border border-[#eaddc5] shadow-sm flex flex-col gap-2">
                                    <div class="flex justify-between items-center">
                                        <span class="font-black text-2xl text-[#6B4E31]">{{ $h->queue_number }}</span>
                                        @if($h->status == 'completed')
                                            <span class="text-[10px] uppercase px-2 py-1 rounded bg-green-100 text-green-800 font-bold tracking-wider">Completed</span>
                                        @elseif($h->status == 'cancelled')
                                            <span class="text-[10px] uppercase px-2 py-1 rounded bg-red-100 text-red-800 font-bold tracking-wider">Cancelled</span>
                                        @else
                                            <span class="text-[10px] uppercase px-2 py-1 rounded bg-gray-100 text-gray-600 font-bold tracking-wider">No Show</span>
                                        @endif
                                    </div>
                                    <div class="flex justify-between items-center border-t border-gray-100 pt-2">
                                        <div class="flex flex-col">
                                            <span class="text-sm font-bold text-gray-700">{{ $h->customer_name }}</span>
                                            
                                            {{-- 🔄 SHOW MULTIPLE TAGS IN HISTORY --}}
                                            <div class="mt-1">
                                                @foreach($h->services as $s)
                                                    <span class="inline-block bg-gray-100 text-gray-600 border border-gray-200 px-2 py-0.5 rounded text-[9px] uppercase tracking-wider mr-1 mb-1">{{ $s->service_name }}</span>
                                                @endforeach
                                            </div>
                                        </div>
                                        <form action="{{ route('queue.updateStatus', $h->id) }}" method="POST">
                                            @csrf @method('PATCH')
                                            <input type="hidden" name="status" value="waiting">
                                            <button type="button" 
                                                    @click="ask('Restore Ticket?', @js('Move ' . $h->customer_name . ' back to waiting?'), 'Restore', 'primary', $el)"
                                                    class="bg-blue-50 text-blue-600 hover:bg-blue-600 hover:text-white text-[10px] font-bold uppercase px-3 py-2 rounded transition border border-blue-200">
                                                Restore
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>

                <div class="flex-none p-4 bg-white border-t border-[#eaddc5] z-10">
                    <button @click="showHistory = false" class="w-full py-3 bg-[#6B4E31] text-white font-bold uppercase text-sm tracking-widest rounded-lg hover:opacity-90 transition flex justify-center items-center gap-2">
                        Close History Panel
                    </button>
                </div>
            </div>
        </div>

        {{-- 🛑 2. CONFIRMATION MODAL (Global) --}}
        <div x-show="confirm.open" 
             style="display: none; position: fixed; top: 0; left: 0; right: 0; bottom: 0; z-index: 10000;" 
             role="dialog" 
             aria-modal="true">
            
            <div x-show="confirm.open"
                 x-transition.opacity
                 @click="confirm.open = false"
                 class="absolute inset-0 bg-[#4a3b2a]/60 backdrop-blur-sm"></div>

            <div class="flex min-h-full items-center justify-center p-4">
                <div x-show="confirm.open"
                     x-transition:enter="ease-out duration-300"
                     x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                     x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                     x-transition:leave="ease-in duration-200"
                     x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                     x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                     class="relative transform overflow-hidden rounded-lg bg-[#F9F3E3] border-t-4 border-[#D4AF37] px-4 pb-4 pt-5 text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-lg sm:p-6 text-center">
                    
                    <div class="sm:flex sm:items-start flex-col items-center">
                        <div class="mx-auto flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-full bg-[#fff9ee] mb-4">
                            <svg class="h-6 w-6 text-[#D4AF37]" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" />
                            </svg>
                        </div>
                        <h3 class="text-lg font-bold leading-6 text-[#6B4E31]" x-text="confirm.title"></h3>
                        <div class="mt-2">
                            <p class="text-sm text-[#8c6b4a]" x-text="confirm.message"></p>
                        </div>
                    </div>
                    
                    <div class="mt-5 sm:mt-4 flex flex-row-reverse gap-2 justify-center">
                        
                        {{-- 1. Primary (Brown) --}}
                        <button type="button" 
                                x-show="confirm.type === 'primary'"
                                @click="confirm.open = false; if(confirm.action) confirm.action()"
                                class="inline-flex w-full justify-center rounded-md bg-[#6B4E31] px-3 py-2 text-sm font-bold text-white shadow-sm hover:opacity-90 sm:w-auto uppercase tracking-wider"
                                x-text="confirm.btnText">
                        </button>

                        {{-- 2. Danger (Red) --}}
                        <button type="button" 
                                x-show="confirm.type === 'danger'"
                                @click="confirm.open = false; if(confirm.action) confirm.action()"
                                class="inline-flex w-full justify-center rounded-md bg-red-600 px-3 py-2 text-sm font-bold text-white shadow-sm hover:bg-red-700 sm:w-auto uppercase tracking-wider"
                                x-text="confirm.btnText">
                        </button>

                        {{-- 3. Warning (Gold) --}}
                        <button type="button" 
                                x-show="confirm.type === 'warning'"
                                @click="confirm.open = false; if(confirm.action) confirm.action()"
                                class="inline-flex w-full justify-center rounded-md bg-[#D4AF37] px-3 py-2 text-sm font-bold text-white shadow-sm hover:opacity-90 sm:w-auto uppercase tracking-wider"
                                x-text="confirm.btnText">
                        </button>
                        
                        <button type="button" 
                                @click="confirm.open = false"
                                class="inline-flex w-full justify-center rounded-md bg-white px-3 py-2 text-sm font-bold text-[#6B4E31] shadow-sm ring-1 ring-inset ring-[#eaddc5] hover:bg-[#F9F3E3] sm:w-auto uppercase tracking-wider">
                            Cancel
                        </button>
                    </div>
                </div>
            </div>
        </div>

    </div> 
</x-app-layout>