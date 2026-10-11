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
        Schema::create('request_responses', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('response_type'); // 1=موافقة, 2=معلق, 3=مرفوض, 4=قيد الانتظار
            $table->unsignedBigInteger('request_id');
            $table->unsignedBigInteger('workflow_id'); // ربط بجدول request_workflows
            $table->unsignedBigInteger('section_id');
            $table->text('notes')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('responsed_by')->nullable();
            $table->timestamp('responsed_at')->nullable();
            $table->string('ip_address')->nullable();
            $table->string('host')->nullable();
            $table->timestamps();
            $table->softDeletes();
            
            $table->foreign('response_type')->references('id')->on('response_types')->onDelete('restrict');
            $table->foreign('request_id')->references('id')->on('requests')->onDelete('cascade');
            $table->foreign('workflow_id')->references('id')->on('request_workflows')->onDelete('cascade');
            $table->foreign('section_id')->references('id')->on('sections')->onDelete('restrict');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
            $table->foreign('responsed_by')->references('id')->on('users')->onDelete('set null');
            
            $table->index(['request_id', 'response_type']);
            $table->index(['section_id', 'response_type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('request_responses');
    }
};
