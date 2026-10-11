<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('notices', function (Blueprint $table) {
            $table->id();

            // حقل السنة ورقم الإخطار الرقمي (نمط قلم الجمهور)
            $table->unsignedBigInteger('year')->nullable();
            $table->unsignedBigInteger('notice_id')->nullable();

            // رقم الإخطار النصي المستخدم في جباليا الحالية
            $table->string('notice_number')->unique();

            // التاريخ الأساسي (تاريخ فقط) + إمكانية تخزين التاريخ/الوقت الكامل إن لزم
            $table->date('notice_date');
            $table->timestamp('payment_date')->nullable();

            // أسباب الإخطار ومواقع الإيجار
            $table->foreignId('notice_reason_id')->constrained()->onDelete('cascade');
            $table->foreignId('rent_location_id')->nullable()->constrained()->onDelete('set null');

            // حقول إضافية من قلم الجمهور
            $table->unsignedBigInteger('jebaia_no')->nullable();
            $table->unsignedBigInteger('fine_amount')->nullable();
            $table->string('notice_address')->nullable();
            $table->string('notice_month')->nullable();
            $table->string('org_file_number')->nullable();
            $table->string('rent_location_name')->nullable();
            $table->unsignedBigInteger('rent_no')->nullable();
            $table->string('build_desc')->nullable();
            $table->unsignedBigInteger('floors_count')->nullable();
            $table->string('floors')->nullable();

            // الوصف النصي العام
            $table->text('description')->nullable();

            // حالة الإخطار
            $table->enum('status', ['draft', 'sent', 'delivered', 'cancelled'])->default('draft');

            // المستخدمون (من جباليا وقلم الجمهور)
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('set null');
            $table->unsignedBigInteger('deleted_by')->nullable();
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');

            $table->timestamps();
            $table->softDeletes();

            // فهرس مركّب لنمط قلم الجمهور (سنة + رقم)
            $table->unique(['year', 'notice_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('notices');
    }
};
