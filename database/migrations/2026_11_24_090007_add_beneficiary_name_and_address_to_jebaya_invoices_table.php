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
        Schema::table('jebaya_invoices', function (Blueprint $table) {
            $table->string('beneficiary_name')->nullable()->after('arabic_name');
            $table->string('address')->nullable()->after('beneficiary_name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('jebaya_invoices', function (Blueprint $table) {
            $table->dropColumn(['beneficiary_name', 'address']);
        });
    }
};
