<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-serif font-bold text-2xl text-[#6B4E31] uppercase tracking-widest">
                Edit Treatment
            </h2>
            <a href="{{ route('services.index') }}" class="text-xs font-bold text-gray-400 hover:text-[#6B4E31] uppercase tracking-wider flex items-center gap-1 transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                Back to Menu
            </a>
        </div>
    </x-slot>

    <div class="py-12 min-h-screen" style="background-color: #F9F3E3;">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            
            <div class="bg-white p-10 rounded-2xl shadow-2xl border-t-8 border-[#D4AF37] relative overflow-hidden">
                
                {{-- Watermark --}}
                <div class="absolute -right-10 -top-10 text-[#F9F3E3] opacity-40">
                    <svg class="w-64 h-64" fill="currentColor" viewBox="0 0 24 24"><path d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                </div>

                <div class="relative z-10">
                    <h3 class="font-serif font-bold text-3xl text-[#6B4E31] mb-2">{{ $service->service_name }}</h3>
                    <p class="text-xs text-[#D4AF37] uppercase tracking-widest mb-8">Modify Details</p>

                    <form action="{{ route('services.update', $service->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        {{-- Service Name --}}
                        <div class="mb-8">
                            <label class="block text-xs font-bold uppercase text-gray-400 mb-2 tracking-wider">Service Name</label>
                            <input type="text" name="service_name" value="{{ old('service_name', $service->service_name) }}"
                                   class="w-full border-0 border-b-2 border-gray-200 bg-transparent focus:border-[#6B4E31] focus:ring-0 transition px-0 py-2 text-[#6B4E31] font-bold text-xl placeholder-gray-300" 
                                   required>
                        </div>

                        {{-- Price --}}
                        <div class="mb-8">
                            <label class="block text-xs font-bold uppercase text-gray-400 mb-2 tracking-wider">Price (₱)</label>
                            <input type="number" name="price" value="{{ old('price', $service->price) }}"
                                   class="w-full border-0 border-b-2 border-gray-200 bg-transparent focus:border-[#6B4E31] focus:ring-0 transition px-0 py-2 text-[#6B4E31] font-serif font-bold text-2xl placeholder-gray-300" 
                                   required>
                        </div>

                        {{-- Description --}}
                        <div class="mb-10">
                            <label class="block text-xs font-bold uppercase text-gray-400 mb-2 tracking-wider">Description</label>
                            <textarea name="description" rows="4" 
                                      class="w-full border-gray-200 bg-[#F9F3E3]/30 rounded-lg focus:border-[#D4AF37] focus:ring-[#D4AF37] text-sm text-gray-600 leading-relaxed"
                                      placeholder="Describe the treatment...">{{ old('description', $service->description) }}</textarea>
                        </div>

                        {{-- Action Buttons --}}
                        <div class="flex items-center gap-4">
                            <button type="submit" class="flex-1 bg-[#6B4E31] text-white font-bold py-4 rounded-lg shadow-lg hover:bg-[#5a422a] hover:shadow-xl transition duration-300 uppercase tracking-widest text-xs flex justify-center items-center gap-2 group">
                                <span>Save Changes</span>
                                <svg class="w-4 h-4 transform group-hover:translate-x-1 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            </button>
                            
                            <a href="{{ route('services.index') }}" class="px-6 py-4 rounded-lg border border-gray-200 text-gray-500 font-bold uppercase text-xs tracking-widest hover:bg-gray-50 transition">
                                Cancel
                            </a>
                        </div>
                    </form>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>