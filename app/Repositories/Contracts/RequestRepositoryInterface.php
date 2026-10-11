<?php

namespace App\Repositories\Contracts;

interface RequestRepositoryInterface extends RepositoryInterface
{
    public function getPendingRequests();
    public function getOverdueRequests();
    public function getUrgentRequests();
    public function getByStatus(int $statusId);
}
