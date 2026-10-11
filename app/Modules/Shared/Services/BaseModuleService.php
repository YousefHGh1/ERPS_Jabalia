<?php

namespace App\Modules\Shared\Services;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

abstract class BaseModuleService
{
    /**
     * Model class for the module
     */
    protected $modelClass;

    /**
     * Repository class for the module
     */
    protected $repository;

    /**
     * Module name
     */
    protected string $moduleName;

    /**
     * Validation rules
     */
    protected array $validationRules = [];

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->moduleName = $this->getModuleName();
        $this->initializeModel();
        $this->initializeRepository();
    }

    /**
     * Get module name
     */
    protected function getModuleName(): string
    {
        $className = class_basename(static::class);
        return str_replace('Service', '', $className);
    }

    /**
     * Initialize model
     */
    protected function initializeModel(): void
    {
        $this->modelClass = "App\\Modules\\{$this->moduleName}\\Models\\" . $this->moduleName;
    }

    /**
     * Initialize repository
     */
    protected function initializeRepository(): void
    {
        $repositoryClass = "App\\Modules\\{$this->moduleName}\\Repositories\\{$this->moduleName}Repository";
        
        if (class_exists($repositoryClass)) {
            $this->repository = new $repositoryClass();
        }
    }

    /**
     * Get all records with optional filtering
     */
    public function getAll(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = $this->modelClass::query();

        // Apply filters
        foreach ($filters as $key => $value) {
            if ($value !== null && $value !== '') {
                $this->applyFilter($query, $key, $value);
            }
        }

        return $query->latest()->paginate($perPage);
    }

    /**
     * Find record by ID
     */
    public function findById(int $id): ?Model
    {
        return $this->modelClass::find($id);
    }

    /**
     * Create new record
     */
    public function create(array $data): Model
    {
        $data = $this->prepareData($data);
        
        $record = $this->modelClass::create($data);
        
        $this->afterCreate($record, $data);
        
        return $record;
    }

    /**
     * Update existing record
     */
    public function update(int $id, array $data): Model
    {
        $record = $this->findById($id);
        
        if (!$record) {
            throw new \Exception("Record not found");
        }

        $data = $this->prepareData($data, $record);
        
        $record->update($data);
        
        $this->afterUpdate($record, $data);
        
        return $record;
    }

    /**
     * Delete record
     */
    public function delete(int $id): bool
    {
        $record = $this->findById($id);
        
        if (!$record) {
            throw new \Exception("Record not found");
        }

        $this->beforeDelete($record);
        
        $deleted = $record->delete();
        
        if ($deleted) {
            $this->afterDelete($record);
        }
        
        return $deleted;
    }

    /**
     * Get validation rules
     */
    public function getValidationRules(): array
    {
        return $this->validationRules;
    }

    /**
     * Apply filter to query
     */
    protected function applyFilter($query, string $key, $value): void
    {
        // Default implementation - override in child classes
        $query->where($key, 'like', "%{$value}%");
    }

    /**
     * Prepare data before saving
     */
    protected function prepareData(array $data, ?Model $model = null): array
    {
        // Add created_by/updated_by
        if (auth()->check()) {
            $userId = auth()->id();
            
            if (!$model) {
                $data['created_by'] = $userId;
            }
            
            $data['updated_by'] = $userId;
        }

        return $data;
    }

    /**
     * Hook after creating record
     */
    protected function afterCreate(Model $record, array $data): void
    {
        // Override in child classes
    }

    /**
     * Hook after updating record
     */
    protected function afterUpdate(Model $record, array $data): void
    {
        // Override in child classes
    }

    /**
     * Hook before deleting record
     */
    protected function beforeDelete(Model $record): void
    {
        // Override in child classes
    }

    /**
     * Hook after deleting record
     */
    protected function afterDelete(Model $record): void
    {
        // Override in child classes
    }

    /**
     * Get module statistics
     */
    public function getStatistics(): array
    {
        return [
            'total' => $this->modelClass::count(),
            'active' => $this->modelClass::where('status', 'active')->count(),
            'inactive' => $this->modelClass::where('status', '!=', 'active')->count(),
            'created_today' => $this->modelClass::whereDate('created_at', today())->count(),
            'created_this_month' => $this->modelClass::whereMonth('created_at', now()->month)->count(),
        ];
    }

    /**
     * Search records
     */
    public function search(string $term, array $filters = [], int $limit = 20): Collection
    {
        $query = $this->modelClass::query();

        // Apply search
        $this->applySearch($query, $term);

        // Apply additional filters
        foreach ($filters as $key => $value) {
            if ($value !== null && $value !== '') {
                $this->applyFilter($query, $key, $value);
            }
        }

        return $query->limit($limit)->get();
    }

    /**
     * Apply search to query
     */
    protected function applySearch($query, string $term): void
    {
        // Default implementation - override in child classes
        $query->where(function ($q) use ($term) {
            $q->where('name', 'like', "%{$term}%")
              ->orWhere('description', 'like', "%{$term}%");
        });
    }

    /**
     * Bulk operations
     */
    public function bulkUpdate(array $ids, array $data): int
    {
        $data = $this->prepareData($data);
        
        return $this->modelClass::whereIn('id', $ids)->update($data);
    }

    public function bulkDelete(array $ids): int
    {
        return $this->modelClass::whereIn('id', $ids)->delete();
    }

    /**
     * Export data
     */
    public function export(array $filters = [], string $format = 'csv'): string
    {
        $records = $this->getAll($filters, 1000);
        
        switch (strtolower($format)) {
            case 'csv':
                return $this->exportToCsv($records);
            case 'excel':
                return $this->exportToExcel($records);
            default:
                throw new \Exception("Unsupported export format: {$format}");
        }
    }

    /**
     * Export to CSV
     */
    protected function exportToCsv($records): string
    {
        // Implementation would go here
        return 'CSV export not implemented yet';
    }

    /**
     * Export to Excel
     */
    protected function exportToExcel($records): string
    {
        // Implementation would go here
        return 'Excel export not implemented yet';
    }
}
