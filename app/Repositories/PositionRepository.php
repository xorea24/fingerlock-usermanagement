<?php

namespace App\Repositories;

use App\Models\Position;
use App\Http\Resources\PositionResource;

class PositionRepository
{
    /**
     * Get all positions with optional filters
     */
    public function all($filters = [])
    {
        $query = Position::query();

        // Search filter
        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function($q) use($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        return $query->orderBy('name', 'asc');
    }

    /**
     * Find position by ID
     */
    public function find($id)
    {
        return Position::findOrFail($id);
    }

    /**
     * Create position
     */
    public function create(array $data)
    {
        return Position::create([
            'name' => $data['name'],
            'description' => $data['description'] ?? null,
        ]);
    }

    /**
     * Update position
     */
    public function update($id, array $data)
    {
        $position = $this->find($id);
        $position->update([
            'name' => $data['name'] ?? $position->name,
            'description' => $data['description'] ?? null,
        ]);
        return $position;
    }

    /**
     * Delete position
     */
    public function delete($id)
    {
        $position = $this->find($id);
        return $position->delete();
    }

    /**
     * Get positions for DataTables
     */
    public function getPositionsForDatatable($request)
    {
        $query = Position::query();

        // Search filter
        if ($request->filled('search')) {
            $search = $request->search['value'];
            $query->where(function($q) use($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Get total count before pagination
        $totalRecords = Position::count();
        $filteredRecords = $query->count();

        // Ordering
        $orderColumn = $request->input('order.0.column', 2);
        $orderDir = $request->input('order.0.dir', 'desc');

        $columns = ['name', 'description', 'created_at'];
        $orderBy = $columns[$orderColumn] ?? 'created_at';

        $query->orderBy($orderBy, $orderDir);

        // Pagination
        $start = $request->input('start', 0);
        $length = $request->input('length', 25);

        $data = $query->skip($start)->take($length)->get();
        $formattedData = PositionResource::collection($data);

        return [
            'draw' => intval($request->input('draw', 1)),
            'recordsTotal' => $totalRecords,
            'recordsFiltered' => $filteredRecords,
            'data' => $formattedData
        ];
    }
}