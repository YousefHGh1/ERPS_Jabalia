<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('employee_advances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained('employees');
            $table->decimal('amount', 10, 2);
            $table->text('reason');
            $table->date('request_date');
            $table->date('expected_repayment_date')->nullable();
            $table->string('status')->default('pending'); // pending, section_approved, mayor_approved, finance_approved, rejected

            // Approval tracking
            $table->foreignId('section_director_id')->nullable()->constrained('employees');
            $table->timestamp('section_approved_at')->nullable();
            $table->text('section_notes')->nullable();

            $table->foreignId('mayor_id')->nullable()->constrained('employees');
            $table->timestamp('mayor_approved_at')->nullable();
            $table->text('mayor_notes')->nullable();

            $table->foreignId('finance_officer_id')->nullable()->constrained('employees');
            $table->timestamp('finance_approved_at')->nullable();
            $table->text('finance_notes')->nullable();

            $table->text('rejection_reason')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users');
            $table->foreignId('updated_by')->nullable()->constrained('users');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('employee_advances');
    }
};
