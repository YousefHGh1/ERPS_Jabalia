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
        Schema::table('external_collections', function (Blueprint $table) {
            $table->string('payment_status', 50)->default('pending')->after('notes');
            $table->decimal('paid_amount', 15, 2)->default(0)->after('payment_status');
            $table->decimal('remaining_amount', 15, 2)->storedAs('amount - paid_amount')->after('paid_amount');
            $table->timestamp('last_payment_date')->nullable()->after('remaining_amount');
            
            $table->index('payment_status');
            $table->index('paid_amount');
            $table->index('remaining_amount');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('external_collections', function (Blueprint $table) {
            $table->dropIndex(['payment_status']);
            $table->dropIndex(['paid_amount']);
            $table->dropIndex(['remaining_amount']);
            $table->dropColumn(['payment_status', 'paid_amount', 'remaining_amount', 'last_payment_date']);
        });
    }
};
