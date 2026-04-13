<?php

namespace App\Http\Controllers;

use App\Models\Audit;
use Illuminate\Http\Request;

// This MUST match the filename "AuditController"
class AuditController extends Controller 
{
    public function index()
    {
        $logs = Audit::with('user')->latest()->get();
        return view('admin.audit.index', compact('logs'));
    }
}