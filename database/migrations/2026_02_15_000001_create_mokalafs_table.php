<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('mokalafs', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // اسم المكلف
            $table->string('identity_number')->nullable(); // هوية
            $table->string('license_number')->nullable(); // معرف | مشتغل مرخص
            $table->text('address')->nullable(); // العنوان
            $table->string('mobile')->nullable(); // رقم الجوال
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down()
    {
        Schema::dropIfExists('mokalafs');
    }
};
