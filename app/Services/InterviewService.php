<?php

namespace App\Services;

use App\Http\Resources\InterviewResource;
use App\Repositories\InterviewRepository;
use Illuminate\Support\Facades\DB;

class InterviewService
{
    public function __construct(
        private InterviewRepository $interviewRepository
    ) {}

    /**
     * Get all interviews
     */
    public function getAllInterviews($request)
    {
        try {
            $per_page = $request && $request->filled('paginate') ? $request->paginate : 1000;
            $filters = [];

            if ($request) {
                $filters = [
                    'search' => $request->filled('search') ? $request->search : null
                ];
            }

            $business_industries = $this->interviewRepository
                ->all($filters)
                ->paginate($per_page);

            return [
                'data' => InterviewResource::collection($business_industries),
                'page' => $business_industries->currentPage(),
                'last_page' => $business_industries->lastPage(),
                'per_page' => $business_industries->perPage(),
                'total' => $business_industries->total(),
                'from' => $business_industries->firstItem(),
                'to' => $business_industries->lastItem()
            ];
        } catch (\Throwable $t) {
            throw $t;
        }
    }

    /**
     * Get interviews for DataTables
     */
    public function getInterviewsForDatatable($request)
    {
        try {
            return $this->interviewRepository->getInterviewsForDatatable($request);
        } catch (\Throwable $t) {
            throw $t;
        }
    }

    /**
     * Get interview by ID
     */
    public function getInterviewById($id)
    {
        try {
            return new InterviewResource($this->interviewRepository->find($id));
        } catch (\Throwable $t) {
            throw $t;
        }
    }

    /**
     * Create interview
     */
    public function createInterview($data)
    {
        try {
            return DB::transaction(function() use($data) {
                return new InterviewResource($this->interviewRepository->create($data));
            });
        } catch (\Throwable $t) {
            throw $t;
        }
    }

    /**
     * Update interview
     */
    public function updateInterview($id, $data)
    {
        try {
            return DB::transaction(function() use($id, $data) {
                return new InterviewResource($this->interviewRepository->update($id, $data));
            });
        } catch (\Throwable $t) {
            throw $t;
        }
    }

    /**
     * Delete interview
     */
    public function deleteInterview($id)
    {
        try {
            return DB::transaction(function() use($id) {
                return $this->interviewRepository->delete($id);
            });
        } catch (\Throwable $t) {
            throw $t;
        }
    }
}