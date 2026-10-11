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
        Schema::create('external_collection_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('external_collection_id')->constrained('external_collections')->cascadeOnDelete();
            $table->decimal('amount', 15, 2);
            $table->decimal('balance_after', 15, 2)->nullable();
            $table->date('payment_date');
            $table->string('payment_method', 50);
            $table->string('receipt_number', 100)->nullable();
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();
            $table->foreignId('revenue_batch_id')->nullable()->constrained('revenue_batches')->nullOnDelete();
            $table->string('status', 50)->default('paid');
            $table->text('cancellation_reason')->nullable();
            $table->string('payment_number', 50)->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index('external_collection_id');
            $table->index('payment_date');
            $table->index('payment_method');
            $table->index('status');
            $table->index('revenue_batch_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('external_collection_payments');
    }
};
