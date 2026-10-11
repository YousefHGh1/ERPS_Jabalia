<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('payment_engineerings', function (Blueprint $table) {
            $table->foreignId('revenue_batch_id')
                ->nullable()
                ->after('created_by')
                ->constrained('revenue_batches')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('payment_engineerings', function (Blueprint $table) {
            $table->dropForeign(['revenue_batch_id']);
            $table->dropColumn('revenue_batch_id');
        });
    }
};

