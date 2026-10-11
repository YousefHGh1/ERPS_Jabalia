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
        Schema::create('craft_fee_histories', function (Blueprint $table) {
            $table->id();
            $table->string('file_number');
            $table->string('craft_type');
            $table->decimal('fee_amount', 10, 2);
            $table->string('operation_type'); // إضافة / تعديل / تجديد
            $table->timestamp('action_date');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('craft_fee_histories');
    }
};
