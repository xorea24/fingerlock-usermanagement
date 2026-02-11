<?php

namespace App\Repositories;

use App\Models\Interview;
use App\Http\Resources\InterviewResource;

class InterviewRepository
{
    /**
     * Get all interviews with optional filters
     */
    public function all($filters = [])
    {
        $query = Interview::query();

        // Search filter
        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function($q) use($search) {
                $q->where('interview_date', 'like', "%{$search}%")
                    ->orWhereHas('position', function($q) use($search) {
                        $q->where('name', 'like', "%{$search}%");
                    });
            });
        }

        return $query->orderBy('interview_date', 'desc');
    }

    /**
     * Find interview by ID
     */
    public function find($id)
    {
        return Interview::with('position')->findOrFail($id);
    }

    /**
     * Create interview
     */
    public function create(array $data)
    {
        return Interview::create([
            'interview_date' => $data['interview_date'],
            'position_id' => $data['position_id'],
        ])->load(['position']);
    }

    /**
     * Update interview
     */
    public function update($id, array $data)
    {
        $interview = $this->find($id);
        $interview->update([
            'interview_date' => $data['interview_date'] ?? $interview->interview_date,
            'position_id' => $data['position_id'] ?? $interview->position_id,
        ]);
        return $interview->load(['position']);
    }

    /**
     * Delete interview
     */
    public function delete($id)
    {
        $interview = $this->find($id);
        return $interview->delete();
    }

    /**
     * Get interviews for DataTables
     */
    public function getInterviewsForDatatable($request)
    {
        $query = Interview::select('interviews.*')
            ->join('positions', 'positions.id', '=', 'interviews.position_id')
            ->with('position');

        // Get total count before filter
        $total_records = $query->count();

        // Search filter
        if ($request->filled('search')) {
            $search = $request->search['value'];
            $query->where(function($q) use($search) {
                $q->where('interview_date', 'like', "%{$search}%")
                    ->orWhereHas('position', function($q) use($search) {
                        $q->where('name', 'like', "%{$search}%");
                    });
            });
        }

        // Get total count after filter
        $filtered_records = $query->count();

        // Ordering
        $order_column = $request->input('order.0.column', 0);
        $order_dir = $request->input('order.0.dir', 'desc');

        if (str_starts_with($order_column, 'position')) {
            $order_column = 'positions.name';
        }

        $columns = ['interview_date', 'positions.name', 'status', 'created_at'];
        $orderBy = $columns[$order_column] ?? 'interview_date';

        $query->orderBy($orderBy, $order_dir);

        // Pagination
        $start = $request->input('start', 0);
        $length = $request->input('length', 25);

        $data = $query->skip($start)->take($length)->get();
        $formattedData = InterviewResource::collection($data);

        return [
            'draw' => intval($request->input('draw', 1)),
            'recordsTotal' => $total_records,
            'recordsFiltered' => $filtered_records,
            'data' => $formattedData
        ];
    }
}