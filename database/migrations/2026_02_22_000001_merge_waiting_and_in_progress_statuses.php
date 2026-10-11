<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * دمج حالتَي "قيد الانتظار" و"قيد التنفيذ" في request_statuses إلى حالة واحدة
     */
    public function up(): void
    {
        // الحصول على ID حالة "قيد الانتظار"
        $waitingStatus = DB::table('request_statuses')
            ->where('name', 'قيد الانتظار')
            ->first();

        // الحصول على ID حالة "قيد التنفيذ"
        $inProgressStatus = DB::table('request_statuses')
            ->where('name', 'قيد التنفيذ')
            ->first();

        if (!$waitingStatus || !$inProgressStatus) {
            return;
        }

        // تحديث جميع الطلبات التي حالتها "قيد التنفيذ" إلى "قيد الانتظار"
        DB::table('requests')
            ->where('status_id', $inProgressStatus->id)
            ->update(['status_id' => $waitingStatus->id]);

        // حذف حالة "قيد التنفيذ" من الجدول
        DB::table('request_statuses')
            ->where('id', $inProgressStatus->id)
            ->delete();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // إعادة إنشاء حالة "قيد التنفيذ" (لا يمكن استعادة البيانات السابقة بدقة)
        DB::table('request_statuses')->insert([
            'name' => 'قيد التنفيذ',
            'color' => 'info',
            'description' => 'الطلب قيد التنفيذ',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
};
