@extends('adminlte::page')

@section('title', 'Audit Log')

@section('css')
<script src="https://cdn.tailwindcss.com"></script>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
<style>
    body { font-family: 'Inter', sans-serif; }
    .fade-in { animation: fadeIn 0.4s ease-out forwards; }
    @keyframes fadeIn { from { opacity: 0; transform: translateY(8px); } to { opacity: 1; transform: translateY(0); } }
    .alert-row { animation: alertPulse 2s ease-in-out; }
    @keyframes alertPulse {
        0%, 100% { background-color: rgba(254, 226, 226, 0.6); }
        50% { background-color: rgba(254, 202, 202, 0.9); }
    }
</style>
@stop

@section('content')
<div class="py-6 px-4 bg-gray-50 min-h-screen fade-in">

    {{-- ── Header ── --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-8">
        <div>
            <h1 class="text-3xl font-extrabold text-gray-900 tracking-tight">Security Audit Log</h1>
            <p class="text-sm text-gray-500 mt-1">Monitor all access events and fingerprint scan attempts.</p>
        </div>

        {{-- Alert Banner --}}
        @if($recentFailed > 0)
        <div class="flex items-center gap-3 bg-red-50 border border-red-200 text-red-700 px-5 py-3 rounded-2xl shadow-sm">
            <div class="w-3 h-3 bg-red-500 rounded-full animate-ping flex-shrink-0"></div>
            <div>
                <p class="text-sm font-bold">{{ $recentFailed }} Failed Attempt{{ $recentFailed > 1 ? 's' : '' }}</p>
                <p class="text-xs font-medium opacity-75">in the last 60 minutes</p>
            </div>
        </div>
        @endif
    </div>

    {{-- ── Filter Tabs ── --}}
    <div class="flex items-center gap-2 mb-6">
        <a href="{{ request()->url() }}?filter=all"
            class="px-5 py-2 rounded-xl text-xs font-bold uppercase tracking-widest transition-all
            {{ $currentFilter === 'all'
                ? 'bg-gray-900 text-white shadow'
                : 'bg-white text-gray-500 border border-gray-200 hover:bg-gray-50' }}">
            All Logs
        </a>
        <a href="{{ request()->url() }}?filter=failed"
            class="flex items-center gap-2 px-5 py-2 rounded-xl text-xs font-bold uppercase tracking-widest transition-all
            {{ $currentFilter === 'failed'
                ? 'bg-red-600 text-white shadow'
                : 'bg-white text-red-500 border border-red-200 hover:bg-red-50' }}">
            <i class="fas fa-exclamation-triangle text-xs"></i>
            Failed Attempts
            @if($totalFailed > 0)
                <span class="px-1.5 py-0.5 rounded-full {{ $currentFilter === 'failed' ? 'bg-red-700' : 'bg-red-100 text-red-600' }} text-[10px] font-black">
                    {{ $totalFailed }}
                </span>
            @endif
        </a>
        <a href="{{ request()->url() }}?filter=success"
            class="px-5 py-2 rounded-xl text-xs font-bold uppercase tracking-widest transition-all
            {{ $currentFilter === 'success'
                ? 'bg-green-600 text-white shadow'
                : 'bg-white text-green-600 border border-green-200 hover:bg-green-50' }}">
            Access Granted
        </a>
    </div>

    {{-- ── Table ── --}}
    <div class="bg-white rounded-[2rem] shadow-sm border border-gray-100 overflow-hidden">

        {{-- Failed alert banner inside table header --}}
        @if($currentFilter === 'failed' && $logs->count() > 0)
        <div class="px-8 py-3 bg-red-50 border-b border-red-100 flex items-center gap-3 text-red-700 text-sm font-semibold">
            <i class="fas fa-shield-alt"></i>
            Showing {{ $logs->total() }} unauthorized access attempt{{ $logs->total() !== 1 ? 's' : '' }} — review immediately.
        </div>
        @endif

        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-gray-600">
                <thead class="bg-gray-50 text-xs uppercase text-gray-400 font-bold tracking-widest border-b border-gray-100">
                    <tr>
                        <th class="px-6 py-4">Timestamp</th>
                        <th class="px-6 py-4">User / Source</th>
                        <th class="px-6 py-4">Status</th>
                        <th class="px-6 py-4">Action</th>
                        <th class="px-6 py-4">Fingerprint ID</th>
                        <th class="px-6 py-4">Description</th>
                        <th class="px-6 py-4">IP Address</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50 font-medium">
                    @forelse($logs as $log)
                        @php
                            $isFailed  = $log->status === 'failed';
                            $isSuccess = $log->status === 'success';
                        @endphp
                        <tr class="transition-colors {{ $isFailed ? 'bg-red-50/60 hover:bg-red-50 alert-row' : 'hover:bg-gray-50/50' }}">

                            {{-- Timestamp --}}
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="text-gray-400 font-semibold text-xs">{{ $log->created_at->format('M d, Y') }}</span><br>
                                <span class="text-gray-700 font-bold text-sm">{{ $log->created_at->format('H:i:s') }}</span>
                            </td>

                            {{-- User --}}
                            <td class="px-6 py-4">
                                @if($log->user)
                                    <div class="flex items-center gap-2">
                                        <div class="w-7 h-7 bg-gray-200 rounded-full flex items-center justify-center text-gray-600 text-xs font-bold flex-shrink-0">
                                            {{ strtoupper(substr($log->user->name, 0, 1)) }}
                                        </div>
                                        <div>
                                            <p class="text-gray-800 font-bold text-sm leading-tight">{{ $log->user->name }}</p>
                                            <p class="text-gray-400 text-[10px]">{{ $log->user->email }}</p>
                                        </div>
                                    </div>
                                @else
                                    <div class="flex items-center gap-2">
                                        <div class="w-7 h-7 bg-red-100 rounded-full flex items-center justify-center flex-shrink-0">
                                            <i class="fas fa-question text-red-400 text-xs"></i>
                                        </div>
                                        <span class="text-red-500 font-semibold text-sm">Unknown</span>
                                    </div>
                                @endif
                            </td>

                            {{-- Status Badge --}}
                            <td class="px-6 py-4">
                                @if($isFailed)
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-red-100 text-red-700 rounded-lg text-[10px] font-black uppercase tracking-widest">
                                        <i class="fas fa-times-circle"></i> Failed
                                    </span>
                                @elseif($isSuccess)
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-green-100 text-green-700 rounded-lg text-[10px] font-black uppercase tracking-widest">
                                        <i class="fas fa-check-circle"></i> Success
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-yellow-100 text-yellow-700 rounded-lg text-[10px] font-black uppercase tracking-widest">
                                        <i class="fas fa-exclamation-circle"></i> {{ $log->status }}
                                    </span>
                                @endif
                            </td>

                            {{-- Action --}}
                            <td class="px-6 py-4">
                                <span class="px-3 py-1 rounded-lg text-[10px] font-black uppercase tracking-widest
                                    {{ $isFailed ? 'bg-red-100 text-red-600' : 'bg-blue-50 text-blue-700' }}">
                                    {{ str_replace('_', ' ', $log->action) }}
                                </span>
                            </td>

                            {{-- Fingerprint ID --}}
                            <td class="px-6 py-4">
                                @if($log->fingerprint_id)
                                    <span class="inline-flex items-center gap-1 font-mono text-xs text-gray-600 bg-gray-100 px-2 py-1 rounded-lg">
                                        <i class="fas fa-fingerprint text-gray-400"></i>
                                        {{ $log->fingerprint_id }}
                                    </span>
                                @else
                                    <span class="text-gray-300 text-xs">—</span>
                                @endif
                            </td>

                            {{-- Description --}}
                            <td class="px-6 py-4 max-w-xs">
                                <p class="text-gray-500 text-xs leading-relaxed truncate" title="{{ $log->description }}">
                                    {{ $log->description ?? '—' }}
                                </p>
                            </td>

                            {{-- IP --}}
                            <td class="px-6 py-4">
                                <span class="font-mono text-xs text-gray-400">{{ $log->ip_address ?? '—' }}</span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-8 py-16 text-center">
                                <i class="fas fa-shield-alt text-4xl text-gray-200 mb-4 block"></i>
                                <p class="text-gray-400 font-bold uppercase tracking-widest text-sm">No log entries found.</p>
                                @if($currentFilter !== 'all')
                                    <a href="{{ request()->url() }}?filter=all" class="text-xs text-blue-500 mt-2 inline-block hover:underline">View all logs</a>
                                @endif
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if($logs->hasPages())
        <div class="px-8 py-4 border-t border-gray-50">
            {{ $logs->links() }}
        </div>
        @endif
    </div>

    {{-- ── How Hardware Should Report ── --}}
    <div class="mt-8 bg-white rounded-2xl border border-gray-100 p-6 shadow-sm">
        <h3 class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-4 flex items-center gap-2">
            <i class="fas fa-microchip"></i> Hardware Integration — How to Report Access Events
        </h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-xs font-mono">
            <div class="bg-green-50 rounded-xl p-4 border border-green-100">
                <p class="text-green-700 font-bold mb-2 text-[10px] uppercase tracking-widest">✓ Successful Scan</p>
                <pre class="text-green-800 whitespace-pre-wrap leading-relaxed">POST /hardware/access
{
  "fingerprint_id": "3",
  "status": "success"
}</pre>
            </div>
            <div class="bg-red-50 rounded-xl p-4 border border-red-100">
                <p class="text-red-700 font-bold mb-2 text-[10px] uppercase tracking-widest">✗ Failed / Unknown Scan</p>
                <pre class="text-red-800 whitespace-pre-wrap leading-relaxed">POST /hardware/access
{
  "fingerprint_id": "99",
  "status": "failed"
}</pre>
            </div>
        </div>
        <p class="mt-4 text-xs text-gray-400">
            <i class="fas fa-info-circle"></i>
            This endpoint is public (no login required) so the hardware can always report. It automatically looks up the fingerprint ID against enrolled users.
        </p>
    </div>
</div>
@stop
