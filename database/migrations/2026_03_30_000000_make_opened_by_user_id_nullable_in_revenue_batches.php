<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('revenue_batches', function (Blueprint $table) {
            // 先删除外键约束
            $table->dropForeign(['opened_by_user_id']);
            // 修改字段为可空
            $table->foreignId('opened_by_user_id')->nullable()->change();
            // 重新添加外键约束
            $table->foreign('opened_by_user_id')->references('id')->on('users')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('revenue_batches', function (Blueprint $table) {
            // 先删除外键约束
            $table->dropForeign(['opened_by_user_id']);
            // 修改字段为不可空
            $table->foreignId('opened_by_user_id')->nullable(false)->change();
            // 重新添加外键约束
            $table->foreign('opened_by_user_id')->references('id')->on('users');
        });
    }
};
