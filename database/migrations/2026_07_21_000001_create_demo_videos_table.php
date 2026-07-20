<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('demo_videos', function (Blueprint $table) {
            $table->id();
            $table->unsignedTinyInteger('grade_level')->index();
            $table->string('title', 150);
            $table->text('description')->nullable();
            $table->text('video_url');
            $table->text('poster_url')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true)->index();
            $table->timestamps();

            $table->index(['grade_level', 'is_active', 'sort_order']);
        });

        if (Schema::hasTable('app_settings')) {
            $settings = DB::table('app_settings')
                ->whereIn('key', [
                    'frontend_video_enabled',
                    'frontend_video_url',
                    'frontend_video_title',
                    'frontend_video_description',
                    'frontend_video_poster',
                ])
                ->pluck('value', 'key');

            if ($settings->get('frontend_video_url')) {
                DB::table('demo_videos')->insert([
                    'grade_level' => 7,
                    'title' => $settings->get('frontend_video_title') ?: 'Demo Pembelajaran Puwinter',
                    'description' => $settings->get('frontend_video_description'),
                    'video_url' => $settings->get('frontend_video_url'),
                    'poster_url' => $settings->get('frontend_video_poster'),
                    'sort_order' => 1,
                    'is_active' => filter_var($settings->get('frontend_video_enabled', '0'), FILTER_VALIDATE_BOOLEAN),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('demo_videos');
    }
};
