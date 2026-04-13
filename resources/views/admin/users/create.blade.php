@extends('adminlte::page')

@section('title', 'Create User')

@section('css')
<script src="https://cdn.tailwindcss.com"></script>
@stop

@section('content')


    <div class="mb-8 px-4 flex items-center gap-4">
        <a href="{{ route('users.index') }}" class="p-2 bg-white text-gray-400 border border-gray-200 hover:bg-gray-50 hover:text-gray-600 rounded-xl transition shadow-sm">
            <i class="fas fa-arrow-left"></i>
        </a>
        <h1 class="text-2xl font-black text-slate-800 uppercase tracking-tight">Create User</h1>
    </div>

    <div class="max-w-3xl mx-auto bg-white rounded-[2.5rem] shadow-sm border border-gray-100 p-8 md:p-12">
        <form action="{{ route('users.store') }}" method="POST" class="space-y-6">
            @csrf
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-xs font-black text-slate-400 uppercase mb-2 ml-1">Full Name</label>
                    <input type="text" name="name" value="{{ old('name') }}" required
                        class="w-full px-5 py-4 bg-gray-50 border border-gray-100 rounded-2xl outline-none font-bold text-slate-700 focus:ring-2 focus:ring-blue-500/20">
                    @error('name') <p class="text-red-500 text-xs mt-1 font-medium ml-1">{{ $message }}</p> @enderror
                </div>
                
                <div>
                    <label class="block text-xs font-black text-slate-400 uppercase mb-2 ml-1">Email Address</label>
                    <input type="email" name="email" value="{{ old('email') }}" required
                        class="w-full px-5 py-4 bg-gray-50 border border-gray-100 rounded-2xl outline-none font-bold text-slate-700 focus:ring-2 focus:ring-blue-500/20">
                    @error('email') <p class="text-red-500 text-xs mt-1 font-medium ml-1">{{ $message }}</p> @enderror
                </div>
                
                <div>
                    <label class="block text-xs font-black text-slate-400 uppercase mb-2 ml-1">Password</label>
                    <input type="password" name="password" required
                        class="w-full px-5 py-4 bg-gray-50 border border-gray-100 rounded-2xl outline-none font-bold text-slate-700 focus:ring-2 focus:ring-blue-500/20">
                    @error('password') <p class="text-red-500 text-xs mt-1 font-medium ml-1">{{ $message }}</p> @enderror
                </div>
                
                <div>
                    <label class="block text-xs font-black text-slate-400 uppercase mb-2 ml-1">Confirm Password</label>
                    <input type="password" name="password_confirmation" required
                        class="w-full px-5 py-4 bg-gray-50 border border-gray-100 rounded-2xl outline-none font-bold text-slate-700 focus:ring-2 focus:ring-blue-500/20">
                </div>

                <div>
                    <label class="block text-xs font-black text-slate-400 uppercase mb-2 ml-1">Fingerprint ID</label>
                    <input type="text" name="fingerprint_id" value="{{ old('fingerprint_id') }}"
                        placeholder="Enrollment ID"
                        class="w-full px-5 py-4 bg-gray-50 border border-gray-100 rounded-2xl outline-none font-bold text-slate-700 focus:ring-2 focus:ring-blue-500/20">
                    @error('fingerprint_id') <p class="text-red-500 text-xs mt-1 font-medium ml-1">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="pt-4 border-t border-gray-50 mt-8">
                <label class="flex items-center gap-3 cursor-pointer">
                    <input type="checkbox" name="is_admin" value="1" class="w-5 h-5 text-blue-600 border-gray-300 rounded focus:ring-blue-500" {{ old('is_admin') ? 'checked' : '' }}>
                    <span class="text-sm font-black text-slate-700 uppercase tracking-widest">Grant Admin Privileges</span>
                </label>
            </div>

            <div class="mt-10 flex gap-4">
                <a href="{{ route('users.index') }}" class="w-1/3 py-4 text-center text-sm font-black text-slate-400 uppercase tracking-widest hover:bg-gray-50 rounded-2xl transition">Cancel</a>
                <button type="submit" class="w-2/3 py-4 bg-slate-900 text-white text-sm font-black uppercase tracking-widest rounded-2xl shadow-lg hover:bg-black active:scale-95 transition-all">Create User</button>
            </div>
        </form>
    </div>
</div>
@stop
