<?php

namespace Tests\Feature;

use App\Models\AppSetting;
use App\Models\FrontendVisit;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FrontendConfigurationAndVisitorTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_update_frontend_seo_video_and_tracking_settings(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($admin)->post(route('admin.settings.frontend'), [
            'video_enabled' => '1',
            'video_url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
            'video_title' => 'Video Belajar Puwinter',
            'video_description' => 'Kenali metode pendampingan belajar kami.',
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
        $this->assertSame('1', AppSetting::get('frontend_video_enabled'));
        $this->assertSame('Bimbel Bahasa Inggris Online | Puwinter', AppSetting::get('seo_title'));
        $this->assertSame('GTM-ABC1234', AppSetting::get('google_tag_manager_id'));
        $this->assertSame('AW-18335033383', AppSetting::get('google_ads_id'));
    }

    public function test_frontend_renders_configured_seo_tracking_video_and_programs(): void
    {
        AppSetting::set('frontend_video_enabled', '1');
        AppSetting::set('frontend_video_url', 'https://youtu.be/dQw4w9WgXcQ');
        AppSetting::set('frontend_video_title', 'Profil Program Puwinter');
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
