<?php

namespace App\Services;

use App\Models\Vehicle;
use Illuminate\Http\UploadedFile;

class VehicleService
{
    /**
     * إنشاء مركبة جديدة مع رفع الصور.
     */
    public function createVehicle(array $data, array $imageFiles = []): Vehicle
    {
        $data['images'] = $this->storeImages($imageFiles);
        return Vehicle::create($data);
    }

    /**
     * تحديث مركبة مع دمج الصور الجديدة مع الموجودة.
     */
    public function updateVehicle(Vehicle $vehicle, array $data, array $newImageFiles = []): Vehicle
    {
        if (!empty($newImageFiles)) {
            $newPaths = $this->storeImages($newImageFiles);
            $data['images'] = array_merge($vehicle->images ?? [], $newPaths);
        }
        $vehicle->update($data);
        return $vehicle;
    }

    /**
     * رفع مجموعة صور إلى التخزين وإرجاع مصفوفة المسارات.
     *
     * @param array<int, UploadedFile> $files
     * @return array<int, string>
     */
    private function storeImages(array $files): array
    {
        $paths = [];
        foreach ($files as $file) {
            if ($file instanceof UploadedFile) {
                $paths[] = $file->store('Vehicle', 'public');
            }
        }
        return $paths;
    }
}
