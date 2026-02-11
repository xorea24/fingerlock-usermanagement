<?php

namespace App\Http\Controllers;

class AdminController extends Controller
{
    protected $positionService;

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
        return to_route('positions.index');
    }
}