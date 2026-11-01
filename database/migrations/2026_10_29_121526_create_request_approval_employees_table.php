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
        Schema::create('request_approval_employees', function (Blueprint $table) {
            $table->foreignId('request_id')->constrained('requests')->onDelete('cascade');
            $table->foreignId('employee_id')->constrained('employees')->onDelete('cascade');
            $table->string('approval_type', 20)->default('primary')->comment('primary=أصلي, substitute=بديل');
            $table->text('substitution_reason')->nullable()->comment('سبب اختيار موظف بديل');
            $table->timestamps();
            
            $table->unique(['request_id', 'employee_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('request_approval_employees');
    }
};
