<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\CacheService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class CacheController extends Controller
{
    /**
     * مسح جميع الكاشات
     */
    public function clearAll(): JsonResponse
    {
        try {
            CacheService::clearAllCache();
            
            return response()->json([
                'success' => true,
                'message' => 'تم مسح جميع الكاشات بنجاح'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء مسح الكاش: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * مسح كاش الموظفين
     */
    public function clearEmployees(): JsonResponse
    {
        try {
            CacheService::clearEmployeesCache();
            
            return response()->json([
                'success' => true,
                'message' => 'تم مسح كاش الموظفين بنجاح'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء مسح كاش الموظفين: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * مسح كاش الطلبات
     */
    public function clearRequests(): JsonResponse
    {
        try {
            CacheService::clearRequestsCache();
            
            return response()->json([
                'success' => true,
                'message' => 'تم مسح كاش الطلبات بنجاح'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء مسح كاش الطلبات: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * تحديث إحصائيات الطلبات
     */
    public function refreshStats(): JsonResponse
    {
        try {
            CacheService::refreshRequestStats();
            
            return response()->json([
                'success' => true,
                'message' => 'تم تحديث الإحصائيات بنجاح',
                'stats' => CacheService::getRequestStats()
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء تحديث الإحصائيات: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * الحصول على معلومات الكاش
     */
    public function info(): JsonResponse
    {
        try {
            $redis = \Cache::store('redis');
            
            $info = [
                'driver' => config('cache.default'),
                'redis_connected' => false,
                'memory_usage' => 'N/A'
            ];

            if (method_exists($redis, 'connection')) {
                try {
                    $connection = $redis->connection();
                    $info['redis_connected'] = true;
                    $info['memory_usage'] = $connection->info('memory') ?? 'N/A';
                } catch (\Exception $e) {
                    $info['redis_error'] = $e->getMessage();
                }
            }

            return response()->json([
                'success' => true,
                'data' => $info
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء الحصول على معلومات الكاش: ' . $e->getMessage()
            ], 500);
        }
    }
}
