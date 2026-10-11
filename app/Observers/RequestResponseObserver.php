<?php

namespace App\Observers;

use App\Models\RequestResponse;
use App\Models\Request as RequestModel;
use App\Models\RequestStatus;

class RequestResponseObserver
{
    /**
     * Handle the RequestResponse "updated" event.
     */
    public function updated(RequestResponse $requestResponse): void
    {
        $this->updateRequestStatus($requestResponse);
    }

    /**
     * Handle the RequestResponse "created" event.
     */
    public function created(RequestResponse $requestResponse): void
    {
        // لا حاجة لتحديث الحالة عند الإنشاء لأنها ستكون "قيد الانتظار"
    }

    /**
     * تحديث حالة الطلب بناءً على الردود.
     * - عند رفض أو تعليق أي قسم: تُحدَّث حالة الطلب فوراً إلى مرفوض أو معلق (من ذلك القسم).
     * - مكتمل فقط عندما تكون جميع الردود موافقة (ولا يجوز إكمال الطلب إلا بذلك).
     */
    private function updateRequestStatus(RequestResponse $requestResponse): void
    {
        $request = RequestModel::with('responses')->find($requestResponse->request_id);
        
        if (!$request) {
            return;
        }

        $responses = $request->responses;
        
        // إذا رفض أي قسم → حالة الطلب = مرفوض فوراً
        if ($responses->where('response_type', 3)->count() > 0) {
            $statusName = 'مرفوض';
        }
        // إذا علق أي قسم → حالة الطلب = معلق فوراً
        elseif ($responses->where('response_type', 2)->count() > 0) {
            $statusName = 'معلق';
        }
        // إذا كان هناك ردود لا تزال قيد الانتظار → قيد الانتظار
        elseif ($responses->where('response_type', 4)->count() > 0) {
            $statusName = 'قيد الانتظار';
        }
        // مكتمل فقط عندما تكون جميع الردود موافقة (ولا يجوز إكمال الطلب إلا بذلك)
        elseif ($responses->where('response_type', 1)->count() > 0 &&
                $responses->where('response_type', '!=', 1)->count() == 0) {
            $statusName = 'مكتمل';
        }
        else {
            $statusName = 'قيد الانتظار';
        }
        
        $requestStatus = RequestStatus::where('name', $statusName)->first();
        
        if ($requestStatus && $request->status_id != $requestStatus->id) {
            $request->update(['status_id' => $requestStatus->id]);
        }
    }
}
