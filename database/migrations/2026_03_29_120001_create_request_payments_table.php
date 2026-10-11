<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('request_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('request_id')->constrained('requests')->onDelete('cascade');
            $table->decimal('amount', 10, 2);
            $table->decimal('balance_after', 10, 2)->default(0);
            $table->date('payment_date');
            $table->string('payment_method', 50)->default('cash');
            $table->string('receipt_number', 100)->nullable();
            $table->text('notes')->nullable();
            $table->string('status', 20)->default('active');
            $table->text('cancellation_reason')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('revenue_batch_id')->nullable()->constrained('revenue_batches')->nullOnDelete();
            $table->unsignedBigInteger('payment_number')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index('request_id');
            $table->index('payment_date');
            $table->index('status');
            $table->index('revenue_batch_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('request_payments');
    }
};
