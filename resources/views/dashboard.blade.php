<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Receptionist Console') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                
                {{-- LEFT SIDE: Add Client Form --}}
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 h-fit">
                    <h3 class="text-lg font-bold mb-4">📝 Register New Client</h3>
                    
                    @if(session('success'))
                        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4">
                            {{ session('success') }}
                        </div>
                    @endif

                    <form action="{{ route('queue.store') }}" method="POST">
                        @csrf
                        
                        <div class="mb-4">
                            <label class="block text-gray-700 font-bold mb-2">Select Service:</label>
                            <select name="service_id" class="w-full border rounded px-3 py-2" required>
                                <option value="">-- Choose Treatment --</option>
                                @foreach($services as $service)
                                    <option value="{{ $service->id }}">
                                        {{ $service->service_name }} (₱{{ number_format($service->price, 0) }})
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-4">
                            <label class="block text-gray-700 font-bold mb-2">Customer Name (Optional):</label>
                            <input type="text" name="customer_name" class="w-full border rounded px-3 py-2" placeholder="e.g. Ma'am Jane">
                        </div>

                        <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 rounded text-lg">
                            + Add to Queue
                        </button>
                    </form>
                </div>

                {{-- RIGHT SIDE: Current Queue List --}}
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <h3 class="text-lg font-bold mb-4">📋 Today's Queue</h3>
                    
                    @if($todaysQueue->isEmpty())
                        <p class="text-gray-500 italic">No clients in line yet.</p>
                    @else
                        <ul class="divide-y divide-gray-200">
                            @foreach($todaysQueue as $queue)
                                <li class="py-4 flex justify-between items-center">
                                    {{-- FLEX CONTAINER: Groups QR + Details together --}}
                                    <div class="flex items-center space-x-4">
                                        
                                        {{-- 📱 QR CODE DISPLAY --}}
                                        <div class="bg-white p-1 border rounded shadow-sm">
                                            {{-- This generates a QR linking to the client's status page --}}
                                            {!! QrCode::size(60)->generate(route('queue.show', $queue->qr_token)) !!}
                                        </div>

                                        {{-- Text Details --}}
                                        <div>
                                            <span class="text-2xl font-bold text-blue-600 block">{{ $queue->queue_number }}</span>
                                            <span class="text-sm text-gray-600">{{ $queue->customer_name }}</span>
                                            <span class="text-xs text-gray-400 block">{{ $queue->service->service_name }}</span>
                                        </div>
                                    </div>

                                    {{-- Status Badge --}}
                                    <span class="px-3 py-1 rounded-full text-sm font-semibold 
                                        {{ $queue->status === 'serving' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                        {{ ucfirst($queue->status) }}
                                    </span>
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </div>

            </div>
        </div>
    </div>
</x-app-layout>