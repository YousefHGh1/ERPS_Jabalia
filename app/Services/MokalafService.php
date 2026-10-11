<?php

namespace App\Services;

use App\Http\Controllers\CitizenController;
use App\Models\Mokalaf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class MokalafService
{
    /**
     * البحث عن مكلف موجود بالهوية أو رقم الترخيص.
     */
    public function findExisting(?string $identityNumber, ?string $licenseNumber): ?Mokalaf
    {
        if ($identityNumber) {
            return Mokalaf::where('identity_number', $identityNumber)->first();
        }

        if ($licenseNumber) {
            return Mokalaf::where('license_number', $licenseNumber)->first();
        }

        return null;
    }

    /**
     * جلب بيانات المواطن من الأنظمة الخارجية مع fallback آمن.
     *
     * تُعيد مصفوفة جاهزة للاستخدام في واجهة إضافة المكلف.
     */
    public function fetchCitizenData(
        string $identityNumber,
        CitizenController $citizenController,
        Request $request
    ): array {
        // محاولة استخدام getCitizenForMokalaf أولاً
        if (method_exists($citizenController, 'getCitizenForMokalaf')) {
            try {
                $request->merge(['identity_number' => $identityNumber]);
                $citizenData = $citizenController->getCitizenForMokalaf($request);

                if ($citizenData->getStatusCode() === 200) {
                    $data = $citizenData->getData(true);
                    if ($data && !isset($data['error'])) {
                        return $data;
                    }
                }
            } catch (\Throwable $e) {
                Log::warning('getCitizenForMokalaf failed, trying fallback: ' . $e->getMessage());
            }
        }

        // Fallback إلى getCitizen العادي مع تحويل الحقول
        try {
            $request->merge(['czn_id' => $identityNumber]);
            $citizenData = $citizenController->getCitizen($request);

            if ($citizenData->getStatusCode() !== 200) {
                return [];
            }

            $basic = $citizenData->getData(true);
            if (!$basic || (isset($basic['error']) && $basic['error'])) {
                return [];
            }

            return [
                'FNAME_ARB' => $basic['first_name_ar'] ?? $basic['firstNameAr'] ?? $basic['first_name'] ?? '',
                'SNAME_ARB' => $basic['second_name_ar'] ?? $basic['secondNameAr'] ?? $basic['second_name'] ?? '',
                'TNAME_ARB' => $basic['third_name_ar'] ?? $basic['thirdNameAr'] ?? $basic['third_name'] ?? '',
                'LNAME_ARB' => $basic['last_name_ar'] ?? $basic['lastNameAr'] ?? $basic['last_name'] ?? '',
                'STREET_ARB' => $basic['street_ar'] ?? $basic['address_ar'] ?? $basic['address'] ?? '',
                'MOBILE' => $basic['mobile'] ?? $basic['mobile_number'] ?? $basic['phone'] ?? $basic['phone_number'] ?? '',
                'PHONE' => $basic['phone'] ?? $basic['landline'] ?? '',
                // شكل الحقول المستخدم حالياً في الواجهات
                'first_name_ar' => $basic['first_name_ar'] ?? $basic['firstNameAr'] ?? $basic['first_name'] ?? '',
                'second_name_ar' => $basic['second_name_ar'] ?? $basic['secondNameAr'] ?? $basic['second_name'] ?? '',
                'third_name_ar' => $basic['third_name_ar'] ?? $basic['thirdNameAr'] ?? $basic['third_name'] ?? '',
                'last_name_ar' => $basic['last_name_ar'] ?? $basic['lastNameAr'] ?? $basic['last_name'] ?? '',
                'mobile' => $basic['mobile'] ?? $basic['mobile_number'] ?? $basic['phone'] ?? $basic['phone_number'] ?? '',
                'street_ar' => $basic['street_ar'] ?? $basic['address_ar'] ?? $basic['address'] ?? '',
            ];
        } catch (\Throwable $e) {
            Log::error('MokalafService fetchCitizenData error: ' . $e->getMessage(), [
                'identity_number' => $identityNumber,
                'trace' => $e->getTraceAsString(),
            ]);
            return [];
        }
    }
}

