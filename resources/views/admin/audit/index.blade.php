@extends('adminlte::page')

@section('title', 'Log Audit')

@section('css')
<script src="https://cdn.tailwindcss.com"></script>
@stop

@section('content')
<div class="p-4 bg-[#f8fafc] min-h-screen">
    <div class="flex justify-between items-center mb-8 px-4">
        <h1 class="text-2xl font-black text-slate-800 uppercase tracking-tight">System Audit logs</h1>
    </div>

    <div class="bg-white rounded-[2.5rem] shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-slate-600">
                <thead class="bg-gray-50/50 text-xs uppercase text-slate-500 font-black tracking-widest border-b border-gray-100">
                    <tr>
                        <th class="px-8 py-5">Timestamp</th>
                        <th class="px-8 py-5">User</th>
                        <th class="px-8 py-5">Action</th>
                        <th class="px-8 py-5">Description</th>
                        <th class="px-8 py-5">IP Address</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50 font-medium">
                    @forelse($logs as $log)
                        <tr class="hover:bg-gray-50/50 transition-colors">
                            <td class="px-8 py-5 text-slate-400 font-bold whitespace-nowrap">{{ $log->created_at->format('M d, Y H:i:s') }}</td>
                            <td class="px-8 py-5">
                                <div class="flex flex-col">
                                    <span class="text-slate-800 font-bold">{{ $log->user->name ?? 'System' }}</span>
                                    <span class="text-[10px] text-gray-400">{{ $log->user->email ?? '' }}</span>
                                </div>
                            </td>
                            <td class="px-8 py-5">
                                <span class="px-3 py-1 bg-blue-50 text-blue-700 rounded-lg text-xs font-black uppercase tracking-widest">{{ $log->action }}</span>
                            </td>
                            <td class="px-8 py-5 text-slate-500 max-w-xs truncate">{{ $log->description }}</td>
                            <td class="px-8 py-5 font-mono text-xs text-slate-400">{{ $log->ip_address }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-8 py-10 text-center text-slate-400 font-black uppercase tracking-widest text-sm">No log entries found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@stop
