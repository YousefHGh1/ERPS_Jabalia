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
        Schema::table('engineerings', function (Blueprint $table) {
            if (!Schema::hasColumn('engineerings', 'penalties')) {
                $table->decimal('penalties', 10, 2)->default(0)->after('gis_fees');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('engineerings', function (Blueprint $table) {
            if (Schema::hasColumn('engineerings', 'penalties')) {
                $table->dropColumn('penalties');
            }
        });
    }
};

