<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Audit;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        // 1. Total scans today
        $scansToday = Audit::whereDate('created_at', Carbon::today())->count();

        // 2. Total successful grants today
        $accessGrantedToday = Audit::whereDate('created_at', Carbon::today())
            ->where('status', 'success')
            ->count();

        // 3. Total failed attempts today
        $failedToday = Audit::whereDate('created_at', Carbon::today())
            ->where('status', 'failed')
            ->count();

        // 4. Active users (enrolled fingerprints)
        $totalUsers = User::count();

        // 5. Last 7 days activity for chart
        $last7Days = collect(range(6, 0))->map(fn($i) => Carbon::today()->subDays($i)->toDateString());

        $rawChart = Audit::selectRaw('DATE(created_at) as date, count(*) as total')
            ->whereBetween('created_at', [Carbon::today()->subDays(6)->startOfDay(), Carbon::now()])
            ->groupBy('date')
            ->orderBy('date', 'asc')
            ->get()
            ->keyBy('date');

        $labels = $last7Days->map(fn($d) => Carbon::parse($d)->format('M d'));
        $data = $last7Days->map(fn($d) => $rawChart->get($d)?->total ?? 0);

        // 6. Recent logs (last 5)
        $recentLogs = Audit::with('user')
            ->latest()
            ->limit(5)
            ->get();

        return view('admin.dashboard', compact(
            'scansToday',
            'accessGrantedToday',
            'failedToday',
            'totalUsers',
            'labels',
            'data',
            'recentLogs'
        ));
    }
}