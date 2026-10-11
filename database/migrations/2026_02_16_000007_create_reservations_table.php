<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('reservations', function (Blueprint $table) {
            $table->id();

            // رقم الحجز الداخلي (نمط قلم الجمهور)
            $table->unsignedBigInteger('reservation_no')->nullable();

            // رقم الحجز النصي (نمط جباليا الحالي) مع فهرس لسهولة البحث
            $table->string('reservation_number')->unique();

            // التواريخ والأوقات الأساسية
            $table->date('reservation_date');
            $table->time('reservation_time');

            // حقول جباليا الحالية
            $table->string('court_name');
            $table->string('judge_name');
            $table->string('case_number');
            $table->integer('case_year');
            $table->text('description')->nullable();

            // حقول قلم الجمهور الإضافية
            $table->string('subject')->nullable();
            $table->string('area')->nullable();
            $table->string('voucher')->nullable();
            $table->string('widget')->nullable();
            $table->string('address')->nullable();
            $table->unsignedBigInteger('jebaia_no')->nullable();
            $table->string('license_file')->nullable();
            $table->text('contents')->nullable();

            // حالة الحجز
            $table->enum('status', ['pending', 'confirmed', 'cancelled', 'completed'])->default('pending');

            // المستخدمون
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('set null');

            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down()
    {
        Schema::dropIfExists('reservations');
    }
};
