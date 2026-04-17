@extends('layouts.app')

@section('title', 'Dashboard')

@section('content_header')
    <div class="d-flex align-items-center justify-content-between">
        <h1 class="m-0 fw-bold" style="font-size:1.4rem; letter-spacing:.5px;">
            <i class="fas fa-chart-line me-2 text-info"></i> Dashboard
        </h1>
        <small class="text-muted">{{ now()->format('l, F j Y') }}</small>
    </div>
@endsection

@section('content_body')
    <div class="container-fluid py-2">

        {{-- ── STAT CARDS ─────────────────────────────────────────────────────────── --}}
        <div class="row g-3 mb-4">

            {{-- Total Users --}}
            <div class="col-xl-3 col-sm-6">
                <div class="card border-0 shadow-sm h-100"
                    style="border-left: 4px solid #17a2b8 !important; border-radius:12px;">
                    <div class="card-body d-flex align-items-center gap-3">
                        <div class="stat-icon rounded-circle d-flex align-items-center justify-content-center"
                            style="width:54px;height:54px;background:rgba(23,162,184,.15);flex-shrink:0;">
                            <i class="fas fa-users fa-lg text-info"></i>
                        </div>
                        <div>
                            <div class="text-muted small mb-1">Total Users</div>
                            <div class="fw-bold" style="font-size:1.8rem;line-height:1;">{{ $totalUsers }}</div>
                        </div>
                        <a href="{{ route('users.index') }}" class="stretched-link"></a>
                    </div>
                </div>
            </div>

            {{-- Scans Today --}}
            <div class="col-xl-3 col-sm-6">
                <div class="card border-0 shadow-sm h-100"
                    style="border-left: 4px solid #6f42c1 !important; border-radius:12px;">
                    <div class="card-body d-flex align-items-center gap-3">
                        <div class="stat-icon rounded-circle d-flex align-items-center justify-content-center"
                            style="width:54px;height:54px;background:rgba(111,66,193,.15);flex-shrink:0;">
                            <i class="fas fa-fingerprint fa-lg" style="color:#6f42c1;"></i>
                        </div>
                        <div>
                            <div class="text-muted small mb-1">Scans Today</div>
                            <div class="fw-bold" style="font-size:1.8rem;line-height:1;">{{ $scansToday }}</div>
                        </div>
                        <a href="{{ route('audit.index') }}" class="stretched-link"></a>
                    </div>
                </div>
            </div>

            {{-- Access Granted Today --}}
            <div class="col-xl-3 col-sm-6">
                <div class="card border-0 shadow-sm h-100"
                    style="border-left: 4px solid #28a745 !important; border-radius:12px;">
                    <div class="card-body d-flex align-items-center gap-3">
                        <div class="stat-icon rounded-circle d-flex align-items-center justify-content-center"
                            style="width:54px;height:54px;background:rgba(40,167,69,.15);flex-shrink:0;">
                            <i class="fas fa-check-circle fa-lg text-success"></i>
                        </div>
                        <div>
                            <div class="text-muted small mb-1">Access Granted Today</div>
                            <div class="fw-bold" style="font-size:1.8rem;line-height:1;">{{ $accessGrantedToday }}</div>
                        </div>
                        <a href="{{ route('audit.index') }}" class="stretched-link"></a>
                    </div>
                </div>
            </div>

            {{-- Failed Attempts Today --}}
            <div class="col-xl-3 col-sm-6">
                <div class="card border-0 shadow-sm h-100"
                    style="border-left: 4px solid #dc3545 !important; border-radius:12px;">
                    <div class="card-body d-flex align-items-center gap-3">
                        <div class="stat-icon rounded-circle d-flex align-items-center justify-content-center"
                            style="width:54px;height:54px;background:rgba(220,53,69,.15);flex-shrink:0;">
                            <i class="fas fa-exclamation-triangle fa-lg text-danger"></i>
                        </div>
                        <div>
                            <div class="text-muted small mb-1">Failed Attempts Today</div>
                            <div class="fw-bold" style="font-size:1.8rem;line-height:1;">{{ $failedToday }}</div>
                        </div>
                        <a href="{{ route('audit.index') }}" class="stretched-link"></a>
                    </div>
                </div>
            </div>
        </div>

        {{-- ── CHART + RECENT LOGS ──────────────────────────────────────────────────── --}}
        <div class="row g-3">

            {{-- Activity Chart --}}
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm" style="border-radius:12px;">
                    <div
                        class="card-header bg-transparent border-0 pt-3 pb-0 d-flex align-items-center justify-content-between">
                        <h6 class="mb-0 fw-semibold">
                            <i class="fas fa-chart-bar me-1 text-info"></i> Scan Activity — Last 7 Days
                        </h6>
                    </div>
                    <div class="card-body">
                        <canvas id="activityChart" height="100"></canvas>
                    </div>
                </div>
            </div>

            {{-- Recent Logs --}}
            <div class="col-lg-4">
                <div class="card border-0 shadow-sm h-100" style="border-radius:12px;">
                    <div class="card-header bg-transparent border-0 pt-3 pb-0">
                        <h6 class="mb-0 fw-semibold">
                            <i class="fas fa-history me-1 text-secondary"></i> Recent Activity
                        </h6>
                    </div>
                    <div class="card-body p-0">
                        <ul class="list-group list-group-flush">
                            @forelse ($recentLogs as $log)
                                <li class="list-group-item border-0 py-2 px-3">
                                    <div class="d-flex align-items-start gap-2">
                                        {{-- status badge --}}
                                        @php
                                            $badgeClass = match ($log->status) {
                                                'success' => 'bg-success',
                                                'failed' => 'bg-danger',
                                                'warning' => 'bg-warning text-dark',
                                                default => 'bg-secondary',
                                            };
                                            $icon = match ($log->status) {
                                                'success' => 'fa-check',
                                                'failed' => 'fa-times',
                                                'warning' => 'fa-exclamation',
                                                default => 'fa-circle',
                                            };
                                        @endphp
                                        <span class="badge {{ $badgeClass }} mt-1"
                                            style="width:22px;height:22px;display:flex;align-items:center;justify-content:center;border-radius:50%;">
                                            <i class="fas {{ $icon }} fa-xs"></i>
                                        </span>
                                        <div class="flex-grow-1 overflow-hidden">
                                            <div class="small fw-semibold text-truncate">
                                                {{ $log->action }}
                                            </div>
                                            <div class="text-muted" style="font-size:.75rem;">
                                                {{ $log->user?->name ?? 'Unknown' }} &middot;
                                                {{ $log->created_at->diffForHumans() }}
                                            </div>
                                        </div>
                                    </div>
                                </li>
                            @empty
                                <li class="list-group-item border-0 text-muted text-center py-4">
                                    <i class="fas fa-inbox fa-2x mb-2 d-block opacity-50"></i>
                                    No activity yet
                                </li>
                            @endforelse
                        </ul>
                    </div>
                    @if($recentLogs->isNotEmpty())
                        <div class="card-footer bg-transparent border-0 text-center pb-3">
                            <a href="{{ route('audit.index') }}" class="btn btn-sm btn-outline-secondary">
                                View all logs <i class="fas fa-arrow-right ms-1"></i>
                            </a>
                        </div>
                    @endif
                </div>
            </div>

        </div>
    </div>
@endsection

@push('js')
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.2/dist/chart.umd.min.js"></script>
    <script>
        (function () {
            const ctx = document.getElementById('activityChart').getContext('2d');

            const labels = @json($labels->values());
            const values = @json($data->values());

            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Total Scans',
                        data: values,
                        backgroundColor: 'rgba(23, 162, 184, 0.25)',
                        borderColor: 'rgba(23, 162, 184, 1)',
                        borderWidth: 2,
                        borderRadius: 6,
                        borderSkipped: false,
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: true,
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            callbacks: {
                                label: ctx => ` ${ctx.parsed.y} scan${ctx.parsed.y !== 1 ? 's' : ''}`
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: { precision: 0 },
                            grid: { color: 'rgba(0,0,0,.06)' }
                        },
                        x: {
                            grid: { display: false }
                        }
                    }
                }
            });
        })();
    </script>
@endpush