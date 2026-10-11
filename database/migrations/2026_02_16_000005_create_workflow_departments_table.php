<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('workflow_departments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('workflow_id')->constrained('request_workflows')->onDelete('cascade');
            $table->foreignId('department_id')->constrained()->onDelete('cascade');
            $table->timestamps();
            
            $table->unique(['workflow_id', 'department_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('workflow_departments');
    }
};
