<?php

namespace App\Modules\Requests\Services;

use App\Modules\Shared\Services\BaseModuleService;
use App\Models\Request as RequestModel;
use App\Models\RequestType;
use App\Models\RequestStatus;
use App\Models\Mokalaf;
use App\Models\RequestAttachment;
use App\Models\RequestPayment;
use App\Models\RequestHistory;
use App\Models\Section;
use App\Models\Department;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class RequestService extends BaseModuleService
{
    /**
     * Initialize model
     */
    protected function initializeModel(): void
    {
        $this->modelClass = RequestModel::class;
    }

    /**
     * Validation rules
     */
    protected array $validationRules = [
        'text' => 'required|string|max:1000',
        'request_type_id' => 'required|exists:request_types,id',
        'mokalaf_id' => 'nullable|exists:mokalafs,id',
        'priority' => 'required|in:normal,high,urgent',
        'deadline' => 'nullable|date|after:today',
        'notes' => 'nullable|string|max:1000',
    ];

    /**
     * Get request types
     */
    public function getRequestTypes(): Collection
    {
        return RequestType::where('is_active', true)
            ->orderBy('name')
            ->get();
    }

    /**
     * Get request statuses
     */
    public function getStatuses(): Collection
    {
        return RequestStatus::orderBy('order')
            ->get();
    }

    /**
     * Get mokalafs
     */
    public function getMokalafs(): Collection
    {
        return Mokalaf::orderBy('name')
            ->get();
    }

    /**
     * Get sections
     */
    public function getSections(): Collection
    {
        return Section::select('id', 'name')
            ->orderBy('name')
            ->get();
    }

    /**
     * Get departments
     */
    public function getDepartments(): Collection
    {
        return Department::select('id', 'name')
            ->orderBy('name')
            ->get();
    }

    /**
     * Create new request with workflow
     */
    public function create(array $data): RequestModel
    {
        DB::beginTransaction();
        
        try {
            // Set initial status
            $data['status_id'] = $this->getInitialStatusId();
            
            // Add sequence number
            $data['sequence_number'] = $this->generateSequenceNumber($data['request_type_id']);
            
            $request = parent::create($data);
            
            // Create history record
            $this->createHistoryRecord($request->id, 'created', 'Request created');
            
            // Send notifications
            $this->sendCreationNotifications($request);
            
            DB::commit();
            
            return $request;
            
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Update request status with workflow
     */
    public function updateStatus(int $id, int $statusId, ?string $notes = null): RequestModel
    {
        DB::beginTransaction();
        
        try {
            $request = $this->findById($id);
            
            if (!$request) {
                throw new \Exception("Request not found");
            }
            
            $oldStatus = $request->status_id;
            
            // Validate status transition
            $this->validateStatusTransition($oldStatus, $statusId);
            
            $request->update([
                'status_id' => $statusId,
                'notes' => $notes,
            ]);
            
            // Create history record
            $this->createHistoryRecord($id, 'status_changed', 
                "Status changed from {$oldStatus} to {$statusId}", 
                ['notes' => $notes]
            );
            
            // Send notifications
            $this->sendStatusChangeNotifications($request, $oldStatus, $statusId);
            
            // Handle completion workflow
            if ($this->isCompletedStatus($statusId)) {
                $this->handleRequestCompletion($request);
            }
            
            DB::commit();
            
            return $request;
            
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Add attachment to request
     */
    public function addAttachment(int $id, array $data): RequestAttachment
    {
        $request = $this->findById($id);
        
        if (!$request) {
            throw new \Exception("Request not found");
        }
        
        $file = $data['file'];
        $description = $data['description'] ?? null;
        
        // Store file
        $path = $file->store("requests/{$id}/attachments", 'public');
        
        // Create attachment record
        $attachment = RequestAttachment::create([
            'request_id' => $id,
            'file_name' => $file->getClientOriginalName(),
            'file_path' => $path,
            'file_size' => $file->getSize(),
            'file_type' => $file->getMimeType(),
            'description' => $description,
            'uploaded_by' => auth()->id(),
        ]);
        
        // Create history record
        $this->createHistoryRecord($id, 'attachment_added', 
            "Attachment added: {$file->getClientOriginalName()}"
        );
        
        return $attachment;
    }

    /**
     * Get request history
     */
    public function getRequestHistory(int $id): Collection
    {
        return RequestHistory::where('request_id', $id)
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Get request attachments
     */
    public function getRequestAttachments(int $id): Collection
    {
        return RequestAttachment::where('request_id', $id)
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Get request payments
     */
    public function getRequestPayments(int $id): Collection
    {
        return RequestPayment::where('request_id', $id)
            ->with('payment')
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Apply search to query
     */
    protected function applySearch($query, string $term): void
    {
        $query->where(function ($q) use ($term) {
            $q->where('text', 'like', "%{$term}%")
              ->orWhere('sequence_number', 'like', "%{$term}%")
              ->orWhereHas('mokalaf', function ($subQuery) use ($term) {
                  $subQuery->where('name', 'like', "%{$term}%")
                           ->orWhere('identity_number', 'like', "%{$term}%");
              });
        });
    }

    /**
     * Apply filter to query
     */
    protected function applyFilter($query, string $key, $value): void
    {
        switch ($key) {
            case 'status':
                $query->where('status_id', $value);
                break;
            case 'request_type':
                $query->where('request_type_id', $value);
                break;
            case 'priority':
                $query->where('priority', $value);
                break;
            case 'date_from':
                $query->whereDate('created_at', '>=', $value);
                break;
            case 'date_to':
                $query->whereDate('created_at', '<=', $value);
                break;
            default:
                parent::applyFilter($query, $key, $value);
        }
    }

    /**
     * Get initial status ID
     */
    private function getInitialStatusId(): int
    {
        $status = RequestStatus::where('is_default', true)->first();
        return $status ? $status->id : 1;
    }

    /**
     * Generate sequence number
     */
    private function generateSequenceNumber(int $requestTypeId): string
    {
        $year = date('Y');
        $prefix = RequestType::find($requestTypeId)->code ?? 'REQ';
        
        $count = RequestModel::whereYear('created_at', $year)
            ->where('request_type_id', $requestTypeId)
            ->count();
        
        return "{$prefix}-{$year}-" . str_pad($count + 1, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Create history record
     */
    private function createHistoryRecord(int $requestId, string $action, string $description, array $metadata = []): void
    {
        RequestHistory::create([
            'request_id' => $requestId,
            'action' => $action,
            'description' => $description,
            'metadata' => json_encode($metadata),
            'user_id' => auth()->id(),
        ]);
    }

    /**
     * Validate status transition
     */
    private function validateStatusTransition(int $fromStatus, int $toStatus): void
    {
        // Add business logic for status transitions
        // For now, allow all transitions
    }

    /**
     * Send creation notifications
     */
    private function sendCreationNotifications(RequestModel $request): void
    {
        // Implementation for sending notifications
        // This would integrate with the notification system
    }

    /**
     * Send status change notifications
     */
    private function sendStatusChangeNotifications(RequestModel $request, int $oldStatus, int $newStatus): void
    {
        // Implementation for sending notifications
        // This would integrate with the notification system
    }

    /**
     * Check if status is completed
     */
    private function isCompletedStatus(int $statusId): bool
    {
        $status = RequestStatus::find($statusId);
        return $status && $status->name === 'completed';
    }

    /**
     * Handle request completion
     */
    private function handleRequestCompletion(RequestModel $request): void
    {
        // Implementation for completion workflow
        // This would trigger file creation notifications or other processes
    }

    /**
     * Get module statistics
     */
    public function getStatistics(): array
    {
        $stats = parent::getStatistics();
        
        // Add request-specific statistics
        $stats['by_type'] = RequestModel::join('request_types', 'requests.request_type_id', '=', 'request_types.id')
            ->selectRaw('request_types.name as type_name, COUNT(*) as count')
            ->groupBy('request_types.name')
            ->pluck('count', 'type_name')
            ->toArray();
            
        $stats['by_status'] = RequestModel::join('request_statuses', 'requests.status_id', '=', 'request_statuses.id')
            ->selectRaw('request_statuses.name as status_name, COUNT(*) as count')
            ->groupBy('request_statuses.name')
            ->pluck('count', 'status_name')
            ->toArray();
            
        $stats['urgent'] = RequestModel::where('priority', 'urgent')->count();
        $stats['overdue'] = RequestModel::where('deadline', '<', now())
            ->where('status_id', '!=', $this->getCompletedStatusId())
            ->count();
            
        return $stats;
    }

    /**
     * Get completed status ID
     */
    private function getCompletedStatusId(): ?int
    {
        $status = RequestStatus::where('name', 'completed')->first();
        return $status ? $status->id : null;
    }
}
