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
        Schema::create('craft_licenses', function (Blueprint $table) {
            $table->id();
            $table->string('file_number', 6)->unique();
            $table->string('tax_number')->nullable();
            $table->string('status'); 
            $table->string('last_license')->nullable();
            $table->string('id_number')->nullable();
            $table->string('phone_number')->nullable();
            $table->string('owner_name')->nullable();
            $table->string('property_owner')->nullable();
            $table->string('property_owner_id')->nullable();
            $table->string('ownership_entity')->nullable();
            $table->string('first_license_year')->nullable();
            $table->string('classification'); 
            $table->string('craft_type');
            $table->string('detailed_craft_type')->nullable();
            $table->date('registration_date');
            $table->date('last_payment_date')->nullable();
            $table->string('holiday_days')->nullable();
            $table->string('licensed_operator')->nullable();
            $table->string('is_licensed')->nullable();
            $table->string('commercial_name')->nullable();
            $table->string('neighborhood')->nullable();
            $table->string('main_street')->nullable();
            $table->string('address')->nullable();
            $table->string('nearest_mosque')->nullable();
            $table->string('gis_building_number')->nullable();
            $table->string('street_number')->nullable();
            $table->string('floor_number')->nullable();
            $table->string('unit_number')->nullable();
            $table->decimal('total_debts', 12, 2)->default(0);
            $table->decimal('craft_fees', 12, 2)->default(0);
            $table->decimal('payment_amount', 12, 2)->default(0);
            $table->decimal('signboard_fees', 12, 2)->default(0);
            $table->decimal('remaining', 12, 2)->default(0);
            $table->string('remaining_years')->nullable();
            $table->string('advertisement_board')->nullable();
            $table->integer('number_of_stores')->nullable();
            $table->string('board_in_meters')->nullable();
            $table->decimal('final_price', 12, 2)->default(0);
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('craft_licenses');
    }
};
