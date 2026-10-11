<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('request_workflows', function (Blueprint $table) {
            $table->foreignId('previous_workflow_id')->nullable()->after('request_type_id')->constrained('request_workflows')->onDelete('cascade');
            $table->foreignId('section_id')->nullable()->after('previous_workflow_id')->constrained('sections')->onDelete('restrict');
        });
    }

    public function down()
    {
        Schema::table('request_workflows', function (Blueprint $table) {
            $table->dropForeign(['previous_workflow_id']);
            $table->dropForeign(['section_id']);
            $table->dropColumn(['previous_workflow_id', 'section_id']);
        });
    }
};
