<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('defendants')) {
            return;
        }
        Schema::create('defendants', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('identity_number')->nullable();
            $table->string('name');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('defendants');
    }
};
