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
        Schema::create('jebaya_invoices', function (Blueprint $table) {
            $table->id();
            $table->string('agreement_id')->nullable();
            $table->string('voucher_no')->nullable();
            $table->date('voucher_date')->nullable();
            $table->decimal('voucher_amt', 15, 2)->default(0);
            $table->decimal('net_amt', 15, 2)->default(0);
            $table->decimal('total_late', 15, 2)->default(0);
            $table->date('last_due_date')->nullable();
            $table->decimal('current_voucher_amt', 15, 2)->default(0);
            $table->decimal('auto_discount_ratio', 10, 2)->default(0);
            $table->string('voucher_month')->nullable();
            $table->string('mobile_no')->nullable();
            $table->string('arabic_name')->nullable();
            $table->timestamps();

            $table->index('agreement_id');
            $table->index('voucher_no');
            $table->index('mobile_no');
            $table->index('arabic_name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('jebaya_invoices');
    }
};
