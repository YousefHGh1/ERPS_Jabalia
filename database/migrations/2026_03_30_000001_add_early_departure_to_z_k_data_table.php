<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('z_k_data', function (Blueprint $table) {
            if (!Schema::hasColumn('z_k_data', 'early_departure')) {
                $table->integer('early_departure')->default(0)->after('late_minutes');
                $table->index('early_departure');
            }
        });
    }

    public function down(): void
    {
        Schema::table('z_k_data', function (Blueprint $table) {
            if (Schema::hasColumn('z_k_data', 'early_departure')) {
                $table->dropIndex(['early_departure']);
                $table->dropColumn('early_departure');
            }
        });
    }
};

