<?php
namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class HistoricalCraftsSeeder extends Seeder
{
    /**
     * Number of rows inserted per batch.
     * Keep this modest so a single INSERT statement doesn't exceed
     * MySQL's max_allowed_packet / SQLite's variable limits.
     */
    private const CHUNK_SIZE = 500;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $path = database_path('seeders/data/historical_crafts.json');

        if (! file_exists($path)) {
            $this->command->warn("Data file not found at {$path}; nothing was seeded.");
            return;
        }

        $rows = json_decode((string) file_get_contents($path), true, 512, JSON_THROW_ON_ERROR);

        if (! is_array($rows)) {
            $this->command->error('Historical craft seed data must be an array of rows.');
            return;
        }

        $total = count($rows);
        $this->command->info("Seeding {$total} historical_crafts rows...");

        DB::disableQueryLog();

        $now = Carbon::now();

        collect($rows)
            ->filter(fn($row) => is_array($row))
            ->map(function (array $row) use ($now) {
                $payload = $row;
                unset($payload['created_at'], $payload['updated_at']);

                return [
                    'data'       => json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            })
            ->chunk(self::CHUNK_SIZE)
            ->each(function ($chunk) {
                DB::table('historical_crafts')->insert($chunk->all());
            });

        $this->command->info('historical_crafts seeded successfully.');

        // بناء فهرس البحث للسجلات المضافة (الإدخال الجماعي لا يمر بأحداث النموذج)
        $this->command->call('historical:reindex', ['--source' => 'crafts']);
    }
}
