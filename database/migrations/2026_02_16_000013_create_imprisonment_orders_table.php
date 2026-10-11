<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('imprisonment_orders', function (Blueprint $table) {
            $table->id();

            // ربط أمر الحبس بإخطار (نمط قلم الجمهور)
            $table->unsignedBigInteger('notice_id')->nullable();

            // رقم أمر الحبس
            $table->string('order_number')->unique();

            // التاريخ الأساسي (تاريخ فقط) + إمكانية تخزين التاريخ/الوقت الكامل إن لزم
            $table->date('order_date');

            // بيانات المحكمة والخصوم (نمط جباليا)
            $table->string('court_name');
            $table->string('judge_name');
            $table->string('defendant_name');
            $table->string('defendant_identity');
            $table->string('imprisonment_period');
            $table->string('imprisonment_type');
            $table->string('case_number');
            $table->integer('case_year');

            // ملاحظات / وصف
            $table->text('description')->nullable();
            $table->text('notes')->nullable();

            // حالة أمر الحبس
            $table->enum('status', ['pending', 'active', 'completed', 'cancelled'])->default('pending');

            // المستخدمون
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('set null');
            $table->unsignedBigInteger('deleted_by')->nullable();

            $table->timestamps();
            $table->softDeletes();

            // مفتاح أجنبي اختياري للإخطار
            $table->foreign('notice_id')->references('id')->on('notices')->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::dropIfExists('imprisonment_orders');
    }
};
