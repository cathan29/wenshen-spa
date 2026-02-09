<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl leading-tight uppercase tracking-widest" style="color: #6B4E31;">
            {{ __('Receptionist Console') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                
                {{-- LEFT SIDE: Add Client Form --}}
                <div class="bg-white shadow-xl sm:rounded-lg p-8 border-t-8 h-fit" style="border-color: #D4AF37;">
                    <h3 class="text-xl font-bold mb-8 flex items-center border-b pb-4" style="color: #6B4E31; border-color: #F9F3E3;">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mr-3" style="width: 24px; height: 24px;" fill="none" viewBox="0 0 24 24" stroke="currentColor" style="color: #D4AF37;">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                        </svg>
                        New Client Registration
                    </h3>
                    
                    <form action="{{ route('queue.store') }}" method="POST">
                        @csrf
                        
                        <div class="mb-6">
                            <label class="block font-bold mb-2 text-xs uppercase tracking-widest" style="color: #6B4E31;">Select Service</label>
                            <div class="relative">
                                <select name="service_id" class="block w-full bg-stone-50 border border-stone-200 text-gray-700 py-3 px-4 pr-8 rounded leading-tight focus:outline-none focus:bg-white focus:border-yellow-500" required>
                                    <option value="">-- Choose Treatment --</option>
                                    @foreach($services as $service)
                                        <option value="{{ $service->id }}">
                                            {{ $service->service_name }} (₱{{ number_format($service->price, 0) }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="mb-8">
                            <label class="block font-bold mb-2 text-xs uppercase tracking-widest" style="color: #6B4E31;">Customer Name</label>
                            <input type="text" name="customer_name" class="appearance-none block w-full bg-stone-50 text-gray-700 border border-stone-200 rounded py-3 px-4 leading-tight focus:outline-none focus:bg-white focus:border-yellow-500" placeholder="e.g. Ma'am Jane">
                        </div>

                        <button type="submit" class="w-full text-white font-bold py-4 rounded shadow-lg hover:shadow-xl transition duration-200 uppercase tracking-widest text-sm flex justify-center items-center gap-2" style="background-color: #6B4E31;">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" style="width: 20px; height: 20px;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                            </svg>
                            Add to Queue
                        </button>
                    </form>
                </div>

                {{-- RIGHT SIDE: Current Queue List --}}
                <div class="bg-white shadow-xl sm:rounded-lg p-8 border-t-8 min-h-[600px] relative" style="border-color: #D4AF37;">
                    <div class="flex justify-between items-center mb-6 border-b pb-4" style="border-color: #F9F3E3;">
                        <h3 class="text-xl font-bold flex items-center" style="color: #6B4E31;">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mr-3" style="width: 24px; height: 24px;" fill="none" viewBox="0 0 24 24" stroke="currentColor" style="color: #D4AF37;">
                              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                            </svg>
                            Queue Status
                        </h3>
                        <span class="text-xs font-bold text-white px-4 py-1 rounded-full uppercase tracking-wider" style="background-color: #D4AF37;">
                            {{ $todaysQueue->count() }} Waiting
                        </span>
                    </div>
                    
                    @if($todaysQueue->isEmpty())
                        <div class="flex flex-col items-center justify-center h-64 border-2 border-dashed border-stone-200 rounded-lg bg-stone-50">
                            <svg xmlns="http://www.w3.org/2000/svg" class="text-stone-300 mb-3" style="width: 64px; height: 64px;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" />
                            </svg>
                            <p class="text-stone-400 font-medium uppercase tracking-wide text-sm">No clients in line</p>
                        </div>
                    @else
                        <ul class="space-y-4">
                            @foreach($todaysQueue as $queue)
                                <li class="p-5 rounded-lg border hover:shadow-md transition duration-150 relative group {{ $queue->status === 'serving' ? 'z-20' : 'z-10' }}" style="background-color: #FFFCF5; border-color: #F3E5AB;">
                                    <div class="flex flex-col sm:flex-row justify-between items-center gap-4">
                                        
                                        {{-- INFO --}}
                                        <div class="flex items-center space-x-5 w-full sm:w-auto">
                                            <div class="bg-white p-2 border rounded shadow-sm">
                                                {!! QrCode::size(45)->generate(route('queue.show', $queue->qr_token)) !!}
                                            </div>
                                            <div>
                                                <div class="flex items-center gap-3 mb-1">
                                                    <span class="text-2xl font-black leading-none" style="color: #6B4E31;">{{ $queue->queue_number }}</span>
                                                    @if($queue->status === 'serving')
                                                        <span class="px-2 py-1 rounded text-[10px] font-bold uppercase tracking-widest bg-green-100 text-green-800 border border-green-200 flex items-center gap-1">
                                                            Serving
                                                        </span>
                                                    @else
                                                        <span class="px-2 py-1 rounded text-[10px] font-bold uppercase tracking-widest bg-yellow-100 text-yellow-800 border border-yellow-200 flex items-center gap-1">
                                                            Waiting
                                                        </span>
                                                    @endif
                                                </div>
                                                <span class="text-sm font-bold block text-gray-800">{{ $queue->customer_name }}</span>
                                                <span class="text-xs text-gray-500 block uppercase tracking-wide">{{ $queue->service->service_name }}</span>
                                            </div>
                                        </div>

                                        {{-- ACTIONS --}}
                                        <div class="flex items-center space-x-2">
                                            
                                            {{-- CALL BUTTON --}}
                                            @if($queue->status === 'waiting')
                                                <form action="{{ route('queue.updateStatus', $queue->id) }}" method="POST">
                                                    @csrf @method('PATCH')
                                                    <input type="hidden" name="status" value="serving">
                                                    <button type="submit" class="text-white text-xs font-bold py-2 px-4 rounded shadow hover:bg-opacity-90 transition uppercase tracking-wider flex items-center gap-2" style="background-color: #6B4E31;">
                                                        <svg class="w-4 h-4" style="width: 16px; height: 16px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6m-5.832-6.268a6 6 0 01-2.2 10.28"></path></svg>
                                                        Call
                                                    </button>
                                                </form>
                                            @endif

                                            {{-- SERVING ACTIONS --}}
                                            @if($queue->status === 'serving')
                                                <form action="{{ route('queue.updateStatus', $queue->id) }}" method="POST">
                                                    @csrf @method('PATCH')
                                                    <input type="hidden" name="status" value="completed">
                                                    <button type="submit" class="text-white text-xs font-bold py-2 px-4 rounded shadow hover:bg-opacity-90 transition uppercase tracking-wider flex items-center gap-2" style="background-color: #D4AF37;">
                                                        <svg class="w-4 h-4" style="width: 16px; height: 16px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                                        Done
                                                    </button>
                                                </form>

                                                {{-- ⚠️ ADD SERVICE DROPDOWN (Forced Size Fix) --}}
                                                <div x-data="{ open: false }" class="relative z-50">
                                                    <button @click="open = !open" class="text-white text-xs font-bold py-2 px-3 rounded shadow hover:bg-opacity-90 transition uppercase tracking-wider flex items-center gap-2" style="background-color: #4A5568;" title="Add another service">
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" style="width: 16px; height: 16px;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                                        </svg>
                                                        Add
                                                    </button>
                                                    
                                                    {{-- Dropdown Container: Fixed Width 300px --}}
                                                    <div x-show="open" 
                                                         @click.away="open = false" 
                                                         x-transition:enter="transition ease-out duration-100"
                                                         x-transition:enter-start="opacity-0 scale-95"
                                                         x-transition:enter-end="opacity-100 scale-100"
                                                         class="absolute right-0 mt-2 bg-white rounded-lg shadow-2xl z-[100] border border-gray-200 p-5 ring-1 ring-black ring-opacity-5" 
                                                         style="width: 300px; display: none;">
                                                        
                                                        <form action="{{ route('queue.addService', $queue->id) }}" method="POST">
                                                            @csrf
                                                            <div class="flex items-center mb-3">
                                                                <svg class="w-4 h-4 text-gray-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path></svg>
                                                                <label class="block text-xs uppercase font-bold text-gray-600 tracking-wide whitespace-nowrap">Next Service</label>
                                                            </div>
                                                            
                                                            <select name="service_id" class="w-full text-sm border-gray-300 rounded-md mb-4 py-2 px-3 focus:ring-amber-500 focus:border-amber-500" required>
                                                                @foreach($services as $service)
                                                                    <option value="{{ $service->id }}">
                                                                        {{ $service->service_name }} (₱{{ number_format($service->price, 0) }})
                                                                    </option>
                                                                @endforeach
                                                            </select>
                                                            
                                                            <button type="submit" class="w-full text-white text-sm font-bold py-3 rounded-md uppercase tracking-wider shadow hover:bg-opacity-90 transition whitespace-nowrap" style="background-color: #6B4E31;">
                                                                Confirm Add
                                                            </button>
                                                        </form>
                                                    </div>
                                                </div>
                                            @endif

                                            {{-- PRINT BUTTON --}}
                                            <a href="{{ route('queue.print', $queue->id) }}" target="_blank" class="bg-white text-gray-600 hover:text-black border border-gray-300 font-bold py-2 px-3 rounded shadow-sm hover:shadow transition" title="Print Ticket">
                                                <svg class="w-4 h-4" style="width: 16px; height: 16px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                                            </a>

                                            {{-- CANCEL BUTTON --}}
                                            <form action="{{ route('queue.updateStatus', $queue->id) }}" method="POST" onsubmit="return confirm('Cancel this ticket?');">
                                                @csrf @method('PATCH')
                                                <input type="hidden" name="status" value="cancelled">
                                                <button type="submit" class="text-red-300 hover:text-red-500 font-bold px-2 transition duration-150" title="Cancel">
                                                    <svg class="w-5 h-5" style="width: 20px; height: 20px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                                </button>
                                            </form>

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
</x-app-layout>