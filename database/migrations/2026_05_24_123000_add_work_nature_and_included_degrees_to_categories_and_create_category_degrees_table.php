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
        Schema::table('categories', function (Blueprint $table) {
            if (! Schema::hasColumn('categories', 'work_nature')) {
                $table->string('work_nature')->nullable()->after('basic_salary');
            }
            if (! Schema::hasColumn('categories', 'included_degrees')) {
                $table->string('included_degrees', 500)->nullable()->after('work_nature');
            }
        });

        if (! Schema::hasTable('category_degrees')) {
            Schema::create('category_degrees', function (Blueprint $table) {
                $table->id();
                $table->foreignId('category_id')->constrained()->cascadeOnDelete();
                $table->string('degree');
                $table->decimal('basic_salary', 10, 2)->nullable();
                $table->unsignedSmallInteger('order')->nullable();
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('category_degrees');

        Schema::table('categories', function (Blueprint $table) {
            if (Schema::hasColumn('categories', 'included_degrees')) {
                $table->dropColumn('included_degrees');
            }
            if (Schema::hasColumn('categories', 'work_nature')) {
                $table->dropColumn('work_nature');
            }
        });
    }
};
