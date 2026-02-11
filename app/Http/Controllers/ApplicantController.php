<?php

namespace App\Http\Controllers;

use App\Http\Traits\ApiResponses;
use App\Http\Resources\Select2Resource;
use App\Models\Applicant;
use App\Services\ApplicantService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;

class ApplicantController extends Controller
{
    use ApiResponses;

    private $genders;
    private $civilStatuses;

    public function __construct(
        private ApplicantService $applicantService
    ) {
        $this->middleware('auth');
        $this->middleware('is_admin');
        $this->genders = Applicant::genders();
        $this->civilStatuses = Applicant::civilStatuses();
    }

    private function getGendersToString()
    {
        return implode(',', $this->genders);
    }

    private function getCivilStatusesToString()
    {
        return implode(',', $this->civilStatuses);
    }

    /**
     * Display the applicant management page
     */
    public function indexPage()
    {
        $page_title = 'Applicants';

        return view('admin.applicant.list', [
            'page_title' => $page_title,
            'genders' => $this->genders,
            'civil_statuses' => $this->civilStatuses,
        ]);
    }

    /**
     * Get applicants for DataTables
     */
    public function datatable(Request $request)
    {
        try {
            $data = $this->applicantService->getApplicantsForDatatable($request);
            return response()->json($data);
        } catch (\Exception $e) {
            return $this->error([$e->getMessage()]);
        }
    }

    /**
     * Display a listing of the resource (API).
     */
    public function index(Request $request)
    {
        try {
            $applicants = $this->applicantService->getAllApplicants($request);
            return $this->success($applicants, 'Applicants retrieved successfully');
        } catch (\Throwable $t) {
            return $this->error([$t->getMessage()], 'Failed to retrieve applicants');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        try {
            $applicant = $this->applicantService->getApplicantById($id);
            if ($applicant) {
                return $this->success($applicant, 'Applicant retrieved successfully');
            } else {
                return $this->error([], 'Applicant not found', 404);
            }
        } catch (\Throwable $t) {
            return $this->error([$t->getMessage()], 'Failed to retrieve applicant');
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'first_name' => 'required|string|max:255',
                'middle_name' => 'nullable|string|max:255',
                'last_name' => [
                    'required',
                    'string',
                    'max:255',
                    Rule::unique('applicants')->where(function($query) use($request) {
                        return $query->where('first_name', $request->first_name)
                                     ->where('middle_name', $request->middle_name)
                                     ->where('last_name', $request->last_name)
                                     ->where('extension_name', $request->extension_name);
                    })
                ],
                'extension_name' => 'nullable|string|max:20',
                'gender' => "required|string|in:{$this->getGendersToString()}",
                'birthdate' => 'required|date',
                'civil_status' => "required|string|in:{$this->getCivilStatusesToString()}",
                'address' => 'required|string',
                'position_id' => 'required|integer|exists:positions,id',
            ]);

            if ($validator->fails()) {
                return $this->error($validator->errors(), 'Validation failed', 422);
            }

            $applicant = $this->applicantService->createApplicant($validator->validated());
            return $this->success($applicant, 'Applicant created successfully', 201);
        } catch (\Throwable $t) {
            return $this->error([$t->getMessage()], 'Failed to create applicant');
        }
    }

     /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        try {
            $validator = Validator::make($request->all(), [
                'first_name' => 'required|string|max:255',
                'middle_name' => 'nullable|string|max:255',
                'last_name' => [
                    'required',
                    'string',
                    'max:255',
                    Rule::unique('applicants')
                        ->ignore($id)
                        ->where(function($query) use($request) {
                        return $query->where('first_name', $request->first_name)
                                     ->where('middle_name', $request->middle_name)
                                     ->where('last_name', $request->last_name)
                                     ->where('extension_name', $request->extension_name);
                    })
                ],
                'extension_name' => 'nullable|string|max:20',
                'gender' => "required|string|in:{$this->getGendersToString()}",
                'birthdate' => 'required|date',
                'civil_status' => "required|string|in:{$this->getCivilStatusesToString()}",
                'address' => 'required|string',
                'position_id' => 'required|integer|exists:positions,id',
            ]);

            if ($validator->fails()) {
                return $this->error($validator->errors(), 'Validation failed', 422);
            }

            $applicant = $this->applicantService->updateApplicant($id, $validator->validated());
            return $this->success($applicant, 'Applicant updated successfully');
        } catch (\Throwable $t) {
            return $this->error([$t->getMessage()], 'Failed to update applicant');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function delete($id)
    {
        try {
            $this->applicantService->deleteApplicant($id);
            return $this->success([], 'Applicant deleted successfully');
        } catch (\Throwable $t) {
            return $this->error([$t->getMessage()], 'Failed to delete applicant');
        }
    }

    /**
     * Get applicants for Select2
     */
    public function search(Request $request)
    {
        $search = $request->input('search', '');
        $limit = $request->input('limit', 20);

        $applicants = Applicant::select('id', 'name')
            ->when($search, function($query) use($search) {
                $query->where('first_name', 'like', "%{$search}%")
                  ->orWhere('middle_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('extension_name', 'like', "%{$search}%");
            })
            ->orderBy('name')
            ->limit($limit)
            ->get();

        $results = Select2Resource::collection($applicants);

        return response()->json(['results' => $results]);
    }
}