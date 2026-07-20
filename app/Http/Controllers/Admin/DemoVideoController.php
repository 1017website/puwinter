<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DemoVideo;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class DemoVideoController extends Controller
{
    public function index(): View
    {
        $videos = DemoVideo::query()
            ->orderBy('grade_level')
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get()
            ->groupBy('grade_level');

        return view('admin.demo-videos.index', compact('videos'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validateVideo($request, true);
        $data = $this->handleUploads($request, $data);
        $data['is_active'] = $request->boolean('is_active');

        DemoVideo::create($data);

        return back()->with('success', 'Video demo berhasil ditambahkan.');
    }

    public function update(Request $request, DemoVideo $demoVideo): RedirectResponse
    {
        $data = $this->validateVideo($request, false);
        $data = $this->handleUploads($request, $data);
        $data['is_active'] = $request->boolean('is_active');

        if (empty($data['video_url'])) {
            unset($data['video_url']);
        }

        $demoVideo->update($data);

        return back()->with('success', 'Video demo berhasil diperbarui.');
    }

    public function destroy(DemoVideo $demoVideo): RedirectResponse
    {
        $demoVideo->delete();

        return back()->with('success', 'Video demo berhasil dihapus.');
    }

    private function validateVideo(Request $request, bool $creating): array
    {
        return $request->validate([
            'grade_level' => ['required', 'integer', Rule::in(DemoVideo::GRADE_LEVELS)],
            'title' => 'required|string|max:150',
            'description' => 'nullable|string|max:800',
            'video_url' => [$creating ? 'required_without:video_file' : 'nullable', 'nullable', 'url', 'max:2048'],
            'video_file' => 'nullable|file|mimes:mp4,webm,mov|max:102400',
            'poster_file' => 'nullable|image|mimes:png,jpg,jpeg,webp|max:4096',
            'sort_order' => 'nullable|integer|min:0|max:9999',
            'is_active' => 'nullable|boolean',
        ], [
            'grade_level.in' => 'Kategori kelas harus antara kelas 7 sampai 12.',
            'video_url.required_without' => 'Isi URL video atau unggah file video.',
            'video_url.url' => 'URL video harus berupa URL lengkap (https://...).',
        ]);
    }

    private function handleUploads(Request $request, array $data): array
    {
        if (! $request->hasFile('video_file') && ! $request->hasFile('poster_file')) {
            return $data;
        }

        $directory = public_path('uploads/demo-videos');
        if (! is_dir($directory)) {
            mkdir($directory, 0755, true);
        }

        if ($request->hasFile('video_file')) {
            $extension = $request->file('video_file')->getClientOriginalExtension();
            $filename = 'video-'.Str::uuid().'.'.$extension;
            $request->file('video_file')->move($directory, $filename);
            $data['video_url'] = asset('uploads/demo-videos/'.$filename);
        }

        if ($request->hasFile('poster_file')) {
            $extension = $request->file('poster_file')->getClientOriginalExtension();
            $filename = 'poster-'.Str::uuid().'.'.$extension;
            $request->file('poster_file')->move($directory, $filename);
            $data['poster_url'] = asset('uploads/demo-videos/'.$filename);
        }

        return $data;
    }
}
