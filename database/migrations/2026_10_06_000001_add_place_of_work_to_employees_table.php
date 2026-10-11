<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * إضافة حقل مكان العمل إلى جدول الموظفين.
     */
    public function up(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            $table->string('place_of_work', 255)->nullable()->after('branch_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            $table->dropColumn('place_of_work');
        });
    }
};
