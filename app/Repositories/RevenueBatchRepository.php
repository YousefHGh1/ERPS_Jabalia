<?php

namespace App\Repositories;

use App\Models\RevenueBatch;
use App\Repositories\Contracts\RevenueBatchRepositoryInterface;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class RevenueBatchRepository implements RevenueBatchRepositoryInterface
{
    public function getOpenUnifiedBatchForDate(Carbon $date): ?RevenueBatch
    {
        return RevenueBatch::open()
            ->unified()
            ->forDate($date->toDateString())
            ->orderByDesc('id')
            ->first();
    }

    public function create(array $data): RevenueBatch
    {
        return RevenueBatch::create($data);
    }

    public function updateTotals(RevenueBatch $batch, float $amount): void
    {
        DB::table('revenue_batches')
            ->where('id', $batch->id)
            ->update([
                'total_amount'   => DB::raw('total_amount + ' . $amount),
                'payments_count' => DB::raw('payments_count + 1'),
                'updated_at'     => now(),
            ]);

        $batch->refresh();

        if ($batch->parent_batch_id) {
            DB::table('revenue_batches')
                ->where('id', $batch->parent_batch_id)
                ->update([
                    'total_amount'   => DB::raw('total_amount + ' . $amount),
                    'payments_count' => DB::raw('payments_count + 1'),
                    'updated_at'     => now(),
                ]);
        }
    }

    public function getOpenBatch(): ?RevenueBatch
    {
        return RevenueBatch::open()->first();
    }

    public function getClosedBatches(): Collection
    {
        return RevenueBatch::closed()->get();
    }

    // Base repository methods
    public function all(): Collection
    {
        return RevenueBatch::all();
    }

    public function find(int $id): ?RevenueBatch
    {
        return RevenueBatch::find($id);
    }

    public function findOrFail(int $id): RevenueBatch
    {
        return RevenueBatch::findOrFail($id);
    }

    public function delete(int $id): bool
    {
        return $this->findOrFail($id)->delete();
    }

    public function paginate(int $perPage = 15)
    {
        return RevenueBatch::paginate($perPage);
    }

    public function with(array $relations): \Illuminate\Database\Eloquent\Builder
    {
        return RevenueBatch::with($relations);
    }

    public function where($column, $operator = null, $value = null)
    {
        return RevenueBatch::where($column, $operator, $value);
    }

    public function update(int $id, array $data): RevenueBatch
    {
        $model = $this->findOrFail($id);
        $model->update($data);
        return $model->fresh();
    }
}
