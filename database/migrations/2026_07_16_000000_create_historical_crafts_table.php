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
        Schema::create('historical_crafts', function (Blueprint $table) {
            $table->id();
            $table->json('data')->nullable();

            $table->unsignedBigInteger('file_no')->nullable()->index();
            $table->string('first_name')->nullable();
            $table->string('father_name')->nullable();
            $table->string('grand_name')->nullable();
            $table->string('family_name')->nullable();
            $table->string('address')->nullable();
            $table->double('oner')->nullable();
            $table->dateTime('reg_date')->nullable();
            $table->text('note')->nullable();
            $table->string('craft_street_no')->nullable();
            $table->string('user_name_insert')->nullable();
            $table->string('user_date_insert')->nullable();
            $table->string('user_time_insert')->nullable();
            $table->string('craft_hay_no')->nullable();
            $table->unsignedBigInteger('system_no')->nullable();
            $table->string('jwal_no')->nullable();
            $table->integer('first_license_year')->nullable();
            $table->unsignedBigInteger('id_card')->nullable()->index();
            $table->string('full_name')->nullable();
            $table->string('craft_status')->nullable();
            $table->double('balance')->nullable();
            $table->string('last_pay_date')->nullable();
            $table->string('shared_name')->nullable();
            $table->string('mosque_no')->nullable();
            $table->integer('class_no')->nullable();
            $table->string('craft_type_no')->nullable();
            $table->string('vacation_day')->nullable();
            $table->unsignedBigInteger('trade_number')->nullable();
            $table->double('last_license_year_no')->nullable();
            $table->double('debt_2017')->nullable();
            $table->integer('units_count')->nullable();
            $table->integer('banner_meters')->nullable();
            $table->double('banner_price')->nullable();
            $table->unsignedBigInteger('owner_id_card')->nullable();
            $table->string('owner_first_name')->nullable();
            $table->string('owner_father_name')->nullable();
            $table->double('tax_2017')->nullable();
            $table->double('unit_number')->nullable();
            $table->string('owner_grand_name')->nullable();
            $table->string('owner_family_name')->nullable();
            $table->double('building_id')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->dateTime('creation_date')->nullable();
            $table->integer('file_status')->nullable();
            $table->dateTime('last_license_year')->nullable();
            $table->text('detailed_type')->nullable();
            $table->double('current_year_tax')->nullable();
            $table->string('trade_mark_name')->nullable();
            $table->string('owner_full_name')->nullable();
            $table->double('last_editor')->nullable();
            $table->dateTime('last_edit_date')->nullable();
            $table->unsignedBigInteger('ownership_party_id')->nullable();
            $table->double('first_licence_year')->nullable();
            $table->text('it_notes')->nullable();
            $table->double('street_number')->nullable();
            $table->string('building_number')->nullable();
            $table->double('floor_number')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('historical_crafts');
    }
};
