<?php

namespace Tests\Feature;

use App\Models\AppSetting;
use App\Models\DemoVideo;
use App\Models\FrontendVisit;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FrontendConfigurationAndVisitorTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_update_frontend_seo_and_tracking_settings(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($admin)->post(route('admin.settings.frontend'), [
            'seo_title' => 'Bimbel Bahasa Inggris Online | Puwinter',
            'seo_description' => 'Belajar TKA, literasi Bahasa Inggris, grammar, reading text, dan TOEFL bersama Puwinter.',
            'seo_keywords' => 'TKA Inggris, grammar, TOEFL',
            'seo_robots' => 'index,follow',
            'google_tag_manager_id' => 'GTM-ABC1234',
            'google_analytics_id' => 'G-ABC1234567',
            'google_ads_id' => 'AW-18335033383',
            'meta_pixel_id' => '123456789012345',
        ]);

        $response->assertSessionHasNoErrors()->assertSessionHas('success');
        $this->assertSame('Bimbel Bahasa Inggris Online | Puwinter', AppSetting::get('seo_title'));
        $this->assertSame('GTM-ABC1234', AppSetting::get('google_tag_manager_id'));
        $this->assertSame('AW-18335033383', AppSetting::get('google_ads_id'));
    }

    public function test_admin_can_manage_multiple_demo_videos_for_different_grades(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        foreach (['7', '12', 'toefl'] as $category) {
            $label = DemoVideo::CATEGORIES[$category];
            $this->actingAs($admin)->post(route('admin.demo-videos.store'), [
                'category' => $category,
                'title' => "Demo Grammar {$label}",
                'description' => "Materi gratis untuk {$label}.",
                'video_url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
                'sort_order' => 1,
                'is_active' => '1',
            ])->assertSessionHasNoErrors()->assertSessionHas('success');
        }

        $this->assertDatabaseHas('demo_videos', ['category' => '7', 'is_active' => true]);
        $this->assertDatabaseHas('demo_videos', ['category' => '12', 'is_active' => true]);
        $this->assertDatabaseHas('demo_videos', ['category' => 'toefl', 'is_active' => true]);

        $this->actingAs($admin)->get(route('admin.demo-videos.index'))
            ->assertOk()
            ->assertSee('Demo Grammar Kelas 7')
            ->assertSee('Demo Grammar Kelas 12')
            ->assertSee('Demo Grammar TOEFL');
    }

    public function test_frontend_renders_demo_video_catalog_by_grade_with_protected_players(): void
    {
        DemoVideo::create([
            'category' => '7',
            'title' => 'Basic Grammar Kelas 7',
            'description' => 'Belajar simple present secara mudah.',
            'video_url' => 'https://youtu.be/dQw4w9WgXcQ',
            'sort_order' => 1,
            'is_active' => true,
        ]);
        DemoVideo::create([
            'category' => '12',
            'title' => 'Reading Text Kelas 12',
            'video_url' => 'https://vimeo.com/123456789',
            'sort_order' => 1,
            'is_active' => true,
        ]);
        DemoVideo::create([
            'category' => '8',
            'title' => 'Video Nonaktif',
            'video_url' => 'https://youtu.be/dQw4w9WgXcQ',
            'is_active' => false,
        ]);
        DemoVideo::create([
            'category' => 'toefl',
            'title' => 'Reading TOEFL Demo',
            'video_url' => 'https://youtu.be/dQw4w9WgXcQ',
            'is_active' => true,
        ]);

        AppSetting::set('seo_title', 'SEO Puwinter Testing');
        AppSetting::set('seo_description', 'Deskripsi SEO Puwinter untuk pengujian frontend.');
        AppSetting::set('google_analytics_id', 'G-TEST123456');
        AppSetting::set('google_ads_id', 'AW-18335033383');

        $this->get(route('home'))
            ->assertOk()
            ->assertSee('<title>SEO Puwinter Testing</title>', false)
            ->assertSee('youtube-nocookie.com/embed/dQw4w9WgXcQ', false)
            ->assertSee('disablekb=1', false)
            ->assertSee('video-mask-top', false)
            ->assertSee('Tombol share dan buka YouTube disembunyikan')
            ->assertSee('Video Demo <span class="highlight">Pembelajaran</span>', false)
            ->assertSee('Kelas 7')
            ->assertSee('Kelas 12')
            ->assertSee('TOEFL')
            ->assertSee('Basic Grammar Kelas 7')
            ->assertSee('Reading Text Kelas 12')
            ->assertSee('Reading TOEFL Demo')
            ->assertDontSee('Video Nonaktif')
            ->assertSee('G-TEST123456', false)
            ->assertSee("gtag('config','AW-18335033383')", false)
            ->assertSee('Program bimbel Puwinter: Pendampingan bimbel:')
            ->assertSee('TKA Bahasa Inggris')
            ->assertSee('Grammar Dasar &amp; Reading Text TOEFL', false)
            ->assertDontSee('Program Bimbel Puwinter', false);
    }

    public function test_guest_frontend_visit_is_recorded_and_visible_to_admin(): void
    {
        $this->withHeader('User-Agent', 'Mozilla/5.0 (iPhone; CPU iPhone OS 17_0) Version/17.0 Mobile Safari/604.1')
            ->get(route('home'))
            ->assertOk()
            ->assertCookie('puwinter_visitor_id');

        $visit = FrontendVisit::latest('id')->firstOrFail();
        $this->assertSame('/', $visit->path);
        $this->assertSame('Mobile', $visit->device);
        $this->assertNotNull($visit->ip_hash);

        $admin = User::factory()->create(['role' => 'admin']);
        $this->actingAs($admin)->get(route('admin.visitors.index'))
            ->assertOk()
            ->assertSee('Visitor Frontend')
            ->assertSee('Page Views');
    }
}
