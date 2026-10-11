<?php

namespace App\Http\Controllers;

use App\Models\Mokalaf;
use GuzzleHttp\Client;
use Illuminate\Http\Client\Request as ClientRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class CitizenController extends Controller
{
    public function getCitizen(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'czn_id' => 'required',
            ]);

            $accessToken = $this->getAccessToken();
            if (!$accessToken) {
                return $this->citizenAuthErrorResponse();
            }

            $headers = $this->getHeaders($accessToken, $validatedData['czn_id'], $request->ip(), $request->userAgent());
            $url = 'https://ws.gov.ps/citizen/id/' . $validatedData['czn_id'];
            $citizenData = $this->fetchCitizenData($url, $headers);
            
            if (isset($citizenData['data']['basic'])) {
                $basic = $citizenData['data']['basic'];
                $employeeFormat = $this->mapBasicToEmployeeFormat($basic);
                $merged = array_merge($basic, $employeeFormat);
                // تأكد أن الواجهة تجد الاسم تحت مفتاح name أيضاً
                if (!empty($merged['FULLNAME']) && empty($merged['name'])) {
                    $merged['name'] = $merged['FULLNAME'];
                }
                return response()->json($merged);
            }

            return response()->json(['error' => 'Data not found in response'], 404);

        } catch (\Exception $e) {
            Log::error('Citizen API Error: ' . $e->getMessage());
            return response()->json(['error' => 'Service Unavailable: ' . $e->getMessage()], 500);
        }
    }
    public function getCitizenRelations(Request $request)
    {
        $validatedData = $request->validate([
            'czn_id' => 'required',
        ], [
            'required' => __('required'),
        ]);

        $accessToken = $this->getAccessToken();
        if (!$accessToken) {
            return $this->citizenAuthErrorResponse();
        }

        $headers = $this->getHeaders($accessToken, $validatedData['czn_id'], $request->ip(), $request->userAgent());
        $url = 'https://ws.gov.ps/citizen/id/' . $validatedData['czn_id'];
        $citizenData = $this->fetchCitizenData($url, $headers);
        if (isset($citizenData)) {
            return $citizenData['data']['relatives'];
        } else {
            return response()->json(['error' => 'Failed to get citizen data.']);
        }
    }

    private function getAccessToken()
    {
        $clientId = config('services.citizen_api.client_id');
        $clientSecret = config('services.citizen_api.client_secret');

        if (empty($clientId) || empty($clientSecret)) {
            Log::warning('Citizen API: CITIZEN_API_CLIENT_ID or CITIZEN_API_CLIENT_SECRET is not set in .env');
            return null;
        }

        try {
            $client = new Client([
                'verify' => false,
                'timeout' => 12,
                'connect_timeout' => 5,
            ]);
            $response = $client->request('POST', 'https://ssoidp.gov.ps/sso/module.php/sspoauth2/token.php', [
                'headers' => ['Content-Type' => 'application/x-www-form-urlencoded'],
                'form_params' => [
                    'client_id' => $clientId,
                    'client_secret' => $clientSecret,
                ],
            ]);

            $tokenData = json_decode($response->getBody(), true);
            if (isset($tokenData['access_token'])) {
                return $tokenData['access_token'];
            }
            Log::warning('Citizen API: SSO did not return access_token', ['response' => $tokenData]);
            return null;
        } catch (\GuzzleHttp\Exception\RequestException $e) {
            $body = $e->hasResponse() ? (string) $e->getResponse()->getBody() : $e->getMessage();
            Log::error('Citizen API SSO request failed: ' . $e->getMessage(), ['response_body' => $body]);
            return null;
        } catch (\Exception $e) {
            Log::error('Citizen API getAccessToken: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * رسالة خطأ موحدة عند فشل مصادقة API المواطن (ws.gov.ps).
     * إن لم تكن بيانات الاعتماد مضبوطة في .env يُرجع رسالة توضيحية.
     */
    private function citizenAuthErrorResponse()
    {
        $clientId = config('services.citizen_api.client_id');
        $clientSecret = config('services.citizen_api.client_secret');
        if (empty($clientId) || empty($clientSecret)) {
            return response()->json([
                'error' => 'بيانات الدخول لخدمة المواطن غير مضبوطة. يرجى تعيين CITIZEN_API_CLIENT_ID و CITIZEN_API_CLIENT_SECRET في ملف .env',
            ], 503);
        }
        return response()->json(['error' => 'Failed to authenticate.'], 401);
    }

    private function getHeaders($accessToken, $userId, $userIp, $userAgent)
    {
        return [
            'x-sso-authorization' => $accessToken,
            'x-user-id' => $userId,
            'x-user-ip' => $userIp,
            'x-user-agent' => $userAgent,
            'Content-Type' => 'application/x-www-form-urlencoded',
        ];
    }

    private function fetchCitizenData($url, $headers)
    {
        $client = new Client([
            'verify' => false,
            'timeout' => 12,
            'connect_timeout' => 5,
        ]);
        $response = $client->request('GET', $url, [
            'headers' => $headers,
        ]);
        return json_decode($response->getBody(), true);
    }

    public function getCzn(Request $request)
    {
        return $this->getCitizen($request);
    }

    /**
     * جلب بيانات المواطن بصيغة متوافقة مع نموذج المكلفين (FNAME_ARB, SNAME_ARB, ...).
     * يُستدعى من صفحة إضافة مكلف بدل getuserData (mtit).
     */
    public function getCitizenForMokalaf(Request $request)
    {
        $idNo = $request->get('identity_number') ?: $request->get('P_DOC_ID') ?: $request->get('czn_id');
        if (!$idNo) {
            return response()->json(['error' => 'رقم الهوية مطلوب'], 422);
        }
        $request->merge(['czn_id' => $idNo]);
        $request->validate(['czn_id' => 'required']);
        try {
            $accessToken = $this->getAccessToken();
            if (!$accessToken) {
                return $this->citizenAuthErrorResponse();
            }
            $headers = $this->getHeaders($accessToken, $idNo, $request->ip(), $request->userAgent());
            $url = 'https://ws.gov.ps/citizen/id/' . $idNo;
            $citizenData = $this->fetchCitizenData($url, $headers);
            if (!isset($citizenData['data']['basic'])) {
                return response()->json(['error' => 'Data not found in response'], 404);
            }
            $basic = $citizenData['data']['basic'];
            $out = $this->mapBasicToMokalafFormat($basic);
            return response()->json($out);
        } catch (\Exception $e) {
            Log::error('Citizen API Error (getCitizenForMokalaf): ' . $e->getMessage());
            return response()->json(['error' => 'Service Unavailable: ' . $e->getMessage()], 500);
        }
    }

    /**
     * تحويل كائن basic من API المواطن إلى صيغة نموذج المكلفين (والهندسة).
     */
    private function mapBasicToMokalafFormat(array $basic): array
    {
        $first = $basic['first_name_ar'] ?? $basic['firstNameAr'] ?? $basic['first_name'] ?? $basic['FNAME_ARB'] ?? $basic['fname_arb'] ?? '';
        $second = $basic['second_name_ar'] ?? $basic['secondNameAr'] ?? $basic['second_name'] ?? $basic['SNAME_ARB'] ?? $basic['sname_arb'] ?? '';
        $third = $basic['third_name_ar'] ?? $basic['thirdNameAr'] ?? $basic['third_name'] ?? $basic['TNAME_ARB'] ?? $basic['tname_arb'] ?? '';
        $last = $basic['last_name_ar'] ?? $basic['lastNameAr'] ?? $basic['last_name'] ?? $basic['LNAME_ARB'] ?? $basic['lname_arb'] ?? '';

        if (trim("$first $second $third $last") === '') {
            $full = $basic['full_name_ar'] ?? $basic['fullNameAr'] ?? $basic['name_ar'] ?? $basic['nameAr'] ?? $basic['name'] ?? $basic['full_name'] ?? '';
            $parts = array_pad(preg_split('/\s+/u', trim($full), 4), 4, '');
            $first = $parts[0] ?? '';
            $second = $parts[1] ?? '';
            $third = $parts[2] ?? '';
            $last = $parts[3] ?? '';
        }

        $sex = $basic['sex'] ?? $basic['gender'] ?? $basic['SEX'] ?? $basic['gender_ar'] ?? '';
        $address = $basic['street_ar'] ?? $basic['STREET_ARB'] ?? $basic['address_ar'] ?? $basic['address'] ?? '';
        $socialStatus = $basic['social_status'] ?? $basic['SOCIAL_STATUS'] ?? $basic['socialStatus'] ?? '';
        $mobile = $basic['mobile'] ?? $basic['mobile_number'] ?? $basic['MOBILE'] ?? $basic['phone'] ?? $basic['phone_number'] ?? $basic['PHONE'] ?? $basic['cell_phone'] ?? '';
        $fullName = trim("$first $second $third $last");

        return [
            'FNAME_ARB' => $first,
            'SNAME_ARB' => $second,
            'TNAME_ARB' => $third,
            'LNAME_ARB' => $last,
            'FULLNAME' => $fullName,
            'SEX' => $sex,
            'STREET_ARB' => $address,
            'SOCIAL_STATUS' => $socialStatus,
            'MOBILE' => $mobile,
            'PHONE' => $mobile ?: ($basic['phone'] ?? $basic['PHONE'] ?? $basic['landline'] ?? ''),
        ];
    }

    /**
     * تحويل كائن basic من API المواطن إلى صيغة نموذج الموظف (FULLNAME, STREET_NAME, BIRTH_PLACE, BIRTH_DT, SEX).
     */
    private function mapBasicToEmployeeFormat(array $basic): array
    {
        $first = $basic['first_name_ar'] ?? $basic['firstNameAr'] ?? $basic['first_name'] ?? $basic['FNAME_ARB'] ?? $basic['fname_arb'] ?? '';
        $second = $basic['second_name_ar'] ?? $basic['secondNameAr'] ?? $basic['second_name'] ?? $basic['SNAME_ARB'] ?? $basic['sname_arb'] ?? '';
        $third = $basic['third_name_ar'] ?? $basic['thirdNameAr'] ?? $basic['third_name'] ?? $basic['TNAME_ARB'] ?? $basic['tname_arb'] ?? '';
        $last = $basic['last_name_ar'] ?? $basic['lastNameAr'] ?? $basic['last_name'] ?? $basic['LNAME_ARB'] ?? $basic['lname_arb'] ?? '';
        $fullName = trim("$first $second $third $last");
        if ($fullName === '') {
            $fullName = $basic['full_name_ar'] ?? $basic['fullNameAr'] ?? $basic['name_ar'] ?? $basic['nameAr'] ?? $basic['name'] ?? $basic['full_name'] ?? '';
        }
        $fullName = trim($fullName);
        $street = $basic['street_ar'] ?? $basic['STREET_ARB'] ?? $basic['address_ar'] ?? $basic['address'] ?? $basic['STREET_NAME'] ?? '';
        $birthPlace = $basic['birth_place_ar'] ?? $basic['birthPlace'] ?? $basic['BIRTH_PLACE'] ?? $basic['birthplace'] ?? '';
        $birthDt = $basic['birth_dt'] ?? $basic['birthDt'] ?? $basic['BIRTH_DT'] ?? $basic['birth_date'] ?? $basic['birthdate'] ?? '';
        $sex = $basic['sex'] ?? $basic['gender'] ?? $basic['SEX'] ?? $basic['gender_ar'] ?? '';
        if ($sex === 'male' || $sex === '1') {
            $sex = 'ذكر';
        } elseif ($sex === 'female' || $sex === '2') {
            $sex = 'أنثى';
        }
        $mobile = $basic['mobile'] ?? $basic['mobile_number'] ?? $basic['MOBILE'] ?? $basic['phone'] ?? $basic['phone_number'] ?? $basic['PHONE'] ?? $basic['cell_phone'] ?? '';
        return [
            'FULLNAME' => $fullName,
            'STREET_NAME' => $street,
            'BIRTH_PLACE' => $birthPlace,
            'BIRTH_DT' => $birthDt,
            'SEX' => $sex,
            'MOBILE' => $mobile,
        ];
    }

    /**
     * إدراج/تحديث بيانات مواطن محلياً (لنماذج الإخطارات وغيرها).
     * يُستدعى عند جلب بيانات من API المواطن وعدم وجود سجل محلي.
     * يُرجع id فارغاً لأن notice.user_id يشير إلى users وليس المكلفين.
     */
    public function insertUserData(Request $request)
    {
        $request->validate([
            'P_DOC_ID' => 'required|string|size:9',
        ]);
        $idNo = $request->P_DOC_ID;
        $name = trim(implode(' ', array_filter([
            $request->get('first_name'),
            $request->get('second_name'),
            $request->get('third_name'),
            $request->get('last_name'),
        ]))) ?: $idNo;
        $mokalaf = Mokalaf::updateOrCreate(
            ['identity_number' => $idNo],
            [
                'name' => $name,
                'mobile' => $request->get('mobile'),
                'address' => $request->get('address'),
            ]
        );
        return response()->json(['id' => $mokalaf->id]);
    }
}
