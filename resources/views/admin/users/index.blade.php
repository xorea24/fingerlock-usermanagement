@extends('adminlte::page')

@section('title', 'Manage Users')

@section('css')
<script src="https://cdn.tailwindcss.com"></script>
<script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
@stop

@section('content')
<div class="p-4 bg-[#f8fafc] min-h-screen">
    <div class="flex justify-between items-center mb-8 px-4">
        <h1 class="text-2xl font-black text-slate-800 uppercase tracking-tight">User Manager</h1>
        <a href="{{ route('users.create') }}" class="flex items-center gap-2 px-6 py-3 bg-blue-600 text-white hover:bg-blue-700 rounded-xl transition text-xs font-bold uppercase tracking-widest shadow-md">
            <i class="fas fa-user-plus"></i> Add New User
        </a>
    </div>

    @if(session('success'))
        <div class="mb-6 px-6 py-4 bg-green-50 border border-green-200 text-green-700 rounded-2xl font-bold">
            {{ session('success') }}
        </div>
    @endif
    
    @if(session('error'))
        <div class="mb-6 px-6 py-4 bg-red-50 border border-red-200 text-red-700 rounded-2xl font-bold">
            {{ session('error') }}
        </div>
    @endif

    <div class="bg-white rounded-[2.5rem] shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-slate-600">
                <thead class="bg-gray-50/50 text-xs uppercase text-slate-500 font-black tracking-widest border-b border-gray-100">
                    <tr>
                        <th class="px-8 py-5">Name</th>
                        <th class="px-8 py-5">Email</th>
                        <th class="px-8 py-5">Role</th>
                        <th class="px-8 py-5">Biometric Status</th>
                        <th class="px-8 py-5 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50 font-medium">
                    @forelse($users as $user)
                        <tr class="hover:bg-gray-50/50 transition-colors">
                            <td class="px-8 py-5 text-slate-800 font-bold">{{ $user->name }}</td>
                            <td class="px-8 py-5">{{ $user->email }}</td>
                            <td class="px-8 py-5">
                                @if($user->is_admin)
                                    <span class="px-3 py-1 bg-blue-100 text-blue-700 rounded-lg text-xs font-black uppercase tracking-widest">Admin</span>
                                @else
                                    <span class="px-3 py-1 bg-gray-100 text-gray-600 rounded-lg text-xs font-black uppercase tracking-widest">Standard</span>
                                @endif
                            </td>
                            <td class="px-8 py-5">
                                @if($user->fingerprint_id)
                                    <div class="flex items-center gap-2">
                                        <div class="w-2 h-2 rounded-full bg-green-500 animate-pulse"></div>
                                        <span class="text-[10px] font-black text-green-600 uppercase tracking-widest">Enrolled</span>
                                    </div>
                                @else
                                    <div class="flex items-center gap-2">
                                        <div class="w-2 h-2 rounded-full bg-red-400"></div>
                                        <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Not Enrolled</span>
                                    </div>
                                @endif
                            </td>
                            <td class="px-8 py-5 flex justify-end gap-3">
                                <a href="{{ route('users.edit', $user) }}" class="p-2 text-blue-500 hover:bg-blue-50 rounded-xl transition-colors">
                                    <i class="fas fa-edit"></i>
                                </a>
                                @if($user->id !== auth()->id())
                                <form action="{{ route('users.destroy', $user) }}" method="POST" onsubmit="return confirm('Delete this user?');" class="inline">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="p-2 text-red-500 hover:bg-red-50 rounded-xl transition-colors">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-8 py-10 text-center text-slate-400 font-black uppercase tracking-widest text-sm">No users found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@stop
