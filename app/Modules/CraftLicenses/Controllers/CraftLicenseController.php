<?php

namespace App\Modules\CraftLicenses\Controllers;

use App\Modules\Shared\Controllers\BaseModuleController;
use App\Modules\CraftLicenses\Services\CraftLicenseService;
use Illuminate\Http\Request;

class CraftLicenseController extends BaseModuleController
{
    /**
     * Display a listing of craft licenses
     */
    public function index(Request $request)
    {
        $filters = $request->only(['status', 'craft_type', 'owner_name', 'registration_from', 'registration_to']);
        $licenses = $this->getService()->getAll($filters, 15);
        
        $statistics = $this->getService()->getStatistics();
        $craftTypes = $this->getCraftTypes();
        $statuses = $this->getStatuses();

        return $this->indexResponse([
            'licenses' => $licenses,
            'statistics' => $statistics,
            'craftTypes' => $craftTypes,
            'statuses' => $statuses,
            'filters' => $filters,
        ]);
    }

    /**
     * Show the form for creating a new craft license
     */
    public function create()
    {
        $craftTypes = $this->getCraftTypes();
        $mokalafs = $this->getMokalafs();
        
        return $this->createResponse([
            'craftTypes' => $craftTypes,
            'mokalafs' => $mokalafs,
        ]);
    }

    /**
     * Store a newly created craft license
     */
    public function store(Request $request)
    {
        try {
            $data = $this->validateModuleRequest($request);
            $license = $this->getService()->create($data);
            
            return $this->redirectToIndex(
                'Craft license created successfully',
                'success'
            );
            
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage());
        }
    }

    /**
     * Display the specified craft license
     */
    public function show($id)
    {
        $license = $this->getService()->findById($id);
        
        if (!$license) {
            return $this->errorResponse('Craft license not found');
        }

        $this->authorize('view', $license);
        
        $payments = $this->getService()->getLicensePayments($id);
        $feeHistory = $this->getService()->getFeeHistory($id);

        return $this->showResponse([
            'license' => $license,
            'payments' => $payments,
            'feeHistory' => $feeHistory,
        ]);
    }

    /**
     * Show the form for editing the specified craft license
     */
    public function edit($id)
    {
        $license = $this->getService()->findById($id);
        
        if (!$license) {
            return $this->errorResponse('Craft license not found');
        }

        $this->authorize('update', $license);
        
        $craftTypes = $this->getCraftTypes();
        $mokalafs = $this->getMokalafs();
        $statuses = $this->getStatuses();

        return $this->editResponse([
            'license' => $license,
            'craftTypes' => $craftTypes,
            'mokalafs' => $mokalafs,
            'statuses' => $statuses,
        ]);
    }

    /**
     * Update the specified craft license
     */
    public function update(Request $request, $id)
    {
        try {
            $data = $this->validateModuleRequest($request);
            $license = $this->getService()->update($id, $data);
            
            return $this->redirectToIndex(
                'Craft license updated successfully',
                'success'
            );
            
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage());
        }
    }

    /**
     * Remove the specified craft license
     */
    public function destroy($id)
    {
        try {
            $license = $this->getService()->findById($id);
            
            if (!$license) {
                return $this->errorResponse('Craft license not found');
            }

            $this->authorize('delete', $license);
            
            $this->getService()->delete($id);
            
            return $this->redirectToIndex(
                'Craft license deleted successfully',
                'success'
            );
            
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage());
        }
    }

    /**
     * Renew license
     */
    public function renew(Request $request, $id)
    {
        try {
            $data = $request->validate([
                'expiry_date' => 'required|date|after:today',
                'fee_amount' => 'nullable|numeric|min:0',
                'notes' => 'nullable|string|max:1000',
            ]);

            $license = $this->getService()->renewLicense($id, $data);
            
            return $this->successResponse('License renewed successfully');
            
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage());
        }
    }

    /**
     * Suspend license
     */
    public function suspend(Request $request, $id)
    {
        try {
            $data = $request->validate([
                'reason' => 'required|string|max:1000',
            ]);

            $license = $this->getService()->suspendLicense($id, $data['reason']);
            
            return $this->successResponse('License suspended successfully');
            
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage());
        }
    }

    /**
     * Reactivate license
     */
    public function reactivate($id)
    {
        try {
            $license = $this->getService()->reactivateLicense($id);
            
            return $this->successResponse('License reactivated successfully');
            
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage());
        }
    }

    /**
     * Search licenses
     */
    public function search(Request $request)
    {
        $term = $request->get('q', '');
        $filters = $request->only(['status', 'craft_type', 'registration_from', 'registration_to']);
        
        $licenses = $this->getService()->searchLicenses($term, $filters);
        
        return response()->json($licenses);
    }

    /**
     * Get expiring licenses
     */
    public function expiring(Request $request)
    {
        $days = $request->get('days', 30);
        $licenses = $this->getService()->getExpiringLicenses($days);
        
        return response()->json($licenses);
    }

    /**
     * Get licenses with outstanding payments
     */
    public function outstandingPayments()
    {
        $licenses = $this->getService()->getOutstandingPayments();
        
        return response()->json($licenses);
    }

    /**
     * Get validation rules
     */
    protected function getValidationRules(): array
    {
        return [
            'file_number' => 'required|string|max:50|unique:craft_licenses,file_number',
            'id_number' => 'required|string|max:50',
            'owner_name' => 'required|string|max:255',
            'craft_type' => 'required|string|max:100',
            'fee_amount' => 'required|numeric|min:0',
            'paid_amount' => 'nullable|numeric|min:0',
            'registration_date' => 'required|date',
            'expiry_date' => 'nullable|date|after:registration_date',
            'status' => 'required|in:draft,active,expired,suspended',
            'notes' => 'nullable|string|max:1000',
        ];
    }

    /**
     * Get craft types
     */
    private function getCraftTypes(): array
    {
        return [
            'carpentry' => 'Carpentry',
            'plumbing' => 'Plumbing',
            'electrical' => 'Electrical',
            'construction' => 'Construction',
            'metal_work' => 'Metal Work',
            'painting' => 'Painting',
            'tiling' => 'Tiling',
            'glass_work' => 'Glass Work',
        ];
    }

    /**
     * Get statuses
     */
    private function getStatuses(): array
    {
        return [
            'draft' => 'Draft',
            'active' => 'Active',
            'expired' => 'Expired',
            'suspended' => 'Suspended',
        ];
    }

    /**
     * Get mokalafs
     */
    private function getMokalafs(): \Illuminate\Support\Collection
    {
        return \App\Models\Mokalaf::orderBy('name')->get();
    }
}
