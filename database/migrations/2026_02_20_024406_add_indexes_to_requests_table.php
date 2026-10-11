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
        Schema::table('requests', function (Blueprint $table) {
            // إضافة indexes للجداول الرئيسية
            if (!$this->hasIndex('requests', 'requests_request_type_id_index')) {
                $table->index('request_type_id');
            }
            
            if (!$this->hasIndex('requests', 'requests_status_id_index')) {
                $table->index('status_id');
            }
            
            if (!$this->hasIndex('requests', 'requests_mokalaf_id_index')) {
                $table->index('mokalaf_id');
            }
            
            if (!$this->hasIndex('requests', 'requests_created_by_index')) {
                $table->index('created_by');
            }
            
            if (!$this->hasIndex('requests', 'requests_created_at_index')) {
                $table->index('created_at');
            }
            
            if (!$this->hasIndex('requests', 'requests_is_urgent_index')) {
                $table->index('is_urgent');
            }
            
            // Composite indexes للاستعلامات الشائعة
            if (!$this->hasIndex('requests', 'requests_request_type_status_index')) {
                $table->index(['request_type_id', 'status_id']);
            }
            
            if (!$this->hasIndex('requests', 'requests_status_urgent_index')) {
                $table->index(['status_id', 'is_urgent']);
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('requests', function (Blueprint $table) {
            $table->dropIndex(['request_type_id']);
            $table->dropIndex(['status_id']);
            $table->dropIndex(['mokalaf_id']);
            $table->dropIndex(['created_by']);
            $table->dropIndex(['created_at']);
            $table->dropIndex(['is_urgent']);
            $table->dropIndex(['request_type_id', 'status_id']);
            $table->dropIndex(['status_id', 'is_urgent']);
        });
    }

    /**
     * التحقق من وجود index
     */
    private function hasIndex(string $table, string $indexName): bool
    {
        $connection = Schema::getConnection();
        $database = $connection->getDatabaseName();
        
        $indexes = $connection->select("
            SELECT INDEX_NAME 
            FROM information_schema.STATISTICS 
            WHERE TABLE_SCHEMA = ? 
            AND TABLE_NAME = ? 
            AND INDEX_NAME = ?
        ", [$database, $table, $indexName]);
        
        return !empty($indexes);
    }
};
