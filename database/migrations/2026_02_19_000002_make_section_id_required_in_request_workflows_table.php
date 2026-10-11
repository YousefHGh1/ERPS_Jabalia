<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        // أولاً، حذف السجلات التي لا تحتوي على section_id
        DB::table('request_workflows')->whereNull('section_id')->delete();
        
        // حذف الـ foreign key constraint القديم
        Schema::table('request_workflows', function (Blueprint $table) {
            $table->dropForeign(['section_id']);
        });
        
        // تحديث العمود ليكون required
        Schema::table('request_workflows', function (Blueprint $table) {
            $table->foreignId('section_id')->nullable(false)->change();
        });
        
        // إعادة إضافة الـ foreign key constraint مع onDelete('restrict') بدلاً من 'set null'
        Schema::table('request_workflows', function (Blueprint $table) {
            $table->foreign('section_id')->references('id')->on('sections')->onDelete('restrict');
        });
    }

    public function down()
    {
        // حذف الـ foreign key constraint
        Schema::table('request_workflows', function (Blueprint $table) {
            $table->dropForeign(['section_id']);
        });
        
        // إعادة العمود ليكون nullable
        Schema::table('request_workflows', function (Blueprint $table) {
            $table->foreignId('section_id')->nullable()->change();
        });
        
        // إعادة إضافة الـ foreign key constraint مع onDelete('set null')
        Schema::table('request_workflows', function (Blueprint $table) {
            $table->foreign('section_id')->references('id')->on('sections')->onDelete('set null');
        });
    }
};
