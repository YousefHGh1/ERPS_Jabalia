<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('craft_licenses', function (Blueprint $table) {
            $table->decimal('discount_percent', 5, 2)->default(0)->after('signboard_fees');
        });
    }

    public function down(): void
    {
        Schema::table('craft_licenses', function (Blueprint $table) {
            $table->dropColumn('discount_percent');
        });
    }
};

