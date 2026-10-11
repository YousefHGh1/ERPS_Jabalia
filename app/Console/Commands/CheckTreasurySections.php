<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Section;

class CheckTreasurySections extends Command
{
    protected $signature = 'treasury:check-sections';
    protected $description = 'Check and display section IDs for Central Treasury configuration';

    public function handle()
    {
        $this->info('=== Central Treasury Section Mapping ===');
        $this->newLine();

        // Get all sections
        $sections = Section::with('department')->get();
        
        $this->info('All Available Sections:');
        $this->table(
            ['ID', 'Name', 'Department'],
            $sections->map(fn($s) => [$s->id, $s->name, $s->department->name ?? 'N/A'])->toArray()
        );

        $this->newLine();
        $this->info('Current Configuration (config/central_treasury.php):');
        $this->line('CRAFTS Section ID: ' . (config('central_treasury.revenue_sources.crafts.section_id') ?? 'NOT SET'));
        $this->line('ENGINEERING Section ID: ' . (config('central_treasury.revenue_sources.engineering.section_id') ?? 'NOT SET'));
        $this->line('REQUESTS Section ID: ' . (config('central_treasury.revenue_sources.public_pen_requests.section_id') ?? 'NOT SET'));

        $this->newLine();
        $this->info('Recommended Section IDs based on names:');
        
        $craftsSection = $sections->firstWhere('name', 'section des métiers et industries');
        $engineeringSection = $sections->firstWhere('name', 'section des bâtiments et de l\'organisation');
        $requestsSection = $sections->firstWhere('name', 'section des services municipaux');

        if ($craftsSection) {
            $this->line("CRAFTS: {$craftsSection->id} ({$craftsSection->name})");
        } else {
            $this->warn('CRAFTS: No section found with name "section des métiers et industries"');
        }

        if ($engineeringSection) {
            $this->line("ENGINEERING: {$engineeringSection->id} ({$engineeringSection->name})");
        } else {
            $this->warn('ENGINEERING: No section found with name "section des bâtiments et de l\'organisation"');
        }

        if ($requestsSection) {
            $this->line("REQUESTS: {$requestsSection->id} ({$requestsSection->name})");
        } else {
            $this->warn('REQUESTS: No section found with name "section des services municipaux"');
        }

        $this->newLine();
        $this->info('ACTUAL CORRECT IDs based on database:');
        $this->line('CENTRAL_TREASURY_SECTION_CRAFTS_ID=19 (Crafts and Industries)');
        $this->line('CENTRAL_TREASURY_SECTION_ENGINEERING_ID=17 (Buildings and Organization)');
        $this->line('CENTRAL_TREASURY_SECTION_REQUESTS_ID=38 (Municipal Services)');

        return 0;
    }
}
