<?php

namespace App\Repositories\Contracts;

interface EngineeringRepositoryInterface extends RepositoryInterface
{
    public function searchByFileNumber(string $fileNumber);
    public function getUnpaidFiles();
}
