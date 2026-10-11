<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('craft_license_partners', function (Blueprint $table) {
            $table->string('status')->default('active')->after('name');
            $table->string('cancellation_reason')->nullable()->after('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('craft_license_partners', function (Blueprint $table) {
            $table->dropColumn(['status', 'cancellation_reason']);
        });
    }
};

