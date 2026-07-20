<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('demo_videos', function (Blueprint $table) {
            $table->string('category', 20)->default('7')->after('id');
        });

        DB::table('demo_videos')->orderBy('id')->each(function ($video) {
            DB::table('demo_videos')->where('id', $video->id)->update([
                'category' => (string) $video->grade_level,
            ]);
        });

        Schema::table('demo_videos', function (Blueprint $table) {
            $table->dropIndex('demo_videos_grade_level_is_active_sort_order_index');
            $table->dropIndex('demo_videos_grade_level_index');
            $table->dropColumn('grade_level');
            $table->index('category');
            $table->index(['category', 'is_active', 'sort_order']);
        });
    }

    public function down(): void
    {
        Schema::table('demo_videos', function (Blueprint $table) {
            $table->unsignedTinyInteger('grade_level')->default(7)->after('id');
        });

        DB::table('demo_videos')->orderBy('id')->each(function ($video) {
            $level = ctype_digit((string) $video->category) ? (int) $video->category : 7;
            DB::table('demo_videos')->where('id', $video->id)->update(['grade_level' => $level]);
        });

        Schema::table('demo_videos', function (Blueprint $table) {
            $table->dropIndex('demo_videos_category_is_active_sort_order_index');
            $table->dropIndex('demo_videos_category_index');
            $table->dropColumn('category');
            $table->index('grade_level');
            $table->index(['grade_level', 'is_active', 'sort_order']);
        });
    }
};
