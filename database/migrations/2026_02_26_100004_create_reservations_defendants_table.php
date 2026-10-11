<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('reservations_defendants')) {
            return;
        }
        Schema::create('reservations_defendants', function (Blueprint $table) {
            $table->unsignedBigInteger('reservation_id');
            $table->unsignedBigInteger('defendant_id');
            $table->primary(['reservation_id', 'defendant_id']);
            $table->foreign('reservation_id')->references('id')->on('reservations')->onDelete('cascade');
            $table->foreign('defendant_id')->references('id')->on('defendants')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reservations_defendants');
    }
};
