<x-app-layout>
    <x-slot name="header">
        <h2 class="font-serif font-bold text-2xl text-[#6B4E31] uppercase tracking-widest">
            Treatment Menu
        </h2>
    </x-slot>

    <div class="py-12 min-h-screen" style="background-color: #F9F3E3;">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-10">
                
                {{-- 🖋️ LEFT SIDE: "CREATE NEW" FORM --}}
                <div class="lg:col-span-1">
                    <div class="bg-white p-8 rounded-2xl shadow-xl border-t-8 border-[#6B4E31] relative overflow-hidden sticky top-6">
                        
                        {{-- Decorative Background Icon --}}
                        <div class="absolute -right-6 -bottom-6 text-[#F9F3E3] opacity-50">
                            <svg class="w-40 h-40" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>
                        </div>

                        <div class="relative z-10">
                            <h3 class="font-serif font-bold text-2xl text-[#6B4E31] mb-1">Curate New Service</h3>
                            <p class="text-xs text-[#D4AF37] uppercase tracking-widest mb-8">Add to your offerings</p>

                            <form action="{{ route('services.store') }}" method="POST">
                                @csrf
                                
                                {{-- Service Name --}}
                                <div class="mb-6">
                                    <label class="block text-xs font-bold uppercase text-gray-500 mb-2 tracking-wider">Service Name</label>
                                    <input type="text" name="service_name" 
                                           class="w-full border-0 border-b-2 border-gray-200 bg-transparent focus:border-[#D4AF37] focus:ring-0 transition px-0 py-2 text-[#6B4E31] placeholder-gray-300 font-serif text-lg" 
                                           placeholder="e.g. Golden Glow Facial" required>
                                </div>

                                {{-- Price --}}
                                <div class="mb-6">
                                    <label class="block text-xs font-bold uppercase text-gray-500 mb-2 tracking-wider">Price (₱)</label>
                                    <input type="number" name="price" 
                                           class="w-full border-0 border-b-2 border-gray-200 bg-transparent focus:border-[#D4AF37] focus:ring-0 transition px-0 py-2 text-[#6B4E31] placeholder-gray-300 font-serif text-lg" 
                                           placeholder="2500" required>
                                </div>

                                {{-- Description --}}
                                <div class="mb-8">
                                    <label class="block text-xs font-bold uppercase text-gray-500 mb-2 tracking-wider">Short Description (Optional)</label>
                                    <textarea name="description" rows="3" 
                                              class="w-full border-gray-200 bg-[#F9F3E3]/30 rounded-lg focus:border-[#D4AF37] focus:ring-[#D4AF37] text-sm text-gray-600 placeholder-gray-300" 
                                              placeholder="A brief description for the receipt..."></textarea>
                                </div>

                                {{-- Submit Button --}}
                                <button type="submit" class="w-full bg-[#6B4E31] text-white font-bold py-4 rounded-lg shadow-lg hover:bg-[#5a422a] hover:shadow-xl transition duration-300 uppercase tracking-widest text-xs flex justify-center items-center gap-2 group">
                                    <span>Add to Menu</span>
                                    <svg class="w-4 h-4 transform group-hover:translate-x-1 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>

                {{-- 📜 RIGHT SIDE: MENU LIST --}}
                <div class="lg:col-span-2">
                    <div class="bg-white rounded-2xl shadow-xl overflow-hidden border border-[#eaddc5]">
                        
                        {{-- Header --}}
                        <div class="p-6 border-b border-[#F9F3E3] bg-[#fffbf2] flex justify-between items-center">
                            <div>
                                <h3 class="font-serif font-bold text-xl text-[#6B4E31]">Current Offerings</h3>
                                <p class="text-xs text-gray-400 uppercase tracking-widest mt-1">Manage your prices & availability</p>
                            </div>
                            <div class="bg-[#F9F3E3] text-[#6B4E31] px-4 py-2 rounded-full text-xs font-bold uppercase tracking-wider">
                                {{ $services->count() }} Items
                            </div>
                        </div>

                        {{-- Table --}}
                        <div class="overflow-x-auto">
                            <table class="w-full text-left">
                                <thead class="bg-[#F9F3E3] text-[#6B4E31] text-xs uppercase tracking-widest">
                                    <tr>
                                        <th class="p-6">Treatment</th>
                                        <th class="p-6">Description</th>
                                        <th class="p-6 text-right">Price</th>
                                        <th class="p-6 text-right">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-[#F9F3E3]">
                                    @foreach($services as $service)
                                    <tr class="group hover:bg-[#fffbf2] transition duration-200">
                                        
                                        {{-- Name --}}
                                        <td class="p-6">
                                            <div class="font-serif font-bold text-lg text-[#6B4E31]">{{ $service->service_name }}</div>
                                            <div class="text-[10px] text-[#D4AF37] uppercase tracking-wider font-bold mt-1">
                                                {{ $service->is_active ? 'Available' : 'Unavailable' }}
                                            </div>
                                        </td>

                                        {{-- Description --}}
                                        <td class="p-6 text-sm text-gray-500 max-w-xs truncate">
                                            {{ $service->description ?? 'No description provided.' }}
                                        </td>

                                        {{-- Price --}}
                                        <td class="p-6 text-right">
                                            <span class="font-serif text-xl font-bold text-[#6B4E31]">₱{{ number_format($service->price, 0) }}</span>
                                        </td>

                                        {{-- Actions --}}
                                        <td class="p-6 text-right">
                                            <div class="flex justify-end items-center gap-3 opacity-60 group-hover:opacity-100 transition">
                                                <a href="{{ route('services.edit', $service->id) }}" class="text-[#D4AF37] hover:text-[#6B4E31] transition p-2 bg-white rounded-full shadow-sm hover:shadow-md border border-gray-100" title="Edit">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                                                </a>
                                                
                                                <form action="{{ route('services.destroy', $service->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this service?');">
                                                    @csrf @method('DELETE')
                                                    <button class="text-red-300 hover:text-red-500 transition p-2 bg-white rounded-full shadow-sm hover:shadow-md border border-gray-100" title="Delete">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        {{-- Empty State --}}
                        @if($services->isEmpty())
                            <div class="p-10 text-center text-gray-400">
                                <p class="text-sm uppercase tracking-widest italic">The menu is currently empty.</p>
                            </div>
                        @endif
                    </div>
                </div>

            </div>
        </div>
    </div>
</x-app-layout>