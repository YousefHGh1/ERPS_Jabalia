<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('revenue_batches', function (Blueprint $table) {
            $table->id();
            $table->date('date')->unique();
            $table->enum('status', ['open', 'closed'])->default('open');

            $table->foreignId('opened_by_user_id')->constrained('users');
            $table->foreignId('closed_by_user_id')->nullable()->constrained('users');

            $table->dateTime('opened_at');
            $table->dateTime('closed_at')->nullable();

            $table->decimal('total_amount', 15, 2)->default(0);
            $table->unsignedInteger('payments_count')->default(0);

            $table->timestamps();

            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('revenue_batches');
    }
};

