<?php

namespace App\Repositories;

use App\Models\CraftLicense;
use App\Repositories\Contracts\CraftLicenseRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class CraftLicenseRepository extends BaseRepository implements CraftLicenseRepositoryInterface
{
    public function __construct(CraftLicense $model)
    {
        parent::__construct($model);
    }

    /**
     * Get craft licenses with relationships (eager loading)
     */
    public function getAllWithRelations(): Collection
    {
        return $this->model->with([
            'payments',
            'partners',
            'attachments'
        ])->get();
    }

    /**
     * Get paginated craft licenses with relations
     */
    public function paginateWithRelations(int $perPage = 15)
    {
        return $this->model->with([
            'payments',
            'partners',
            'attachments'
        ])->paginate($perPage);
    }

    /**
     * Search craft licenses
     */
    public function search(string $query, int $perPage = 15)
    {
        return $this->model->with(['payments'])
            ->where('file_number', 'like', "%{$query}%")
            ->orWhere('tax_number', 'like', "%{$query}%")
            ->orWhere('id_number', 'like', "%{$query}%")
            ->orWhere('owner_name', 'like', "%{$query}%")
            ->orWhere('commercial_name', 'like', "%{$query}%")
            ->paginate($perPage);
    }

    /**
     * Search by file number
     */
    public function searchByFileNumber(string $fileNumber)
    {
        return $this->model->where('file_number', $fileNumber)->first();
    }

    /**
     * Get unpaid licenses
     */
    public function getUnpaidLicenses()
    {
        return $this->model->where('remaining', '>', 0)->get();
    }

    /**
     * Get active licenses
     */
    public function getActiveLicenses()
    {
        return $this->model->where('status', 'active')->get();
    }

    /**
     * Get craft licenses by status
     */
    public function getByStatus(string $status): Collection
    {
        return $this->model->where('status', $status)->get();
    }

    /**
     * Get craft licenses by craft type
     */
    public function getByCraftType(string $craftType): Collection
    {
        return $this->model->where('craft_type', $craftType)->get();
    }

    /**
     * Get craft licenses with remaining balance
     */
    public function getWithRemainingBalance(): Collection
    {
        return $this->model->where('remaining', '>', 0)->get();
    }

    /**
     * Get craft licenses by neighborhood
     */
    public function getByNeighborhood(string $neighborhood): Collection
    {
        return $this->model->where('neighborhood', $neighborhood)->get();
    }
}
