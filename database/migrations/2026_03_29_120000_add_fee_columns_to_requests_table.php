<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('requests', function (Blueprint $table) {
            $table->decimal('fee_amount', 12, 2)->nullable()->after('text');
            $table->decimal('paid_amount', 12, 2)->default(0)->after('fee_amount');
            $table->decimal('remaining_amount', 12, 2)->default(0)->after('paid_amount');
        });
    }

    public function down(): void
    {
        Schema::table('requests', function (Blueprint $table) {
            $table->dropColumn(['fee_amount', 'paid_amount', 'remaining_amount']);
        });
    }
};
