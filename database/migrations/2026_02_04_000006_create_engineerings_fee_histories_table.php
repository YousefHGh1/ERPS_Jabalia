<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('engineerings_fee_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('engineering_id')->constrained('engineerings')->onDelete('cascade');
            $table->decimal('fee_amount', 10, 2);
            $table->string('operation_type'); // إضافة / تعديل / تجديد
            $table->timestamp('action_date');
            $table->timestamps();

            // فهرسة الأعمدة لتحسين الأداء
            $table->index('engineering_id');
            $table->index('action_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('engineerings_fee_histories');
    }
};
