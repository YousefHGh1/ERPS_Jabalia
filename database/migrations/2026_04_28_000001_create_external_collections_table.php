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
        Schema::create('external_collections', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mokalaf_id')->nullable()->constrained('mokalafs')->nullOnDelete();
            $table->string('taxpayer_name')->nullable();
            $table->string('identity_number')->nullable();
            $table->foreignId('section_id')->constrained('sections')->cascadeOnDelete();
            $table->decimal('amount', 15, 2);
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();

            $table->index('identity_number');
            $table->index('section_id');
            $table->index('amount');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('external_collections');
    }
};
