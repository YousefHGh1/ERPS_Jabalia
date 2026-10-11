<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('craft_license_attachments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('craft_license_id')->constrained('craft_licenses')->onDelete('cascade');
            $table->string('file_name'); // الاسم الأصلي للملف
            $table->string('file_path'); // المسار مع رقم الملف + اسم الملف
            $table->bigInteger('file_size')->nullable();
            $table->string('mime_type')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('craft_license_attachments');
    }
};

