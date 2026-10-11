<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * جدول الملفات التاريخية (هندسة وتنظيم) - مستورد من Oracle ENG.MASTER
     * Run the migrations.
     */
    public function up(): void
    {
        // اسم الجدول MASTER ليتوافق مع ملف master2026_mysql.sql عند استيراد البيانات
        Schema::create('MASTER', function (Blueprint $table) {
            $table->integer('ID')->primary();
            $table->string('FILE_NO', 50)->nullable();
            $table->string('FIRST_NAME', 100)->nullable();
            $table->string('FATHER_NAME', 100)->nullable();
            $table->string('GRAND_NAME', 100)->nullable();
            $table->string('FAMILY_NAME', 100)->nullable();
            $table->string('COUPON', 50)->nullable();
            $table->string('ADDRESS', 500)->nullable();
            $table->string('PIECE', 50)->nullable();
            $table->string('AREA', 50)->nullable();
            $table->string('DIVIDER', 50)->nullable();
            $table->date('SETTING_DATE')->nullable();
            $table->date('REG_DATE')->nullable();
            $table->string('BUILD_NO', 50)->nullable();
            $table->string('FLOOR_NO', 50)->nullable();
            $table->string('PART_NO', 50)->nullable();
            $table->text('NOTE')->nullable();
            $table->string('STREET', 200)->nullable();
            $table->string('USER_NAME_INSERT', 100)->nullable();
            $table->string('USER_DATE_INSERT', 50)->nullable();
            $table->string('USER_TIME_INSERT', 50)->nullable();
            $table->string('IS_UPDATE', 20)->nullable();
            $table->string('GIS_FILE_NO', 50)->nullable();
            $table->string('HAY_NO', 50)->nullable();
            $table->integer('AGREMENT_NO_1')->nullable();
            $table->integer('AGREMENT_NO_2')->nullable();
            $table->integer('AGREMENT_NO_3')->nullable();
            $table->string('FINE_TYPE', 200)->nullable();
            $table->string('DEPT_REPLAY', 200)->nullable();
            $table->string('IS_TRUE', 200)->nullable();
            $table->string('JWAL_NO', 50)->nullable();
            $table->string('TEL_NO', 50)->nullable();
            $table->unsignedBigInteger('ID_NO')->nullable();
            $table->string('FULL_NAME', 500)->nullable();
            $table->string('FILE_STATUSE', 100)->nullable();
            $table->decimal('TOTAL_AMT', 15, 2)->nullable();
            $table->date('LAST_PAY_DATE')->nullable();
            $table->string('FILE_NO_VAL', 50)->nullable();
            $table->decimal('LAST_UPDATE_AMT', 15, 2)->nullable();
            $table->integer('ISDELETE')->default(0);
            $table->string('S_STATUSE', 100)->nullable();
            $table->string('CLASS_STATUSE', 100)->nullable();
            $table->integer('INSERT_USERID')->nullable();
            $table->integer('UPDATE_USERID')->nullable();
            $table->integer('DELETE_USERID')->nullable();
            $table->date('INSERT_DATE')->nullable();
            $table->date('UPDATE_DATE')->nullable();
            $table->date('DELETE_DATE')->nullable();
            $table->string('FIRST_NAME_F', 100)->nullable();
            $table->string('FATHER_NAME_F', 100)->nullable();
            $table->string('GRAND_NAME_F', 100)->nullable();
            $table->string('FAMILY_NAME_F', 100)->nullable();
            $table->string('FULL_NAME_F', 500)->nullable();
            $table->text('NOTE_F')->nullable();
            $table->integer('LOG_ID')->nullable();
            $table->integer('LOG_MODE')->nullable();
            $table->string('ADDRESS_F', 500)->nullable();
            $table->decimal('REMAINDER', 15, 2)->nullable();
            $table->integer('FILE_YEAR')->nullable();
            $table->string('FILE_NO_LONG', 50)->nullable();
            $table->string('OTHER_OWNER', 500)->nullable();
            $table->index('FILE_NO');
            $table->index('FULL_NAME');
            $table->index('FILE_STATUSE');
            $table->index('ID_NO');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('MASTER');
    }
};
