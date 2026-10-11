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
        Schema::table('z_k_data', function (Blueprint $table) {
            $table->index('uid');
            $table->index('emp_id');
            $table->index('arrival_time');
            $table->index('late_minutes');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('z_k_data', function (Blueprint $table) {
            $table->dropIndex(['uid']);
            $table->dropIndex(['emp_id']);
            $table->dropIndex(['arrival_time']);
            $table->dropIndex(['late_minutes']);
        });
    }
};
