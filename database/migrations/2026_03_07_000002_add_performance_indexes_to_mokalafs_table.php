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
        Schema::table('mokalafs', function (Blueprint $table) {
            // Single indexes للحقول المستخدمة في البحث والفلترة
            if (!$this->hasIndex('mokalafs', 'mokalafs_identity_number_index')) {
                $table->index('identity_number');
            }
            
            if (!$this->hasIndex('mokalafs', 'mokalafs_license_number_index')) {
                $table->index('license_number');
            }
            
            if (!$this->hasIndex('mokalafs', 'mokalafs_mobile_index')) {
                $table->index('mobile');
            }
            
            if (!$this->hasIndex('mokalafs', 'mokalafs_created_by_index')) {
                $table->index('created_by');
            }
            
            // Index للبحث بالاسم
            if (!$this->hasIndex('mokalafs', 'mokalafs_name_index')) {
                $table->index('name');
            }
            
            // Composite indexes للاستعلامات الشائعة
            if (!$this->hasIndex('mokalafs', 'mokalafs_name_identity_index')) {
                $table->index(['name', 'identity_number']);
            }
            
            if (!$this->hasIndex('mokalafs', 'mokalafs_created_at_index')) {
                $table->index('created_at');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('mokalafs', function (Blueprint $table) {
            $table->dropIndex(['identity_number']);
            $table->dropIndex(['license_number']);
            $table->dropIndex(['mobile']);
            $table->dropIndex(['created_by']);
            $table->dropIndex(['name']);
            $table->dropIndex(['name', 'identity_number']);
            $table->dropIndex(['created_at']);
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
