<?php

namespace App\Repositories\Contracts;

interface RevenueBatchRepositoryInterface extends RepositoryInterface
{
    public function getOpenBatch();
    public function getClosedBatches();
}
