<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('requests', function (Blueprint $table) {
            $table->id();
            $table->text('text');
            $table->foreignId('request_type_id')->constrained('request_types')->onDelete('cascade');
            $table->foreignId('status_id')->constrained('request_statuses')->onDelete('cascade');
            $table->foreignId('mokalaf_id')->nullable()->constrained('mokalafs')->onDelete('cascade');
            $table->date('deadline')->nullable();
            $table->boolean('is_urgent')->default(false);
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down()
    {
        Schema::dropIfExists('requests');
    }
};
