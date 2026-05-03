@extends('adminlte::page')

@section('title', 'Manage Users')

@section('css')
<script src="https://cdn.tailwindcss.com"></script>
<script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
@stop

@section('content')
<div class="p-4 bg-[#f8fafc] min-h-screen" x-data="{ 
    nameSearch: '{{ $search ?? '' }}',
    roleFilter: '{{ $role ?? '' }}',
    statusFilter: '{{ $status ?? '' }}'
}">
    <div class="flex flex-col md:flex-row justify-between items-center mb-8 px-4 gap-6">
        <div class="flex items-center gap-3">
            <div class="w-12 h-12 bg-blue-600 rounded-2xl flex items-center justify-center shadow-lg shadow-blue-200">
                <i class="fas fa-users text-white text-xl"></i>
            </div>
            <h1 class="text-2xl font-black text-slate-800 uppercase tracking-tight">User Manager</h1>
        </div>

        <div class="flex-1 max-w-4xl w-full">
            <form action="{{ route('users.index') }}" method="GET"
                class="flex flex-wrap items-center gap-3 bg-white p-2 rounded-[2rem] shadow-xl shadow-slate-100 border border-gray-100">
                {{-- Search Input (Alpine.js Live Filter) --}}
                <div class="relative flex-1 min-w-[200px]">
                    <input type="text" name="search" x-model="nameSearch" placeholder="Search names, emails or IDs..."
                        class="w-full pl-10 pr-4 py-2.5 border-none rounded-xl text-xs focus:ring-4 focus:ring-blue-500/5 outline-none bg-gray-50/80 font-bold transition-all">
                    <div class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400">
                        <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" stroke-width="3" />
                        </svg>
                    </div>
                </div>

                {{-- Role Filter --}}
                <select name="role" x-model="roleFilter" @change="$el.form.submit()"
                    class="px-4 py-2.5 bg-gray-50 border-none rounded-xl text-[10px] font-black uppercase tracking-widest text-slate-500 focus:ring-4 focus:ring-blue-500/5 outline-none cursor-pointer">
                    <option value="">All Roles</option>
                    <option value="1">Admin</option>
                    <option value="0">Standard</option>
                </select>

                {{-- Status Filter --}}
                <select name="status" x-model="statusFilter" @change="$el.form.submit()"
                    class="px-4 py-2.5 bg-gray-50 border-none rounded-xl text-[10px] font-black uppercase tracking-widest text-slate-500 focus:ring-4 focus:ring-blue-500/5 outline-none cursor-pointer">
                    <option value="">All Status</option>
                    <option value="enrolled">Enrolled</option>
                    <option value="not_enrolled">Not Enrolled</option>
                </select>

                <button type="submit"
                    class="p-2.5 bg-blue-600 text-white rounded-full hover:bg-blue-700 transition-colors shadow-md shadow-blue-100 mx-1">
                    <i class="fas fa-search"></i>
                </button>

                @if($search !== '' || $role !== '' || $status !== '')
                    <a href="{{ route('users.index') }}"></a>
                @endif
            </form>
        </div>

        <a href="{{ route('users.create') }}"
            class="flex items-center gap-2 px-6 py-3 bg-blue-600 text-white hover:bg-blue-700 rounded-xl transition text-xs font-bold uppercase tracking-widest shadow-md whitespace-nowrap">
            <i class="fas fa-user-plus"></i> Add New User
        </a>
    </div>

    @if(session('success'))
        <div
            class="mb-6 px-6 py-4 bg-green-50 border border-green-200 text-green-700 rounded-2xl font-bold flex items-center gap-3">
            <i class="fas fa-check-circle"></i> {{ session('success') }}
        </div>
    @endif

    <div class="bg-white rounded-[2.5rem] shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-slate-600">
                <thead
                    class="bg-gray-50/50 text-xs uppercase text-slate-500 font-black tracking-widest border-b border-gray-100">
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
                        <tr class="hover:bg-gray-50/50 transition-colors"
                            x-show="nameSearch === '' || '{{ strtolower($user->name) }}'.includes(nameSearch.toLowerCase())">
                            <td class="px-8 py-5 text-slate-800 font-bold">{{ $user->name }}</td>
                            <td class="px-8 py-5">{{ $user->email }}</td>
                            <td class="px-8 py-5">
                                @if($user->is_admin)
                                    <span
                                        class="px-3 py-1 bg-blue-100 text-blue-700 rounded-lg text-[10px] font-black uppercase tracking-widest shadow-sm">Admin</span>
                                @else
                                    <span
                                        class="px-3 py-1 bg-gray-100 text-gray-500 rounded-lg text-[10px] font-black uppercase tracking-widest shadow-sm border border-gray-200/50">Standard</span>
                                @endif
                            </td>
                            <td class="px-8 py-5">
                                @if($user->fingerprint_id)
                                    <div class="flex items-center gap-2">
                                        <div
                                            class="w-2 h-2 rounded-full bg-green-500 shadow-[0_0_8px_rgba(34,197,94,0.5)] animate-pulse">
                                        </div>
                                        <span
                                            class="text-[10px] font-black text-green-600 uppercase tracking-widest">Enrolled</span>
                                    </div>
                                @else
                                    <div class="flex items-center gap-2">
                                        <div class="w-2 h-2 rounded-full bg-slate-300"></div>
                                        <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Not
                                            Enrolled</span>
                                    </div>
                                @endif
                            </td>
                            <td class="px-8 py-5 flex justify-end gap-3 text-lg">
                                <a href="{{ route('users.edit', $user) }}"
                                    class="p-2 text-slate-400 hover:text-blue-600 transition-colors">
                                    <i class="fas fa-edit"></i>
                                </a>
                                @if($user->id !== auth()->id())
                                    <form action="{{ route('users.destroy', $user) }}" method="POST"
                                        onsubmit="return confirm('Delete this user?');" class="inline">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="p-2 text-slate-400 hover:text-red-500 transition-colors">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5"
                                class="px-8 py-12 text-center text-slate-300 font-bold uppercase tracking-[0.2em] text-xs underline decoration-slate-100 underline-offset-8">
                                No matching records found
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($users->hasPages())
            <div class="px-8 py-6 border-t border-gray-100 bg-gray-50/20">
                {{ $users->links() }}
            </div>
        @endif
    </div>
</div>
@stop