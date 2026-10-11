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
        Schema::table('payments', function (Blueprint $table) {
            $table->string('payment_method')->default('cash')->after('amount'); // cash, bank, check
            $table->string('reference_number')->nullable()->after('payment_method');
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null')->after('reference_number');
            $table->decimal('balance_after', 15, 2)->nullable()->after('amount');
            $table->enum('status', ['active', 'cancelled'])->default('active')->after('notes');
            $table->text('cancellation_reason')->nullable()->after('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropColumn(['payment_method', 'reference_number', 'user_id', 'balance_after', 'status', 'cancellation_reason']);
        });
    }
};
