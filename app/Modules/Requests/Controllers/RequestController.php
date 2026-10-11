<?php

namespace App\Modules\Requests\Controllers;

use App\Modules\Shared\Controllers\BaseModuleController;
use App\Modules\Requests\Services\RequestService;
use Illuminate\Http\Request;

class RequestController extends BaseModuleController
{
    /**
     * Display a listing of requests
     */
    public function index(Request $request)
    {
        $filters = $request->only(['status', 'request_type', 'priority', 'date_from', 'date_to']);
        $requests = $this->getService()->getAll($filters, 15);
        
        $statistics = $this->getService()->getStatistics();
        $requestTypes = $this->getService()->getRequestTypes();
        $statuses = $this->getService()->getStatuses();

        return $this->indexResponse([
            'requests' => $requests,
            'statistics' => $statistics,
            'requestTypes' => $requestTypes,
            'statuses' => $statuses,
            'filters' => $filters,
        ]);
    }

    /**
     * Show the form for creating a new request
     */
    public function create()
    {
        $requestTypes = $this->getService()->getRequestTypes();
        $mokalafs = $this->getService()->getMokalafs();
        $sections = $this->getService()->getSections();
        $departments = $this->getService()->getDepartments();

        return $this->createResponse([
            'requestTypes' => $requestTypes,
            'mokalafs' => $mokalafs,
            'sections' => $sections,
            'departments' => $departments,
        ]);
    }

    /**
     * Store a newly created request
     */
    public function store(Request $request)
    {
        try {
            $data = $this->validateModuleRequest($request);
            $newRequest = $this->getService()->create($data);
            
            return $this->redirectToIndex(
                'Request created successfully',
                'success'
            );
            
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage());
        }
    }

    /**
     * Display the specified request
     */
    public function show($id)
    {
        $request = $this->getService()->findById($id);
        
        if (!$request) {
            return $this->errorResponse('Request not found');
        }

        $this->authorize('view', $request);
        
        $history = $this->getService()->getRequestHistory($id);
        $attachments = $this->getService()->getRequestAttachments($id);
        $payments = $this->getService()->getRequestPayments($id);

        return $this->showResponse([
            'request' => $request,
            'history' => $history,
            'attachments' => $attachments,
            'payments' => $payments,
        ]);
    }

    /**
     * Show the form for editing the specified request
     */
    public function edit($id)
    {
        $request = $this->getService()->findById($id);
        
        if (!$request) {
            return $this->errorResponse('Request not found');
        }

        $this->authorize('update', $request);
        
        $requestTypes = $this->getService()->getRequestTypes();
        $mokalafs = $this->getService()->getMokalafs();
        $statuses = $this->getService()->getStatuses();

        return $this->editResponse([
            'request' => $request,
            'requestTypes' => $requestTypes,
            'mokalafs' => $mokalafs,
            'statuses' => $statuses,
        ]);
    }

    /**
     * Update the specified request
     */
    public function update(Request $request, $id)
    {
        try {
            $data = $this->validateModuleRequest($request);
            $updatedRequest = $this->getService()->update($id, $data);
            
            return $this->redirectToIndex(
                'Request updated successfully',
                'success'
            );
            
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage());
        }
    }

    /**
     * Remove the specified request
     */
    public function destroy($id)
    {
        try {
            $request = $this->getService()->findById($id);
            
            if (!$request) {
                return $this->errorResponse('Request not found');
            }

            $this->authorize('delete', $request);
            
            $this->getService()->delete($id);
            
            return $this->redirectToIndex(
                'Request deleted successfully',
                'success'
            );
            
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage());
        }
    }

    /**
     * Update request status
     */
    public function updateStatus(Request $request, $id)
    {
        try {
            $data = $request->validate([
                'status_id' => 'required|exists:request_statuses,id',
                'notes' => 'nullable|string',
            ]);

            $this->getService()->updateStatus($id, $data['status_id'], $data['notes'] ?? null);
            
            return $this->successResponse('Request status updated successfully');
            
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage());
        }
    }

    /**
     * Add attachment to request
     */
    public function addAttachment(Request $request, $id)
    {
        try {
            $data = $request->validate([
                'file' => 'required|file|max:10240', // 10MB max
                'description' => 'nullable|string',
            ]);

            $attachment = $this->getService()->addAttachment($id, $data);
            
            return $this->successResponse('Attachment added successfully');
            
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage());
        }
    }

    /**
     * Search requests
     */
    public function search(Request $request)
    {
        $term = $request->get('q', '');
        $filters = $request->only(['status', 'request_type']);
        
        $requests = $this->getService()->search($term, $filters, 20);
        
        return response()->json($requests);
    }

    /**
     * Get validation rules
     */
    protected function getValidationRules(): array
    {
        return [
            'text' => 'required|string|max:1000',
            'request_type_id' => 'required|exists:request_types,id',
            'mokalaf_id' => 'nullable|exists:mokalafs,id',
            'priority' => 'required|in:normal,high,urgent',
            'deadline' => 'nullable|date|after:today',
            'notes' => 'nullable|string|max:1000',
        ];
    }

    /**
     * Get module-specific permissions
     */
    protected function getModulePermissions(): array
    {
        return [
            'view' => 'view requests',
            'create' => 'create requests',
            'update' => 'update requests',
            'delete' => 'delete requests',
            'update_status' => 'update request status',
            'add_attachment' => 'add request attachment',
        ];
    }
}
