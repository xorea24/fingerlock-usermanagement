@extends('adminlte::page')

@section('title', 'Create User')

@section('css')
<script src="https://cdn.tailwindcss.com"></script>
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
<style>
    body { font-family: 'Inter', sans-serif; }
    .glass-panel {
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(10px);
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05), inset 0 1px 0 rgba(255, 255, 255, 0.8);
    }
    .input-field { transition: all 0.3s ease; }
    .input-field:focus-within { transform: translateY(-2px); }
    .fade-in { animation: fadeIn 0.5s ease-out forwards; }
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }
</style>
@stop

@section('content')
<div class="py-8 min-h-screen bg-gradient-to-br from-gray-50 to-gray-200 flex justify-center items-start">
    <div class="w-full max-w-4xl px-4 fade-in">

        <!-- Header Section -->
        <div class="flex items-center justify-between mb-8">
            <div class="flex items-center gap-4">
                <a href="{{ route('users.index') }}" class="flex items-center justify-center w-12 h-12 bg-white text-gray-400 hover:text-gray-900 hover:bg-gray-100 rounded-full transition-all duration-300 shadow-sm border border-gray-200">
                    <i class="fas fa-arrow-left text-lg"></i>
                </a>
                <div>
                    <h1 class="text-3xl font-extrabold text-gray-900 tracking-tight">Create User</h1>
                    <p class="text-sm text-gray-500 mt-1 font-medium">Add a new user and enroll their fingerprint.</p>
                </div>
            </div>
        </div>

        <!-- Form Section -->
        <div class="glass-panel rounded-[2rem] border border-gray-200 overflow-hidden relative">
            <div class="absolute top-0 left-0 w-full h-1 bg-gradient-to-r from-gray-400 via-gray-500 to-gray-700"></div>

            <div class="p-8 md:p-12">
                <form action="{{ route('users.store') }}" method="POST" class="space-y-8">
                    @csrf

                    <!-- Basic Information -->
                    <div>
                        <h3 class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-6 flex items-center gap-2">
                            <i class="fas fa-user-circle"></i> Basic Information
                        </h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="input-field group">
                                <label class="block text-sm font-semibold text-gray-700 mb-2 ml-1">Full Name</label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                        <i class="fas fa-user text-gray-400 group-focus-within:text-gray-900 transition-colors"></i>
                                    </div>
                                    <input type="text" name="name" value="{{ old('name') }}" required
                                        class="w-full pl-11 pr-5 py-3.5 bg-gray-50 border border-gray-200 rounded-xl outline-none font-medium text-gray-800 focus:bg-white focus:border-gray-500 focus:ring-4 focus:ring-gray-100 transition-all">
                                </div>
                                @error('name') <p class="text-red-500 text-xs mt-2 font-medium flex items-center gap-1"><i class="fas fa-exclamation-circle"></i> {{ $message }}</p> @enderror
                            </div>

                            <div class="input-field group">
                                <label class="block text-sm font-semibold text-gray-700 mb-2 ml-1">Email Address</label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                        <i class="fas fa-envelope text-gray-400 group-focus-within:text-gray-900 transition-colors"></i>
                                    </div>
                                    <input type="email" name="email" value="{{ old('email') }}" required
                                        class="w-full pl-11 pr-5 py-3.5 bg-gray-50 border border-gray-200 rounded-xl outline-none font-medium text-gray-800 focus:bg-white focus:border-gray-500 focus:ring-4 focus:ring-gray-100 transition-all">
                                </div>
                                @error('email') <p class="text-red-500 text-xs mt-2 font-medium flex items-center gap-1"><i class="fas fa-exclamation-circle"></i> {{ $message }}</p> @enderror
                            </div>
                        </div>
                    </div>

                    <hr class="border-gray-100">

                    <!-- Security & Authentication -->
                    <div>
                        <h3 class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-6 flex items-center gap-2">
                            <i class="fas fa-shield-alt"></i> Security & Authentication
                        </h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="input-field group">
                                <label class="block text-sm font-semibold text-gray-700 mb-2 ml-1">Password</label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                        <i class="fas fa-lock text-gray-400 group-focus-within:text-gray-900 transition-colors"></i>
                                    </div>
                                    <input type="password" name="password" placeholder="••••••••" required
                                        class="w-full pl-11 pr-5 py-3.5 bg-gray-50 border border-gray-200 rounded-xl outline-none font-medium text-gray-800 focus:bg-white focus:border-gray-500 focus:ring-4 focus:ring-gray-100 transition-all">
                                </div>
                                @error('password') <p class="text-red-500 text-xs mt-2 font-medium flex items-center gap-1"><i class="fas fa-exclamation-circle"></i> {{ $message }}</p> @enderror
                            </div>

                            <div class="input-field group">
                                <label class="block text-sm font-semibold text-gray-700 mb-2 ml-1">Confirm Password</label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                        <i class="fas fa-check-circle text-gray-400 group-focus-within:text-gray-900 transition-colors"></i>
                                    </div>
                                    <input type="password" name="password_confirmation" placeholder="••••••••" required
                                        class="w-full pl-11 pr-5 py-3.5 bg-gray-50 border border-gray-200 rounded-xl outline-none font-medium text-gray-800 focus:bg-white focus:border-gray-500 focus:ring-4 focus:ring-gray-100 transition-all">
                                </div>
                            </div>

                            <div class="input-field group md:col-span-2 lg:col-span-1">
                                <label class="block text-sm font-semibold text-gray-700 mb-2 ml-1">Fingerprint ID</label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                        <i class="fas fa-fingerprint text-gray-400 group-focus-within:text-gray-900 transition-colors"></i>
                                    </div>
                                    <input type="text" name="fingerprint_id" value="{{ old('fingerprint_id') }}"
                                        placeholder="Device Enrollment ID"
                                        class="w-full pl-11 pr-5 py-3.5 bg-gray-50 border border-gray-200 rounded-xl outline-none font-medium text-gray-800 focus:bg-white focus:border-gray-500 focus:ring-4 focus:ring-gray-100 transition-all">
                                </div>
                                <p class="text-xs text-gray-400 mt-2 ml-1"><i class="fas fa-info-circle"></i> Used for biometric authentication login.</p>
                                @error('fingerprint_id') <p class="text-red-500 text-xs mt-2 font-medium flex items-center gap-1"><i class="fas fa-exclamation-circle"></i> {{ $message }}</p> @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Role Configuration -->
                    <div class="pt-6 border-t border-gray-100">
                        <div class="bg-gray-50/50 rounded-2xl border border-gray-200 p-5 flex items-start gap-4">
                            <div class="mt-1">
                                <label class="relative inline-flex items-center cursor-pointer">
                                    <input type="checkbox" name="is_admin" value="1" class="sr-only peer"
                                        {{ old('is_admin') ? 'checked' : '' }}>
                                    <div class="w-11 h-6 bg-gray-300 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-gray-200 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-gray-800"></div>
                                </label>
                            </div>
                            <div>
                                <h4 class="text-sm font-bold text-gray-800">Administrator Privileges</h4>
                                <p class="text-xs text-gray-500 mt-1 leading-relaxed">Grant this user full access to the admin panel, including user management and system settings.</p>
                            </div>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="pt-6 flex flex-col-reverse sm:flex-row gap-4 items-center justify-end">
                        <a href="{{ route('users.index') }}" class="w-full sm:w-auto px-6 py-3.5 text-center text-sm font-semibold text-gray-500 hover:text-gray-900 hover:bg-gray-100 rounded-xl transition-colors">
                            Cancel
                        </a>
                        <button type="submit" class="w-full sm:w-auto px-8 py-3.5 bg-gray-900 text-white text-sm font-semibold rounded-xl shadow-[0_4px_14px_0_rgba(0,0,0,0.15)] hover:shadow-[0_6px_20px_rgba(0,0,0,0.2)] hover:bg-black active:scale-95 transition-all">
                            Create User
                        </button>
                    </div>
                </form>
            </div>
        </div>

    </div>
</div>
@stop