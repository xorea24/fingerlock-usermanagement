<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('is_admin');
    }

    public function index()
    {
        return to_route('dashboard');
    }

    public function dashboard()
    {
        return view('admin.dashboard');
    }

    public function positions()
    {
        return view('admin.position.list');
    }
}
