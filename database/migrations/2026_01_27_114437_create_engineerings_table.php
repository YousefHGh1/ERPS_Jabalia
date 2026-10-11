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
        Schema::create('engineerings', function (Blueprint $table) {
            $table->id();
            // File owner info
            $table->string('file_number')->nullable();
            $table->string('first_name')->nullable();
            $table->string('father_name')->nullable();
            $table->string('grandfather_name')->nullable();
            $table->string('family_name')->nullable();
            $table->string('national_id', 20)->nullable();
            $table->year('file_year')->nullable();

            // Land info
            $table->integer('plot_number')->nullable();
            $table->integer('parcel_number')->nullable();
            $table->integer('division_number')->nullable();
            $table->decimal('area_size', 10, 2)->nullable();
            $table->string('neighborhood')->nullable();
            $table->text('address')->nullable();

            // Building info
            $table->string('building_type')->nullable();
            $table->json('floors_count')->nullable();
            $table->string('subdivision_project')->nullable();

            // Financials
            $table->decimal('gis_fees', 10, 2)->default(0);
            $table->decimal('license_fees', 10, 2)->default(0);
            $table->decimal('total_amount', 10, 2)->default(0);
            $table->decimal('paid_amount', 10, 2)->default(0);
            $table->decimal('remaining_amount', 10, 2)->default(0);

            // Dates
            $table->date('application_date')->nullable();
            $table->date('session_date')->nullable();
            $table->date('file_registered_at')->nullable();
            $table->date('last_payment_date')->nullable();

            // Contact
            $table->string('phone_number', 20)->nullable();
            $table->string('mobile_number', 20)->nullable();

            // Others
            $table->text('notes')->nullable();
            $table->integer('created_by')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('engineerings');
    }
};
