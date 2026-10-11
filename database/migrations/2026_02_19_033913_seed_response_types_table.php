<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     * إدراج أنواع الردود المطلوبة لسير عمل الطلبات (إن لم تكن موجودة).
     */
    public function up(): void
    {
        $now = now();
        // استخدام null لأن جدول users قد لا يكون مُعبّأ عند migrate:fresh
        $types = [
            ['id' => 1, 'name' => 'مقبول', 'created_by' => null, 'created_at' => $now, 'updated_at' => $now],
            ['id' => 2, 'name' => 'معلق', 'created_by' => null, 'created_at' => $now, 'updated_at' => $now],
            ['id' => 3, 'name' => 'مرفوض', 'created_by' => null, 'created_at' => $now, 'updated_at' => $now],
            ['id' => 4, 'name' => 'قيد الانتظار', 'created_by' => null, 'created_at' => $now, 'updated_at' => $now],
        ];

        foreach ($types as $row) {
            if (DB::table('response_types')->where('id', $row['id'])->doesntExist()) {
                DB::table('response_types')->insert($row);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('response_types')->whereIn('id', [1, 2, 3, 4])->delete();
    }
};
