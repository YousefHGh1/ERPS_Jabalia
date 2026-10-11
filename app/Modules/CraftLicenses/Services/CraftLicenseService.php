<?php

namespace App\Modules\CraftLicenses\Services;

use App\Modules\Shared\Services\BaseModuleService;
use App\Models\CraftLicense;
use App\Models\Mokalaf;
use App\Models\Payment;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class CraftLicenseService extends BaseModuleService
{
    /**
     * Initialize model
     */
    protected function initializeModel(): void
    {
        $this->modelClass = CraftLicense::class;
    }

    /**
     * Validation rules
     */
    protected array $validationRules = [
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

    /**
     * Create new craft license with workflow
     */
    public function create(array $data): CraftLicense
    {
        DB::beginTransaction();
        
        try {
            // Generate file number if not provided
            if (!isset($data['file_number'])) {
                $data['file_number'] = $this->generateFileNumber($data['craft_type']);
            }
            
            // Calculate remaining amount
            $data['remaining'] = ($data['fee_amount'] ?? 0) - ($data['paid_amount'] ?? 0);
            
            // Set default status
            $data['status'] = $data['status'] ?? 'draft';
            
            $license = parent::create($data);
            
            // Create payment record if paid amount provided
            if ($data['paid_amount'] > 0) {
                $this->createPaymentRecord($license, $data['paid_amount']);
            }
            
            // Send notifications
            $this->sendCreationNotifications($license);
            
            DB::commit();
            
            return $license;
            
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Update craft license with payment tracking
     */
    public function update(int $id, array $data): CraftLicense
    {
        DB::beginTransaction();
        
        try {
            $license = $this->findById($id);
            
            if (!$license) {
                throw new \Exception("Craft license not found");
            }
            
            // Recalculate amounts if fee or paid amount changed
            if (isset($data['fee_amount']) || isset($data['paid_amount'])) {
                $feeAmount = $data['fee_amount'] ?? $license->fee_amount;
                $paidAmount = $data['paid_amount'] ?? $license->paid_amount;
                $data['remaining'] = $feeAmount - $paidAmount;
                
                // Create additional payment record if payment increased
                if ($paidAmount > $license->paid_amount) {
                    $additionalPayment = $paidAmount - $license->paid_amount;
                    $this->createPaymentRecord($license, $additionalPayment);
                }
            }
            
            $updatedLicense = parent::update($id, $data);
            
            // Handle status change notifications
            if (isset($data['status']) && $data['status'] !== $license->status) {
                $this->sendStatusChangeNotifications($updatedLicense, $license->status, $data['status']);
            }
            
            DB::commit();
            
            return $updatedLicense;
            
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Get craft licenses by status
     */
    public function getByStatus(string $status, int $perPage = 15): Collection
    {
        return $this->modelClass::where('status', $status)
            ->latest()
            ->paginate($perPage);
    }

    /**
     * Get expiring licenses
     */
    public function getExpiringLicenses(int $days = 30): Collection
    {
        $expiryDate = now()->addDays($days);
        
        return $this->modelClass::where('expiry_date', '<=', $expiryDate)
            ->where('expiry_date', '>', now())
            ->where('status', 'active')
            ->orderBy('expiry_date')
            ->get();
    }

    /**
     * Get overdue licenses
     */
    public function getOverdueLicenses(): Collection
    {
        return $this->modelClass::where('expiry_date', '<', now())
            ->where('status', 'active')
            ->orderBy('expiry_date')
            ->get();
    }

    /**
     * Get licenses with outstanding payments
     */
    public function getOutstandingPayments(): Collection
    {
        return $this->modelClass::where('remaining', '>', 0)
            ->where('status', 'active')
            ->orderBy('remaining', 'desc')
            ->get();
    }

    /**
     * Search licenses by multiple criteria
     */
    public function searchLicenses(string $term, array $filters = []): Collection
    {
        $query = $this->modelClass::query();
        
        // Search term
        $query->where(function ($q) use ($term) {
            $q->where('file_number', 'like', "%{$term}%")
              ->orWhere('owner_name', 'like', "%{$term}%")
              ->orWhere('id_number', 'like', "%{$term}%")
              ->orWhere('craft_type', 'like', "%{$term}%");
        });
        
        // Apply filters
        foreach ($filters as $key => $value) {
            if ($value !== null && $value !== '') {
                switch ($key) {
                    case 'status':
                        $query->where('status', $value);
                        break;
                    case 'craft_type':
                        $query->where('craft_type', $value);
                        break;
                    case 'registration_from':
                        $query->whereDate('registration_date', '>=', $value);
                        break;
                    case 'registration_to':
                        $query->whereDate('registration_date', '<=', $value);
                        break;
                    case 'expiry_from':
                        $query->whereDate('expiry_date', '>=', $value);
                        break;
                    case 'expiry_to':
                        $query->whereDate('expiry_date', '<=', $value);
                        break;
                }
            }
        }
        
        return $query->limit(50)->get();
    }

    /**
     * Renew license
     */
    public function renewLicense(int $id, array $renewalData): CraftLicense
    {
        $license = $this->findById($id);
        
        if (!$license) {
            throw new \Exception("License not found");
        }
        
        if ($license->status !== 'active') {
            throw new \Exception("Only active licenses can be renewed");
        }
        
        // Calculate new expiry date
        $newExpiryDate = isset($renewalData['expiry_date']) 
            ? $renewalData['expiry_date']
            : $license->expiry_date->addYear();
        
        $renewalData['expiry_date'] = $newExpiryDate;
        $renewalData['status'] = 'active';
        
        return $this->update($id, $renewalData);
    }

    /**
     * Suspend license
     */
    public function suspendLicense(int $id, string $reason): CraftLicense
    {
        return $this->update($id, [
            'status' => 'suspended',
            'notes' => $reason,
        ]);
    }

    /**
     * Reactivate license
     */
    public function reactivateLicense(int $id): CraftLicense
    {
        $license = $this->findById($id);
        
        if (!$license) {
            throw new \Exception("License not found");
        }
        
        if ($license->status !== 'suspended') {
            throw new \Exception("Only suspended licenses can be reactivated");
        }
        
        // Check if expired
        if ($license->expiry_date && $license->expiry_date->isPast()) {
            return $this->update($id, ['status' => 'expired']);
        }
        
        return $this->update($id, ['status' => 'active']);
    }

    /**
     * Get license statistics
     */
    public function getStatistics(): array
    {
        $stats = parent::getStatistics();
        
        // Add craft license specific statistics
        $stats['by_status'] = $this->modelClass::selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();
            
        $stats['by_craft_type'] = $this->modelClass::selectRaw('craft_type, COUNT(*) as count')
            ->groupBy('craft_type')
            ->orderBy('count', 'desc')
            ->limit(10)
            ->pluck('count', 'craft_type')
            ->toArray();
            
        $stats['expiring_soon'] = $this->getExpiringLicenses(30)->count();
        $stats['overdue'] = $this->getOverdueLicenses()->count();
        $stats['outstanding_payments'] = $this->getOutstandingPayments()->count();
        
        // Financial statistics
        $stats['total_fees'] = $this->modelClass::sum('fee_amount');
        $stats['total_paid'] = $this->modelClass::sum('paid_amount');
        $stats['total_remaining'] = $this->modelClass::sum('remaining');
        $stats['revenue_this_month'] = $this->modelClass::whereMonth('created_at', now()->month)
            ->sum('paid_amount');
        
        return $stats;
    }

    /**
     * Generate file number
     */
    private function generateFileNumber(string $craftType): string
    {
        $prefix = $this->getCraftTypePrefix($craftType);
        $year = date('Y');
        
        $count = $this->modelClass::whereYear('created_at', $year)
            ->where('craft_type', $craftType)
            ->count();
        
        return "{$prefix}-{$year}-" . str_pad($count + 1, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Get craft type prefix
     */
    private function getCraftTypePrefix(string $craftType): string
    {
        $prefixes = [
            'carpentry' => 'CARP',
            'plumbing' => 'PLUM',
            'electrical' => 'ELEC',
            'construction' => 'CONS',
            'metal_work' => 'METL',
        ];
        
        return $prefixes[$craftType] ?? 'CRAFT';
    }

    /**
     * Create payment record
     */
    private function createPaymentRecord(CraftLicense $license, float $amount): Payment
    {
        return Payment::create([
            'craft_license_id' => $license->id,
            'amount' => $amount,
            'payment_method' => 'cash',
            'payment_date' => now(),
            'status' => 'completed',
            'notes' => 'Payment for craft license ' . $license->file_number,
            'created_by' => auth()->id(),
        ]);
    }

    /**
     * Send creation notifications
     */
    private function sendCreationNotifications(CraftLicense $license): void
    {
        // Implementation for sending notifications
        // This would integrate with the notification system
    }

    /**
     * Send status change notifications
     */
    private function sendStatusChangeNotifications(CraftLicense $license, string $oldStatus, string $newStatus): void
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
              ->orWhere('id_number', 'like', "%{$term}%")
              ->orWhere('craft_type', 'like', "%{$term}%");
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
            case 'craft_type':
                $query->where('craft_type', $value);
                break;
            case 'owner_name':
                $query->where('owner_name', 'like', "%{$value}%");
                break;
            case 'registration_from':
                $query->whereDate('registration_date', '>=', $value);
                break;
            case 'registration_to':
                $query->whereDate('registration_date', '<=', $value);
                break;
            case 'expiry_from':
                $query->whereDate('expiry_date', '>=', $value);
                break;
            case 'expiry_to':
                $query->whereDate('expiry_date', '<=', $value);
                break;
            default:
                parent::applyFilter($query, $key, $value);
        }
    }
}
