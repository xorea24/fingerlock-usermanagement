<?php

namespace App\Repositories;

use App\Models\Applicant;
use App\Http\Resources\ApplicantResource;

class ApplicantRepository
{
    /**
     * Get all applicants with optional filters
     */
    public function all($filters = [])
    {
        $query = Applicant::query();

        // Search filter
        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function($q) use($search) {
                $q->where('first_name', 'like', "%{$search}%")
                    ->orWhere('middle_name', 'like', "%{$search}%")
                    ->orWhere('last_name', 'like', "%{$search}%")
                    ->orWhere('extension_name', 'like', "%{$search}%")
                    ->orWhere('address', 'like', "%{$search}%")
                    ->orWhereHas('position', function($q) use($search) {
                        $q->where('name', 'like', "%{$search}%");
                    });
            });
        }

        return $query->orderBy('name', 'asc');
    }

    /**
     * Find applicant by ID
     */
    public function find($id)
    {
        return Applicant::with('position')->findOrFail($id);
    }

    /**
     * Create applicant
     */
    public function create(array $data)
    {
        return Applicant::create([
            'first_name' => $data['first_name'],
            'middle_name' => $data['middle_name'] ?? null,
            'last_name' => $data['last_name'],
            'extension_name' => $data['extension_name'] ?? null,
            'gender' => $data['gender'],
            'birthdate' => $data['birthdate'],
            'civil_status' => $data['civil_status'],
            'address' => $data['address'],
            'position_id' => $data['position_id'],
        ])->load(['position']);
    }

    /**
     * Update applicant
     */
    public function update($id, array $data)
    {
        $applicant = $this->find($id);
        $applicant->update([
            'first_name' => $data['first_name'] ?? $applicant->first_name,
            'middle_name' => $data['middle_name'] ?? null,
            'last_name' => $data['last_name'] ?? $applicant->last_name,
            'extension_name' => $data['extension_name'] ?? null,
            'gender' => $data['gender'] ?? $applicant->gender,
            'birthdate' => $data['birthdate'] ?? $applicant->birthdate,
            'civil_status' => $data['civil_status'] ?? $applicant->civil_status,
            'address' => $data['address'] ?? $applicant->address,
            'position_id' => $data['position_id'] ?? $applicant->position_id,
        ]);
        return $applicant->load(['position']);
    }

    /**
     * Delete applicant
     */
    public function delete($id)
    {
        $applicant = $this->find($id);
        return $applicant->delete();
    }

    /**
     * Get applicants for DataTables
     */
    public function getApplicantsForDatatable($request)
    {
        $query = Applicant::select('applicants.*')
            ->join('positions', 'positions.id', '=', 'applicants.position_id')
            ->with('position');

        // Get total count before filter
        $total_records = $query->count();

        // Search filter
        if ($request->filled('search')) {
            $search = $request->search['value'];
            $query->where(function($q) use($search) {
                $q->where('first_name', 'like', "%{$search}%")
                    ->orWhere('middle_name', 'like', "%{$search}%")
                    ->orWhere('last_name', 'like', "%{$search}%")
                    ->orWhere('extension_name', 'like', "%{$search}%")
                    ->orWhere('address', 'like', "%{$search}%")
                    ->orWhereHas('position', function($q) use($search) {
                        $q->where('name', 'like', "%{$search}%");
                    });
            });
        }

        // Get total count after filter
        $filtered_records = $query->count();

        // Ordering
        $order_column = $request->input('order.0.column', 5);
        $order_dir = $request->input('order.0.dir', 'desc');

        if (str_starts_with($order_column, 'position')) {
            $order_column = 'positions.name';
        }

        $columns = ['name', 'gender', 'birthdate', 'civil_status', 'address', 'positions.name', 'created_at'];
        $orderBy = $columns[$order_column] ?? 'created_at';

        $query->orderBy($orderBy, $order_dir);

        // Pagination
        $start = $request->input('start', 0);
        $length = $request->input('length', 25);

        $data = $query->skip($start)->take($length)->get();
        $formattedData = ApplicantResource::collection($data);

        return [
            'draw' => intval($request->input('draw', 1)),
            'recordsTotal' => $total_records,
            'recordsFiltered' => $filtered_records,
            'data' => $formattedData
        ];
    }
}