<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('craft_license_partners', function (Blueprint $table) {
            $table->id();
            $table->foreignId('craft_license_id')->constrained('craft_licenses')->onDelete('cascade');
            $table->string('identity_number', 20);
            $table->string('name')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('craft_license_partners');
    }
};

