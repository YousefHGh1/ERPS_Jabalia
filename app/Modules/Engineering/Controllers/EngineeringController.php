<?php

namespace App\Modules\Engineering\Controllers;

use App\Modules\Shared\Controllers\BaseModuleController;
use App\Modules\Engineering\Services\EngineeringService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class EngineeringController extends BaseModuleController
{
    /**
     * Constructor - bypass the base middleware since we handle permissions in routes
     */
    public function __construct()
    {
        // Don't call parent constructor to avoid the middleware issue
        $this->moduleName = 'Engineering';
        $this->service = app(EngineeringService::class);
    }

    /**
     * Get service instance - override parent since we bypass constructor
     */
    protected function getService()
    {
        return $this->service;
    }

    /**
     * Display a listing of engineering files
     */
    public function index(Request $request)
    {
        $filters = $request->only(['status', 'engineering_type', 'neighborhood', 'application_from', 'application_to']);
        $files = $this->getService()->getAll($filters, 15);

        $statistics = $this->getService()->getStatistics();
        $engineeringTypes = $this->getEngineeringTypes();
        $neighborhoods = $this->getNeighborhoods();
        $statuses = $this->getStatuses();

        return $this->indexResponse([
            'files' => $files,
            'engineerings' => $files, // Alias for compatibility with existing views
            'statistics' => $statistics,
            'engineeringTypes' => $engineeringTypes,
            'neighborhoods' => $neighborhoods,
            'statuses' => $statuses,
            'filters' => $filters,
        ]);
    }

    /**
     * Show the form for creating a new engineering file
     */
    public function create()
    {
        $engineeringTypes = $this->getEngineeringTypes();
        $neighborhoods = $this->getNeighborhoods();
        
        return $this->createResponse([
            'engineeringTypes' => $engineeringTypes,
            'neighborhoods' => $neighborhoods,
        ]);
    }

    /**
     * Store a newly created engineering file
     */
    public function store(Request $request)
    {
        try {
            $data = $this->validateModuleRequest($request);
            $file = $this->getService()->create($data);
            
            return $this->redirectToIndex(
                'Engineering file created successfully',
                'success'
            );
            
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage());
        }
    }

    /**
     * Display the specified engineering file
     */
    public function show($id)
    {
        $file = $this->getService()->findById($id);
        
        if (!$file) {
            return $this->errorResponse('Engineering file not found');
        }

        $this->authorize('view', $file);
        
        $payments = $this->getService()->getFilePayments($id);
        $feeHistory = $this->getService()->getFeeHistory($id);

        return $this->showResponse([
            'file' => $file,
            'payments' => $payments,
            'feeHistory' => $feeHistory,
        ]);
    }

    /**
     * Show the form for editing the specified engineering file
     */
    public function edit($id)
    {
        $file = $this->getService()->findById($id);
        
        if (!$file) {
            return $this->errorResponse('Engineering file not found');
        }

        $this->authorize('update', $file);
        
        $engineeringTypes = $this->getEngineeringTypes();
        $neighborhoods = $this->getNeighborhoods();
        $statuses = $this->getStatuses();

        return $this->editResponse([
            'file' => $file,
            'engineeringTypes' => $engineeringTypes,
            'neighborhoods' => $neighborhoods,
            'statuses' => $statuses,
        ]);
    }

    /**
     * Update the specified engineering file
     */
    public function update(Request $request, $id)
    {
        try {
            $data = $this->validateModuleRequest($request);
            $file = $this->getService()->update($id, $data);
            
            return $this->redirectToIndex(
                'Engineering file updated successfully',
                'success'
            );
            
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage());
        }
    }

    /**
     * Remove the specified engineering file
     */
    public function destroy($id)
    {
        try {
            $file = $this->getService()->findById($id);
            
            if (!$file) {
                return $this->errorResponse('Engineering file not found');
            }

            $this->authorize('delete', $file);
            
            $this->getService()->delete($id);
            
            return $this->redirectToIndex(
                'Engineering file deleted successfully',
                'success'
            );
            
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage());
        }
    }

    /**
     * Approve engineering file
     */
    public function approve(Request $request, $id)
    {
        try {
            $data = $request->validate([
                'approval_notes' => 'nullable|string|max:1000',
                'approval_date' => 'required|date',
            ]);

            $file = $this->getService()->approveFile($id, $data);
            
            return $this->successResponse('File approved successfully');
            
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage());
        }
    }

    /**
     * Reject engineering file
     */
    public function reject(Request $request, $id)
    {
        try {
            $data = $request->validate([
                'rejection_reason' => 'required|string|max:1000',
            ]);

            $file = $this->getService()->rejectFile($id, $data['rejection_reason']);
            
            return $this->successResponse('File rejected successfully');
            
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage());
        }
    }

    /**
     * Complete engineering file
     */
    public function complete(Request $request, $id)
    {
        try {
            $data = $request->validate([
                'completion_notes' => 'nullable|string|max:1000',
                'completion_date' => 'required|date',
            ]);

            $file = $this->getService()->completeFile($id, $data);
            
            return $this->successResponse('File completed successfully');
            
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage());
        }
    }

    /**
     * Search engineering files
     */
    public function search(Request $request)
    {
        $term = $request->get('q', '');
        $filters = $request->only(['status', 'engineering_type', 'neighborhood', 'application_from', 'application_to']);
        
        $files = $this->getService()->searchFiles($term, $filters);
        
        return response()->json($files);
    }

    /**
     * Get files by neighborhood
     */
    public function byNeighborhood($neighborhood)
    {
        $files = $this->getService()->getByNeighborhood($neighborhood);
        
        return response()->json($files);
    }

    /**
     * Get files with outstanding payments
     */
    public function outstandingPayments()
    {
        $files = $this->getService()->getOutstandingPayments();
        
        return response()->json($files);
    }

    /**
     * Get validation rules
     */
    protected function getValidationRules(): array
    {
        return [
            'file_number' => [
                'required', 'string', 'max:50',
                Rule::unique('engineerings', 'file_number')->whereNull('deleted_at'),
            ],
            'national_id' => 'required|string|max:50',
            'owner_name' => 'required|string|max:255',
            'plot_number' => 'required|string|max:50',
            'neighborhood' => 'required|string|max:100',
            'engineering_type' => 'required|string|max:100',
            'fee_amount' => 'required|numeric|min:0',
            'paid_amount' => 'nullable|numeric|min:0',
            'application_date' => 'required|date',
            'approval_date' => 'nullable|date|after:application_date',
            'status' => 'required|in:draft,pending,approved,rejected,completed',
            'notes' => 'nullable|string|max:1000',
        ];
    }

    /**
     * Get engineering types
     */
    private function getEngineeringTypes(): array
    {
        return [
            'building' => 'Building',
            'structural' => 'Structural',
            'electrical' => 'Electrical',
            'plumbing' => 'Plumbing',
            'architectural' => 'Architectural',
            'civil' => 'Civil',
            'mechanical' => 'Mechanical',
            'landscape' => 'Landscape',
        ];
    }

    /**
     * Get neighborhoods
     */
    private function getNeighborhoods(): array
    {
        return [
            'Al-Nasr' => 'Al-Nasr',
            'Al-Rimal' => 'Al-Rimal',
            'Al-Quds' => 'Al-Quds',
            'Al-Maghazi' => 'Al-Maghazi',
            'Al-Shuja\'iya' => 'Al-Shuja\'iya',
            'Al-Tuffah' => 'Al-Tuffah',
            'Al-Daraj' => 'Al-Daraj',
            'Al-Sabra' => 'Al-Sabra',
        ];
    }

    /**
     * Get statuses
     */
    private function getStatuses(): array
    {
        return [
            'draft' => 'Draft',
            'pending' => 'Pending',
            'approved' => 'Approved',
            'rejected' => 'Rejected',
            'completed' => 'Completed',
        ];
    }
}
