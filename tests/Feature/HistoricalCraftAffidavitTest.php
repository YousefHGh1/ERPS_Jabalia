<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\HistoricalCraft;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HistoricalCraftAffidavitTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Category::create([
            'name' => 'General',
            'start_degree' => 'A',
            'end_degree' => 'Z',
            'basic_salary' => 0,
        ]);
    }

    public function test_historical_craft_affidavit_shows_record_data_and_print_action(): void
    {
        $craft = HistoricalCraft::create([
            'data' => [
                'file_no' => '15680',
                'full_name' => 'أحمد محمد خليل',
                'id_card' => '123456789',
                'detailed_type' => 'تجارة عامة',
                'trade_mark_name' => 'متجر النور',
                'address' => 'جباليا - شارع السوق',
                'first_license_year' => 2018,
            ],
        ]);

        $response = $this->actingAs(User::factory()->create(['password' => 'password']))
            ->get(route('historical-files.show', $craft->id));

        $response->assertOk()
            ->assertSee('إفادة حرفة')
            ->assertSee('15680')
            ->assertSee('أحمد محمد خليل')
            ->assertSee('123456789')
            ->assertSee('تجارة عامة')
            ->assertSee('متجر النور')
            ->assertSee('جباليا - شارع السوق')
            ->assertSee('2018')
            ->assertSee('window.print()');
    }
}
