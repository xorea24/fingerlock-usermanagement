<?php

namespace App\Services;

use App\Http\Resources\ApplicantResource;
use App\Repositories\ApplicantRepository;
use Illuminate\Support\Facades\DB;

class ApplicantService
{
    public function __construct(
        private ApplicantRepository $applicantRepository
    ) {}

    /**
     * Get all applicants
     */
    public function getAllApplicants($request)
    {
        try {
            $per_page = $request && $request->filled('paginate') ? $request->paginate : 1000;
            $filters = [];

            if ($request) {
                $filters = [
                    'search' => $request->filled('search') ? $request->search : null
                ];
            }

            $business_industries = $this->applicantRepository
                ->all($filters)
                ->paginate($per_page);

            return [
                'data' => ApplicantResource::collection($business_industries),
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
     * Get applicants for DataTables
     */
    public function getApplicantsForDatatable($request)
    {
        try {
            return $this->applicantRepository->getApplicantsForDatatable($request);
        } catch (\Throwable $t) {
            throw $t;
        }
    }

    /**
     * Get applicant by ID
     */
    public function getApplicantById($id)
    {
        try {
            return new ApplicantResource($this->applicantRepository->find($id));
        } catch (\Throwable $t) {
            throw $t;
        }
    }

    /**
     * Create applicant
     */
    public function createApplicant($data)
    {
        try {
            return DB::transaction(function() use($data) {
                return new ApplicantResource($this->applicantRepository->create($data));
            });
        } catch (\Throwable $t) {
            throw $t;
        }
    }

    /**
     * Update applicant
     */
    public function updateApplicant($id, $data)
    {
        try {
            return DB::transaction(function() use($id, $data) {
                return new ApplicantResource($this->applicantRepository->update($id, $data));
            });
        } catch (\Throwable $t) {
            throw $t;
        }
    }

    /**
     * Delete applicant
     */
    public function deleteApplicant($id)
    {
        try {
            return DB::transaction(function() use($id) {
                return $this->applicantRepository->delete($id);
            });
        } catch (\Throwable $t) {
            throw $t;
        }
    }
}