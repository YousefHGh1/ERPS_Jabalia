<?php

namespace App\Repositories\Contracts;

interface CraftLicenseRepositoryInterface extends RepositoryInterface
{
    public function searchByFileNumber(string $fileNumber);
    public function getUnpaidLicenses();
    public function getActiveLicenses();
}
