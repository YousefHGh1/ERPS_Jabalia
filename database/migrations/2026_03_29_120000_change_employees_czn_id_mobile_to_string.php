<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * تحويل رقم الهوية والجوال إلى string لسلامة البيانات (أصفار بادئة، أرقام طويلة).
     */
    public function up(): void
    {
        $driver = Schema::getConnection()->getDriverName();

        if ($driver === 'mysql' || $driver === 'mariadb') {
            DB::statement('ALTER TABLE employees MODIFY czn_id VARCHAR(20) NOT NULL');
            DB::statement('ALTER TABLE employees MODIFY mobile VARCHAR(20) NOT NULL');

            return;
        }

        if ($driver === 'pgsql') {
            DB::statement('ALTER TABLE employees ALTER COLUMN czn_id TYPE VARCHAR(20) USING czn_id::text');
            DB::statement('ALTER TABLE employees ALTER COLUMN mobile TYPE VARCHAR(20) USING mobile::text');

            return;
        }

        // sqlite: لا يدعم MODIFY بسهولة؛ إن لزم تثبيت doctrine/dbal أو ترحيل يدوي لبيئات الاختبار
    }

    public function down(): void
    {
        $driver = Schema::getConnection()->getDriverName();

        if ($driver === 'mysql' || $driver === 'mariadb') {
            DB::statement('ALTER TABLE employees MODIFY czn_id INT NOT NULL');
            DB::statement('ALTER TABLE employees MODIFY mobile INT NOT NULL');

            return;
        }

        if ($driver === 'pgsql') {
            DB::statement('ALTER TABLE employees ALTER COLUMN czn_id TYPE INTEGER USING czn_id::integer');
            DB::statement('ALTER TABLE employees ALTER COLUMN mobile TYPE INTEGER USING mobile::integer');
        }
    }
};
