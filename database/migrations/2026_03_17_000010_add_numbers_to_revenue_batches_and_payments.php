<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('revenue_batches', function (Blueprint $table) {
            $table->unsignedBigInteger('sequence_number')
                ->after('id')
                ->nullable()
                ->index();
        });

        Schema::table('payments', function (Blueprint $table) {
            $table->unsignedBigInteger('payment_number')
                ->after('id')
                ->nullable()
                ->index();
        });

        Schema::table('payment_engineerings', function (Blueprint $table) {
            $table->unsignedBigInteger('payment_number')
                ->after('id')
                ->nullable()
                ->index();
        });
    }

    public function down(): void
    {
        Schema::table('payment_engineerings', function (Blueprint $table) {
            $table->dropColumn('payment_number');
        });

        Schema::table('payments', function (Blueprint $table) {
            $table->dropColumn('payment_number');
        });

        Schema::table('revenue_batches', function (Blueprint $table) {
            $table->dropColumn('sequence_number');
        });
    }
};

