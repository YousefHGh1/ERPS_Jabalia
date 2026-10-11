<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('revenue_batches', function (Blueprint $table) {
            $table->string('batch_kind', 20)->default('unified')->after('sequence_number');
            $table->foreignId('parent_batch_id')->nullable()->after('batch_kind')->constrained('revenue_batches')->nullOnDelete();
            $table->foreignId('section_id')->nullable()->after('parent_batch_id')->constrained('sections')->nullOnDelete();
        });

        // إزالة تفرّد التاريخ: يمكن وجود عدة رزم لنفس اليوم (موحّدة + أقسام)
        Schema::table('revenue_batches', function (Blueprint $table) {
            $table->dropUnique(['date']);
        });

        DB::table('revenue_batches')->update([
            'batch_kind' => 'unified',
            'parent_batch_id' => null,
        ]);

        Schema::table('revenue_batches', function (Blueprint $table) {
            $table->index(['date', 'batch_kind']);
            $table->index(['parent_batch_id', 'section_id']);
        });
    }

    public function down(): void
    {
        Schema::table('revenue_batches', function (Blueprint $table) {
            $table->dropIndex(['date', 'batch_kind']);
            $table->dropIndex(['parent_batch_id', 'section_id']);
        });

        Schema::table('revenue_batches', function (Blueprint $table) {
            $table->dropForeign(['parent_batch_id']);
            $table->dropForeign(['section_id']);
            $table->dropColumn(['batch_kind', 'parent_batch_id', 'section_id']);
        });

        Schema::table('revenue_batches', function (Blueprint $table) {
            $table->unique('date');
        });
    }
};
