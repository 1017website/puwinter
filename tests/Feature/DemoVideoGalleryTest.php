<?php

namespace Tests\Feature;

use App\Models\DemoVideo;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DemoVideoGalleryTest extends TestCase
{
    use RefreshDatabase;

    public function test_homepage_shows_all_demo_videos_as_one_thumbnail_gallery(): void
    {
        foreach ([
            ['category' => '7', 'title' => 'Grammar Kelas 7'],
            ['category' => '12', 'title' => 'TKA Kelas 12'],
            ['category' => 'toefl', 'title' => 'Reading TOEFL'],
        ] as $index => $video) {
            DemoVideo::create($video + [
                'video_url' => 'https://youtu.be/dQw4w9WgXcQ',
                'sort_order' => $index + 1,
                'is_active' => true,
            ]);
        }

        $this->get(route('home'))
            ->assertOk()
            ->assertSee('Grammar Kelas 7')
            ->assertSee('TKA Kelas 12')
            ->assertSee('Reading TOEFL')
            ->assertSee('https://i.ytimg.com/vi/dQw4w9WgXcQ/hqdefault.jpg', false)
            ->assertSeeInOrder(['Grammar Kelas 7', 'TKA Kelas 12', 'Reading TOEFL'])
            ->assertSee('Tonton Gratis')
            ->assertSee('id="demoPlayerModal"', false)
            ->assertDontSee('class="demo-tabs"', false)
            ->assertDontSee('class="demo-panel"', false)
            ->assertDontSee('<iframe', false);
    }
}
