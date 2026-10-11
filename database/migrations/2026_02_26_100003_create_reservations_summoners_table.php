<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('reservations_summoners')) {
            return;
        }
        Schema::create('reservations_summoners', function (Blueprint $table) {
            $table->unsignedBigInteger('reservation_id');
            $table->unsignedBigInteger('summoner_id');
            $table->primary(['reservation_id', 'summoner_id']);
            $table->foreign('reservation_id')->references('id')->on('reservations')->onDelete('cascade');
            $table->foreign('summoner_id')->references('id')->on('summoners')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reservations_summoners');
    }
};
