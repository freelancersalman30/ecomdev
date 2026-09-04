<?php

namespace Tests\Feature;

use App\Models\Page;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class PageManagementTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $admin = User::firstOrCreate(
            ['email' => 'admin@dreamerspcb.com'],
            ['name' => 'Dreamers Admin', 'password' => Hash::make('password')]
        );
        $this->actingAs($admin, 'web');
    }

    public function test_admin_can_view_pages_index(): void
    {
        $response = $this->get(route('admin.pages.index'));
        $response->assertStatus(200);
        $response->assertSee('Custom Pages & Policies');
    }

    public function test_admin_can_view_create_page(): void
    {
        $response = $this->get(route('admin.pages.create'));
        $response->assertStatus(200);
        $response->assertSee('Create Custom Page');
        $response->assertSee('Warranty Policy');
    }

    public function test_admin_can_create_custom_page(): void
    {
        $pageData = [
            'title' => 'Test Return Policy',
            'slug' => 'test-return-policy',
            'content' => '<h3>Return terms</h3><p>Return within 7 days with original invoice.</p>',
            'placement' => 'both',
            'sort_order' => 1,
            'is_active' => '1',
            'meta_title' => 'Return Policy - Dreamers PCB',
            'meta_description' => 'Learn how our return process works seamlessly.',
            'meta_keywords' => 'returns, refund, warranty',
        ];

        $response = $this->post(route('admin.pages.store'), $pageData);
        $response->assertRedirect(route('admin.pages.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('pages', [
            'slug' => 'test-return-policy',
            'placement' => 'both',
            'is_active' => true,
        ]);
    }

    public function test_admin_can_edit_and_update_page(): void
    {
        $page = Page::create([
            'title' => 'Original Warranty',
            'slug' => 'original-warranty',
            'content' => '<p>Original warranty content.</p>',
            'placement' => 'footer',
            'is_active' => true,
            'sort_order' => 5,
        ]);

        $editResponse = $this->get(route('admin.pages.edit', $page));
        $editResponse->assertStatus(200);
        $editResponse->assertSee('Original Warranty');

        $updateResponse = $this->put(route('admin.pages.update', $page), [
            'title' => 'Updated Warranty Coverage',
            'slug' => 'updated-warranty-coverage',
            'content' => '<p>Updated comprehensive warranty coverage details.</p>',
            'placement' => 'header',
            'sort_order' => 2,
            'is_active' => '1',
        ]);

        $updateResponse->assertRedirect(route('admin.pages.index'));
        $this->assertDatabaseHas('pages', [
            'id' => $page->id,
            'title' => 'Updated Warranty Coverage',
            'slug' => 'updated-warranty-coverage',
            'placement' => 'header',
        ]);
    }

    public function test_admin_can_toggle_page_status(): void
    {
        $page = Page::create([
            'title' => 'Toggle Test Page',
            'slug' => 'toggle-test-page',
            'content' => '<p>Toggle test content.</p>',
            'placement' => 'none',
            'is_active' => true,
        ]);

        $toggleResponse = $this->post(route('admin.pages.toggle', $page));
        $toggleResponse->assertRedirect();
        $this->assertDatabaseHas('pages', [
            'id' => $page->id,
            'is_active' => false,
        ]);

        // Toggle back to active
        $this->post(route('admin.pages.toggle', $page));
        $this->assertDatabaseHas('pages', [
            'id' => $page->id,
            'is_active' => true,
        ]);
    }

    public function test_admin_can_delete_page(): void
    {
        $page = Page::create([
            'title' => 'Page To Delete',
            'slug' => 'page-to-delete',
            'content' => '<p>Delete this page.</p>',
            'placement' => 'none',
            'is_active' => true,
        ]);

        $deleteResponse = $this->delete(route('admin.pages.destroy', $page));
        $deleteResponse->assertRedirect(route('admin.pages.index'));
        $this->assertDatabaseMissing('pages', ['id' => $page->id]);
    }

    public function test_public_storefront_renders_active_page(): void
    {
        $page = Page::create([
            'title' => 'Public Delivery Policy',
            'slug' => 'public-delivery-policy',
            'content' => '<p>We deliver nationwide inside Bangladesh within 48 hours.</p>',
            'placement' => 'both',
            'is_active' => true,
            'meta_title' => 'Fast Delivery Information',
            'meta_description' => 'Nationwide fast delivery details.',
        ]);

        $response = $this->get(route('page.show', $page->slug));
        $response->assertStatus(200);
        $response->assertSee('Public Delivery Policy');
        $response->assertSee('We deliver nationwide inside Bangladesh within 48 hours.');
    }

    public function test_public_storefront_returns_404_for_inactive_page(): void
    {
        $page = Page::create([
            'title' => 'Draft Inactive Page',
            'slug' => 'draft-inactive-page',
            'content' => '<p>Draft content not ready for public view.</p>',
            'placement' => 'none',
            'is_active' => false,
        ]);

        $response = $this->get(route('page.show', $page->slug));
        $response->assertStatus(404);
    }

    public function test_storefront_layout_renders_header_and_footer_pages(): void
    {
        Page::create([
            'title' => 'Top Bar Header Page',
            'slug' => 'top-bar-header-page',
            'content' => '<p>Header content.</p>',
            'placement' => 'header',
            'is_active' => true,
            'sort_order' => 1,
        ]);

        Page::create([
            'title' => 'Footer Only Policy',
            'slug' => 'footer-only-policy',
            'content' => '<p>Footer content.</p>',
            'placement' => 'footer',
            'is_active' => true,
            'sort_order' => 1,
        ]);

        $response = $this->get(route('home'));
        $response->assertStatus(200);
        $response->assertSee('Top Bar Header Page');
        $response->assertSee('Footer Only Policy');
    }
}
