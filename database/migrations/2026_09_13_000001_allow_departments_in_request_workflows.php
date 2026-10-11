<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('request_workflows', function (Blueprint $table) {
            $table->dropForeign(['section_id']);
        });

        Schema::table('request_workflows', function (Blueprint $table) {
            $table->foreignId('section_id')->nullable()->change();
            $table->foreignId('department_id')->nullable()->after('section_id')->constrained()->onDelete('restrict');
        });

        Schema::table('request_workflows', function (Blueprint $table) {
            $table->foreign('section_id')->references('id')->on('sections')->onDelete('restrict');
        });
    }

    public function down()
    {
        Schema::table('request_workflows', function (Blueprint $table) {
            $table->dropForeign(['department_id']);
            $table->dropColumn('department_id');
            $table->dropForeign(['section_id']);
        });

        Schema::table('request_workflows', function (Blueprint $table) {
            $table->foreignId('section_id')->nullable(false)->change();
        });

        Schema::table('request_workflows', function (Blueprint $table) {
            $table->foreign('section_id')->references('id')->on('sections')->onDelete('restrict');
        });
    }
};