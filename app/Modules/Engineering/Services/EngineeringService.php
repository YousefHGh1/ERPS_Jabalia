<?php

namespace App\Modules\Engineering\Services;

use App\Modules\Shared\Services\BaseModuleService;
use App\Models\Engineering;
use App\Models\PaymentEngineering;
use App\Models\EngineeringFeeHistory;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class EngineeringService extends BaseModuleService
{
    /**
     * Initialize model
     */
    protected function initializeModel(): void
    {
        $this->modelClass = Engineering::class;
    }

    /**
     * Validation rules
     */
    protected array $validationRules = [
        'file_number' => 'required|string|max:50',
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

    /**
     * Create new engineering file with workflow
     */
    public function create(array $data): Engineering
    {
        DB::beginTransaction();
        
        try {
            // Generate file number if not provided
            if (!isset($data['file_number'])) {
                $data['file_number'] = $this->generateFileNumber($data['engineering_type']);
            }
            
            // Calculate remaining amount
            $data['remaining_amount'] = ($data['fee_amount'] ?? 0) - ($data['paid_amount'] ?? 0);
            
            // Set default status
            $data['status'] = $data['status'] ?? 'draft';
            
            $engineering = parent::create($data);
            
            // Create payment record if paid amount provided
            if ($data['paid_amount'] > 0) {
                $this->createPaymentRecord($engineering, $data['paid_amount']);
            }
            
            // Create fee history record
            $this->createFeeHistoryRecord($engineering, 'initial', $data['fee_amount'] ?? 0);
            
            // Send notifications
            $this->sendCreationNotifications($engineering);
            
            DB::commit();
            
            return $engineering;
            
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Update engineering file with payment tracking
     */
    public function update(int $id, array $data): Engineering
    {
        DB::beginTransaction();
        
        try {
            $engineering = $this->findById($id);
            
            if (!$engineering) {
                throw new \Exception("Engineering file not found");
            }
            
            $oldFeeAmount = $engineering->fee_amount;
            $oldPaidAmount = $engineering->paid_amount;
            
            // Recalculate amounts if fee or paid amount changed
            if (isset($data['fee_amount']) || isset($data['paid_amount'])) {
                $feeAmount = $data['fee_amount'] ?? $engineering->fee_amount;
                $paidAmount = $data['paid_amount'] ?? $engineering->paid_amount;
                $data['remaining_amount'] = $feeAmount - $paidAmount;
                
                // Create additional payment record if payment increased
                if ($paidAmount > $oldPaidAmount) {
                    $additionalPayment = $paidAmount - $oldPaidAmount;
                    $this->createPaymentRecord($engineering, $additionalPayment);
                }
                
                // Create fee history record if fee changed
                if (isset($data['fee_amount']) && $data['fee_amount'] !== $oldFeeAmount) {
                    $this->createFeeHistoryRecord($engineering, 'fee_change', $data['fee_amount'], $oldFeeAmount);
                }
            }
            
            $updatedEngineering = parent::update($id, $data);
            
            // Handle status change notifications
            if (isset($data['status']) && $data['status'] !== $engineering->status) {
                $this->sendStatusChangeNotifications($updatedEngineering, $engineering->status, $data['status']);
            }
            
            DB::commit();
            
            return $updatedEngineering;
            
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Approve engineering file
     */
    public function approveFile(int $id, array $approvalData): Engineering
    {
        $engineering = $this->findById($id);
        
        if (!$engineering) {
            throw new \Exception("Engineering file not found");
        }
        
        if ($engineering->status !== 'pending') {
            throw new \Exception("Only pending files can be approved");
        }
        
        return $this->update($id, array_merge($approvalData, [
            'status' => 'approved',
            'approval_date' => now(),
        ]));
    }

    /**
     * Reject engineering file
     */
    public function rejectFile(int $id, string $rejectionReason): Engineering
    {
        $engineering = $this->findById($id);
        
        if (!$engineering) {
            throw new \Exception("Engineering file not found");
        }
        
        if ($engineering->status !== 'pending') {
            throw new \Exception("Only pending files can be rejected");
        }
        
        return $this->update($id, [
            'status' => 'rejected',
            'notes' => $rejectionReason,
        ]);
    }

    /**
     * Complete engineering file
     */
    public function completeFile(int $id, array $completionData): Engineering
    {
        $engineering = $this->findById($id);
        
        if (!$engineering) {
            throw new \Exception("Engineering file not found");
        }
        
        if ($engineering->status !== 'approved') {
            throw new \Exception("Only approved files can be completed");
        }
        
        if ($engineering->remaining_amount > 0) {
            throw new \Exception("Cannot complete file with outstanding payments");
        }
        
        return $this->update($id, array_merge($completionData, [
            'status' => 'completed',
        ]));
    }

    /**
     * Get engineering files by status
     */
    public function getByStatus(string $status, int $perPage = 15): Collection
    {
        return $this->modelClass::where('status', $status)
            ->latest()
            ->paginate($perPage);
    }

    /**
     * Get files with outstanding payments
     */
    public function getOutstandingPayments(): Collection
    {
        return $this->modelClass::where('remaining_amount', '>', 0)
            ->whereIn('status', ['approved', 'completed'])
            ->orderBy('remaining_amount', 'desc')
            ->get();
    }

    /**
     * Search engineering files by multiple criteria
     */
    public function searchFiles(string $term, array $filters = []): Collection
    {
        $query = $this->modelClass::query();
        
        // Search term
        $query->where(function ($q) use ($term) {
            $q->where('file_number', 'like', "%{$term}%")
              ->orWhere('owner_name', 'like', "%{$term}%")
              ->orWhere('national_id', 'like', "%{$term}%")
              ->orWhere('plot_number', 'like', "%{$term}%")
              ->orWhere('neighborhood', 'like', "%{$term}%");
        });
        
        // Apply filters
        foreach ($filters as $key => $value) {
            if ($value !== null && $value !== '') {
                switch ($key) {
                    case 'status':
                        $query->where('status', $value);
                        break;
                    case 'engineering_type':
                        $query->where('engineering_type', $value);
                        break;
                    case 'neighborhood':
                        $query->where('neighborhood', $value);
                        break;
                    case 'application_from':
                        $query->whereDate('application_date', '>=', $value);
                        break;
                    case 'application_to':
                        $query->whereDate('application_date', '<=', $value);
                        break;
                }
            }
        }
        
        return $query->limit(50)->get();
    }

    /**
     * Get files by neighborhood
     */
    public function getByNeighborhood(string $neighborhood): Collection
    {
        return $this->modelClass::where('neighborhood', $neighborhood)
            ->latest()
            ->get();
    }

    /**
     * Get engineering statistics
     */
    public function getStatistics(): array
    {
        // Override parent's getStatistics since engineerings table doesn't have 'status' column
        $stats = [
            'total' => $this->modelClass::count(),
            'created_today' => $this->modelClass::whereDate('created_at', today())->count(),
            'created_this_month' => $this->modelClass::whereMonth('created_at', now()->month)->count(),
        ];
        
        // Add engineering specific statistics
        try {
            $stats['by_neighborhood'] = $this->modelClass::selectRaw('neighborhood, COUNT(*) as count')
                ->groupBy('neighborhood')
                ->orderBy('count', 'desc')
                ->limit(10)
                ->pluck('count', 'neighborhood')
                ->toArray();
        } catch (\Exception $e) {
            $stats['by_neighborhood'] = [];
        }
        
        try {
            $stats['outstanding_payments'] = $this->getOutstandingPayments()->count();
        } catch (\Exception $e) {
            $stats['outstanding_payments'] = 0;
        }
        
        // Financial statistics
        try {
            $stats['total_fees'] = $this->modelClass::sum('fee_amount');
        } catch (\Exception $e) {
            $stats['total_fees'] = 0;
        }
        
        try {
            $stats['total_paid'] = $this->modelClass::sum('paid_amount');
        } catch (\Exception $e) {
            $stats['total_paid'] = 0;
        }
        
        try {
            $stats['total_remaining'] = $this->modelClass::sum('remaining_amount');
        } catch (\Exception $e) {
            $stats['total_remaining'] = 0;
        }
        
        try {
            $stats['revenue_this_month'] = $this->modelClass::whereMonth('created_at', now()->month)
                ->sum('paid_amount');
        } catch (\Exception $e) {
            $stats['revenue_this_month'] = 0;
        }
        
        return $stats;
    }

    /**
     * Calculate average processing time
     */
    private function calculateAverageProcessingTime(): float
    {
        $files = $this->modelClass::whereIn('status', ['approved', 'rejected'])
            ->whereNotNull('application_date')
            ->whereNotNull('approval_date')
            ->get();
            
        if ($files->isEmpty()) {
            return 0;
        }
        
        $totalDays = $files->sum(function ($file) {
            return $file->application_date->diffInDays($file->approval_date);
        });
        
        return round($totalDays / $files->count(), 1);
    }

    /**
     * Calculate approval rate
     */
    private function calculateApprovalRate(): float
    {
        $totalProcessed = $this->modelClass::whereIn('status', ['approved', 'rejected'])->count();
        
        if ($totalProcessed === 0) {
            return 0;
        }
        
        $approved = $this->modelClass::where('status', 'approved')->count();
        
        return round(($approved / $totalProcessed) * 100, 1);
    }

    /**
     * Generate file number
     */
    private function generateFileNumber(string $engineeringType): string
    {
        $prefix = $this->getEngineeringTypePrefix($engineeringType);
        $year = date('Y');
        
        $count = $this->modelClass::whereYear('created_at', $year)
            ->where('engineering_type', $engineeringType)
            ->count();
        
        return "{$prefix}-{$year}-" . str_pad($count + 1, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Get engineering type prefix
     */
    private function getEngineeringTypePrefix(string $engineeringType): string
    {
        $prefixes = [
            'building' => 'BLDG',
            'structural' => 'STRU',
            'electrical' => 'ELEC',
            'plumbing' => 'PLUM',
            'architectural' => 'ARCH',
            'civil' => 'CIVL',
        ];
        
        return $prefixes[$engineeringType] ?? 'ENG';
    }

    /**
     * Create payment record
     */
    private function createPaymentRecord(Engineering $engineering, float $amount): PaymentEngineering
    {
        return PaymentEngineering::create([
            'engineering_id' => $engineering->id,
            'amount' => $amount,
            'payment_method' => 'cash',
            'payment_date' => now(),
            'status' => 'completed',
            'notes' => 'Payment for engineering file ' . $engineering->file_number,
            'created_by' => auth()->id(),
        ]);
    }

    /**
     * Create fee history record
     */
    private function createFeeHistoryRecord(Engineering $engineering, string $action, float $newAmount, ?float $oldAmount = null): EngineeringFeeHistory
    {
        return EngineeringFeeHistory::create([
            'engineering_id' => $engineering->id,
            'action' => $action,
            'old_amount' => $oldAmount,
            'new_amount' => $newAmount,
            'reason' => $this->getFeeHistoryReason($action),
            'created_by' => auth()->id(),
        ]);
    }

    /**
     * Get fee history reason
     */
    private function getFeeHistoryReason(string $action): string
    {
        $reasons = [
            'initial' => 'Initial fee set',
            'fee_change' => 'Fee amount updated',
            'adjustment' => 'Fee adjustment applied',
        ];
        
        return $reasons[$action] ?? 'Fee change';
    }

    /**
     * Send creation notifications
     */
    private function sendCreationNotifications(Engineering $engineering): void
    {
        // Implementation for sending notifications
        // This would integrate with the notification system
    }

    /**
     * Send status change notifications
     */
    private function sendStatusChangeNotifications(Engineering $engineering, string $oldStatus, string $newStatus): void
    {
        // Implementation for sending notifications
        // This would integrate with the notification system
    }

    /**
     * Apply search to query
     */
    protected function applySearch($query, string $term): void
    {
        $query->where(function ($q) use ($term) {
            $q->where('file_number', 'like', "%{$term}%")
              ->orWhere('owner_name', 'like', "%{$term}%")
              ->orWhere('national_id', 'like', "%{$term}%")
              ->orWhere('plot_number', 'like', "%{$term}%")
              ->orWhere('neighborhood', 'like', "%{$term}%");
        });
    }

    /**
     * Apply filter to query
     */
    protected function applyFilter($query, string $key, $value): void
    {
        switch ($key) {
            case 'status':
                $query->where('status', $value);
                break;
            case 'engineering_type':
                $query->where('engineering_type', $value);
                break;
            case 'neighborhood':
                $query->where('neighborhood', $value);
                break;
            case 'owner_name':
                $query->where('owner_name', 'like', "%{$value}%");
                break;
            case 'application_from':
                $query->whereDate('application_date', '>=', $value);
                break;
            case 'application_to':
                $query->whereDate('application_date', '<=', $value);
                break;
            default:
                parent::applyFilter($query, $key, $value);
        }
    }
}
