<?php

namespace App\Services;

use App\Models\Position;

class PositionService
{
    public function getAllPositions()
    {
        return Position::all();
    }
}