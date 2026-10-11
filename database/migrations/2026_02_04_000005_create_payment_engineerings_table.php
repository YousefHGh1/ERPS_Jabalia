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
        Schema::create('payment_engineerings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('engineering_id')->constrained('engineerings')->onDelete('cascade');
            $table->decimal('amount', 10, 2);
            $table->decimal('balance_after', 10, 2)->default(0);
            $table->date('payment_date');
            $table->string('payment_method', 50)->default('cash'); // cash, bank, check
            $table->string('receipt_number', 100)->nullable();
            $table->text('notes')->nullable();
            $table->string('status', 20)->default('active'); // active, cancelled
            $table->text('cancellation_reason')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
            $table->softDeletes();

            // فهرسة الأعمدة لتحسين الأداء
            $table->index('engineering_id');
            $table->index('payment_date');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_engineerings');
    }
};
