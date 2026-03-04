@extends('adminlte::page')

@section('title', $title ?? 'Recycle Bin')

@section('css')
<style>
    [x-cloak] { display: none !important; }
    .custom-scrollbar::-webkit-scrollbar { width: 6px; }
    .custom-scrollbar::-webkit-scrollbar-track { background: #f1f1f1; border-radius: 10px; }
    .custom-scrollbar::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }
</style>
<script src="https://cdn.tailwindcss.com"></script> 
<script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
@stop

@section('content')
<div class="p-4" x-data="{ trashSearch: '' }">
    <div class="max-w-6xl mx-auto space-y-8">
        
        <div class="bg-blue-50/50 p-4 rounded-2xl border border-blue-100 flex items-center gap-4">
            <div class="bg-blue-600 p-2 rounded-lg">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
            <div>
                <p class="text-sm text-blue-900 font-bold">Recycle Bin Storage</p>
                <p class="text-xs text-blue-700">Deleted items are grouped by their original album. Restoring them will put them back into active rotation.</p>
            </div>
        </div>

        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div class="relative group">
                <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400 group-focus-within:text-blue-600 transition-colors">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </span>
                <input type="text" x-model="trashSearch" placeholder="Search deleted albums..." 
                    class="pl-10 pr-4 py-3 border border-gray-200 rounded-2xl text-sm focus:ring-4 focus:ring-blue-500/10 focus:border-blue-600 outline-none transition-all w-full md:w-96 shadow-sm bg-white font-medium">
            </div>
        </div>

        @forelse($trashedPhotosByAlbum as $albumId => $trashedSlides)
            @php 
                $album = \App\Models\Album::withTrashed()->find($albumId); 
                $albumName = $album ? $album->name : 'Deleted Album';
            @endphp

            <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden transition-all hover:shadow-md mb-6"
                 x-show="trashSearch === '' || '{{ strtolower($albumName) }}'.includes(trashSearch.toLowerCase())"
                 x-transition>
                
                <div class="px-6 py-4 bg-gray-50/50 border-b border-gray-100 flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-white border border-gray-200 flex items-center justify-center shadow-sm">
                            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-sm font-black text-gray-700 uppercase tracking-wide">{{ $albumName }}</h3>
                            <p class="text-[10px] text-gray-400 font-bold uppercase">{{ $trashedSlides->count() }} Archived Items</p>
                        </div>
                    </div>
            
                    <div class="flex items-center gap-2">
                        <form action="{{ route('photos.restore-album') }}" method="POST">
                            @csrf @method('PATCH')
                            <input type="hidden" name="album_id" value="{{ $albumId }}">
                            <button type="submit" class="text-[10px] bg-blue-600 text-white px-4 py-2 rounded-xl font-black uppercase hover:bg-blue-700 transition shadow-lg shadow-blue-100 flex items-center gap-2">
                                Restore Entire Group
                            </button>
                        </form>

                        <form action="{{ route('Photo.delete-album', $albumId) }}" method="POST"
                              onsubmit="return confirm('WARNING: Permanently delete all photos in this group?')">
                            @csrf @method('DELETE')
                            <button class="p-2 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-xl transition-all">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                </svg>
                            </button>
                        </form>
                    </div>
                </div>

                <div class="p-6 grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-4">
                    @foreach($trashedSlides as $trash)
                        <div class="relative bg-gray-50 rounded-2xl border border-gray-100 overflow-hidden group transition-all hover:border-blue-300">
                            <div class="relative aspect-square overflow-hidden bg-gray-200">
                                <img src="{{ Storage::url($trash->image_path) }}" 
                                     class="w-full h-full object-cover grayscale opacity-60 group-hover:grayscale-0 group-hover:opacity-100 transition-all duration-500">
                            </div>

                            <div class="absolute inset-x-0 bottom-0 p-2 flex gap-1 transform translate-y-full group-hover:translate-y-0 transition-transform duration-300 bg-white/90 backdrop-blur-sm">
                                <form action="{{ route('photos.restore', $trash->id) }}" method="POST" class="flex-1">
                                    @csrf @method('PATCH')
                                    <button class="w-full py-2 bg-blue-600 text-white text-[9px] font-black rounded-lg hover:bg-blue-700 transition">
                                        RESTORE
                                    </button>
                                </form>
                                
                                <form action="{{ route('photos.forceDelete', $trash->id) }}" method="POST" onsubmit="return confirm('Permanently delete this photo?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="p-2 bg-red-50 text-red-600 rounded-lg hover:bg-red-600 hover:text-white transition">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                    </button>
                                </form>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @empty
            <div class="text-center py-24 bg-white rounded-3xl border-2 border-dashed border-gray-100">
                <h3 class="text-gray-500 font-black uppercase tracking-widest text-sm">Recycle Bin is Empty</h3>
            </div>
        @endforelse
    </div>
</div>
@stop