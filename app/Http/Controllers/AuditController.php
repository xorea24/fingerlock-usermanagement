<?php

namespace App\Http\Controllers;

use App\Models\Audit;
use Illuminate\Http\Request;

class AuditController extends Controller
{
    public function index(Request $request)
    {
        $filter = $request->query('filter', 'all'); // all | failed | success

        $query = Audit::with('user')->latest();

        if ($filter === 'failed') {
            $query->where('status', 'failed');
        } elseif ($filter === 'success') {
            $query->where('status', 'success');
        }

        $logs           = $query->paginate(50)->withQueryString();
        $totalFailed    = Audit::where('status', 'failed')->count();
        $recentFailed   = Audit::failed()->recent(60)->count(); // last 60 minutes
        $currentFilter  = $filter;

        return view('admin.audit.index', compact('logs', 'totalFailed', 'recentFailed', 'currentFilter'));
    }
}