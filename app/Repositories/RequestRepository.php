<?php

namespace App\Repositories;

use App\Models\Request as RequestModel;
use App\Repositories\Contracts\RequestRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class RequestRepository implements RequestRepositoryInterface
{
    /**
     * Get pending requests
     */
    public function getPendingRequests()
    {
        return RequestModel::with(['requestType', 'status', 'mokalaf'])
            ->pending()
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Get overdue requests
     */
    public function getOverdueRequests()
    {
        return RequestModel::with(['requestType', 'status', 'mokalaf'])
            ->overdue()
            ->orderBy('deadline', 'asc')
            ->get();
    }

    /**
     * Get urgent requests
     */
    public function getUrgentRequests()
    {
        return RequestModel::with(['requestType', 'status', 'mokalaf'])
            ->urgent()
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Get requests by status
     */
    public function getByStatus(int $statusId)
    {
        return RequestModel::with(['requestType', 'status', 'mokalaf'])
            ->byStatus($statusId)
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * الحصول على الطلبات مع العلاقات باستخدام Query Scopes
     *
     * @param array $filters
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function getWithRelations(array $filters = [], int $perPage = 10): LengthAwarePaginator
    {
        $query = RequestModel::with(['requestType', 'status', 'mokalaf', 'creator']);

        // استخدام Query Scopes للفلاتر
        if (isset($filters['search']) && !empty($filters['search'])) {
            $query->search($filters['search']);
        }

        if (isset($filters['request_type_id']) && !empty($filters['request_type_id'])) {
            $query->byRequestType($filters['request_type_id']);
        }

        if (isset($filters['status_id']) && !empty($filters['status_id'])) {
            $query->byStatus($filters['status_id']);
        }

        if (isset($filters['mokalaf_id']) && !empty($filters['mokalaf_id'])) {
            $query->byMokalaf($filters['mokalaf_id']);
        }

        if (isset($filters['is_urgent']) && $filters['is_urgent'] !== null) {
            if ($filters['is_urgent']) {
                $query->urgent();
            } else {
                $query->where('is_urgent', false);
            }
        }

        if (isset($filters['from_date']) || isset($filters['to_date'])) {
            $query->dateRange($filters['from_date'] ?? null, $filters['to_date'] ?? null);
        }

        return $query->orderBy('created_at', 'desc')->paginate($perPage);
    }

    /**
     * الحصول على طلب مع جميع العلاقات
     *
     * @param int $id
     * @return RequestModel|null
     */
    public function findWithRelations(int $id): ?RequestModel
    {
        return RequestModel::with([
            'requestType',
            'status',
            'mokalaf',
            'creator',
            'attachments.creator',
            'responses.responseType',
            'responses.section',
            'responses.department',
            'responses.responsedBy',
            'responses.workflow',
            'responses.workflow.section',
            'responses.workflow.department'
        ])->find($id);
    }

    /**
     * الحصول على الطلبات حسب الحالة باستخدام Query Scope
     *
     * @param int $statusId
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function getByStatusPaginated(int $statusId, int $perPage = 10): LengthAwarePaginator
    {
        return RequestModel::with(['requestType', 'status', 'mokalaf'])
            ->byStatus($statusId)
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }

    /**
     * الحصول على الطلبات العاجلة باستخدام Query Scope
     *
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function getUrgentPaginated(int $perPage = 10): LengthAwarePaginator
    {
        return RequestModel::with(['requestType', 'status', 'mokalaf'])
            ->urgent()
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }

    /**
     * الحصول على الطلبات قيد الانتظار باستخدام Query Scope
     *
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function getPendingPaginated(int $perPage = 10): LengthAwarePaginator
    {
        return RequestModel::with(['requestType', 'status', 'mokalaf'])
            ->pending()
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }

    /**
     * البحث في الطلبات باستخدام Query Scope
     *
     * @param string $term
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function search(string $term, int $perPage = 10): LengthAwarePaginator
    {
        return RequestModel::with(['requestType', 'status', 'mokalaf'])
            ->search($term)
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }

    /**
     * الحصول على الطلبات التي اقترب موعدها
     *
     * @param int $days
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function getDeadlineApproaching(int $days = 7, int $perPage = 10): LengthAwarePaginator
    {
        return RequestModel::with(['requestType', 'status', 'mokalaf'])
            ->deadlineApproaching($days)
            ->orderBy('deadline', 'asc')
            ->paginate($perPage);
    }

    /**
     * الحصول على الطلبات المتأخرة
     *
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function getOverduePaginated(int $perPage = 10): LengthAwarePaginator
    {
        return RequestModel::with(['requestType', 'status', 'mokalaf'])
            ->overdue()
            ->orderBy('deadline', 'asc')
            ->paginate($perPage);
    }

    // Base repository methods
    public function all(): Collection
    {
        return RequestModel::all();
    }

    public function find(int $id): ?RequestModel
    {
        return RequestModel::find($id);
    }

    public function findOrFail(int $id): RequestModel
    {
        return RequestModel::findOrFail($id);
    }

    public function create(array $data): RequestModel
    {
        return RequestModel::create($data);
    }

    public function update(int $id, array $data): RequestModel
    {
        $model = $this->findOrFail($id);
        $model->update($data);
        return $model->fresh();
    }

    public function delete(int $id): bool
    {
        return $this->findOrFail($id)->delete();
    }

    public function paginate(int $perPage = 15)
    {
        return RequestModel::paginate($perPage);
    }

    public function with(array $relations): \Illuminate\Database\Eloquent\Builder
    {
        return RequestModel::with($relations);
    }

    public function where($column, $operator = null, $value = null)
    {
        return RequestModel::where($column, $operator, $value);
    }
}
