<?php

namespace App\Repositories;

use App\Models\Engineering;
use App\Repositories\Contracts\EngineeringRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class EngineeringRepository extends BaseRepository implements EngineeringRepositoryInterface
{
    public function __construct(Engineering $model)
    {
        parent::__construct($model);
    }

    /**
     * Get engineering records with relationships (eager loading)
     */
    public function getAllWithRelations(): Collection
    {
        return $this->model->with([
            'payments',
            'feeHistories',
            'attachments',
            'creator'
        ])->get();
    }

    /**
     * Get paginated engineering records with relations
     */
    public function paginateWithRelations(int $perPage = 15)
    {
        return $this->model->with([
            'payments',
            'feeHistories',
            'attachments',
            'creator'
        ])->paginate($perPage);
    }

    /**
     * Search engineering records
     */
    public function search(string $query, int $perPage = 15)
    {
        return $this->model->with(['payments', 'creator'])
            ->where('file_number', 'like', "%{$query}%")
            ->orWhere('owner_name', 'like', "%{$query}%")
            ->orWhere('identity_number', 'like', "%{$query}%")
            ->orWhere('location', 'like', "%{$query}%")
            ->paginate($perPage);
    }

    /**
     * Get engineering records by status
     */
    public function getByStatus(string $status): Collection
    {
        return $this->model->where('status', $status)->get();
    }

    /**
     * Search by file number
     */
    public function searchByFileNumber(string $fileNumber)
    {
        return $this->model->where('file_number', $fileNumber)->first();
    }

    /**
     * Get unpaid files
     */
    public function getUnpaidFiles()
    {
        return $this->model->where('remaining', '>', 0)->get();
    }

    /**
     * Get engineering records by subdivision type
     */
    public function getBySubdivisionType(string $subdivisionType): Collection
    {
        return $this->model->where('subdivision_type', $subdivisionType)->get();
    }

    /**
     * Get engineering records with remaining balance
     */
    public function getWithRemainingBalance(): Collection
    {
        return $this->model->where('remaining', '>', 0)->get();
    }

    /**
     * Get engineering records by creator
     */
    public function getByCreator(int $creatorId): Collection
    {
        return $this->model->where('created_by', $creatorId)->get();
    }

    /**
     * Get engineering records with penalties
     */
    public function getWithPenalties(): Collection
    {
        return $this->model->where('penalties', '>', 0)->get();
    }
}
