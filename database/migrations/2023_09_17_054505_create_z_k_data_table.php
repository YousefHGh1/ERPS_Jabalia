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
        Schema::create('z_k_data', function (Blueprint $table) {
            $table->id();
            $table->integer('uid');
            $table->integer('emp_id');
            $table->integer('state');
            $table->timestamp('arrival_time');
            $table->integer('type');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('z_k_data');
    }
};
