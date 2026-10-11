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
        Schema::table('engineerings', function (Blueprint $table) {
            $table->string('subdivision_type')->nullable()->default('غير مفرز')->after('subdivision_project');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('engineerings', function (Blueprint $table) {
            $table->dropColumn('subdivision_type');
        });
    }
};
