<?php

namespace Tests\Feature;

use App\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CategoryNavbarTest extends TestCase
{
    use RefreshDatabase;

    public function test_secondary_navbar_and_mobile_menu_display_real_database_categories(): void
    {
        $cat1 = Category::create([
            'name' => 'Custom FPGA & CPLD Boards',
            'slug' => 'custom-fpga-cpld',
            'icon' => 'cpu',
            'is_active' => true,
            'display_order' => 1,
        ]);

        $cat2 = Category::create([
            'name' => 'SMD Soldering Rework Gear',
            'slug' => 'smd-soldering-rework',
            'icon' => 'wrench',
            'is_active' => true,
            'display_order' => 2,
        ]);

        $inactiveCat = Category::create([
            'name' => 'Discontinued Hidden Parts',
            'slug' => 'discontinued',
            'icon' => 'box',
            'is_active' => false,
            'display_order' => 3,
        ]);

        $response = $this->get('/');
        $response->assertStatus(200);

        // Check that active categories appear in the top menu bar
        $response->assertSee('Custom FPGA & CPLD Boards');
        $response->assertSee(route('shop.index', ['category_id' => $cat1->id]));
        $response->assertSee('SMD Soldering Rework Gear');
        $response->assertSee(route('shop.index', ['category_id' => $cat2->id]));

        // Inactive category should not appear
        $response->assertDontSee('Discontinued Hidden Parts');
    }
}
