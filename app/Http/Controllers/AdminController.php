<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\PositionService;

class AdminController extends Controller
{
    protected $positionService;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(PositionService $positionService)
    {
        $this->positionService = $positionService;
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
        $positions = $this->positionService->getAllPositions();
        $heads = [
            ['label' => '', 'width' => 1],
            'Name',
            ['label' => 'Actions', 'no-export' => true, 'width' => 10],
        ];
        $config = [
            'order' => [[1, 'asc']],
            'columns' => [['orderable' => false], null, ['orderable' => false]],
        ];
        return view('admin.position.list', [
            'title' => 'Positions',
            'positions' => $positions,
            'heads' => $heads,
            'config' => $config,
        ]);
    }

    public function applicants()
    {
        $positions = $this->positionService->getAllPositions();
        $heads = [
            ['label' => '', 'width' => 1],
            'Name',
            ['label' => 'Actions', 'no-export' => true, 'width' => 10],
        ];
        $config = [
            'order' => [[1, 'asc']],
            'columns' => [['orderable' => false], null, ['orderable' => false]],
        ];
        return view('admin.position.list', [
            'title' => 'Positions',
            'positions' => $positions,
            'heads' => $heads,
            'config' => $config,
        ]);
    }
}