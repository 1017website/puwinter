@extends('admin.layouts.app')
@section('title', 'Video Demo Pembelajaran')

@section('content')
<div class="page-header">
    <div>
        <h2>Video Demo Pembelajaran</h2>
        <p>Kelola video materi gratis untuk menarik calon siswa, dikelompokkan berdasarkan kelas 7–12.</p>
    </div>
    <a href="{{ route('home') }}#video-demo" target="_blank" class="btn btn-outline btn-sm"><i class="fas fa-arrow-up-right-from-square"></i> Lihat Frontend</a>
</div>

<div style="display:grid;grid-template-columns:minmax(0,1fr) 350px;gap:20px;align-items:start;" class="demo-admin-layout">
    <div style="display:flex;flex-direction:column;gap:18px;">
        @foreach(\App\Models\DemoVideo::CATEGORIES as $category => $categoryLabel)
        @php $categoryVideos = $videos->get($category, collect()); @endphp
        <div class="card" style="padding:0;overflow:hidden;">
            <div style="padding:14px 17px;background:#F8FAFC;border-bottom:1px solid var(--border);display:flex;justify-content:space-between;align-items:center;">
                <div style="display:flex;align-items:center;gap:9px;"><span style="min-width:38px;height:34px;padding:0 8px;border-radius:9px;background:#7C3AED;color:#fff;display:grid;place-items:center;font-size:11px;font-weight:800;">{{ strtoupper($category) }}</span><div><strong style="font-size:13px;">{{ $categoryLabel }}</strong><div style="font-size:10.5px;color:var(--muted);">{{ $categoryVideos->count() }} video demo</div></div></div>
            </div>

            @forelse($categoryVideos as $video)
            @php $player = $video->playerData(); @endphp
            <div x-data="{ editOpen: false }" style="padding:15px 17px;border-bottom:1px solid var(--border);">
                <div style="display:grid;grid-template-columns:54px minmax(0,1fr) auto;gap:12px;align-items:center;">
                    <div style="width:54px;height:40px;border-radius:8px;overflow:hidden;background:#0F172A;display:grid;place-items:center;color:#fff;position:relative;">
                        @if($video->poster_url)<img src="{{ $video->poster_url }}" alt="" style="width:100%;height:100%;object-fit:cover;">@else<i class="fas fa-play" style="font-size:12px;"></i>@endif
                    </div>
                    <div style="min-width:0;">
                        <div style="display:flex;align-items:center;gap:7px;flex-wrap:wrap;"><strong style="font-size:13px;">{{ $video->title }}</strong><span class="badge {{ $video->is_active ? 'badge-success' : 'badge-warning' }}" style="font-size:9px;">{{ $video->is_active ? 'Aktif' : 'Nonaktif' }}</span></div>
                        <div style="font-size:10.5px;color:var(--muted);margin-top:3px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">{{ ucfirst($player['provider']) }} · Urutan {{ $video->sort_order }} · {{ $video->video_url }}</div>
                    </div>
                    <div style="display:flex;gap:6px;">
                        <button type="button" @click="editOpen=!editOpen" class="btn btn-outline btn-sm" title="Edit"><i class="fas fa-pen"></i></button>
                        <form method="POST" action="{{ route('admin.demo-videos.destroy', $video) }}" onsubmit="return confirm('Hapus video demo ini?')">@csrf @method('DELETE')<button type="submit" class="btn btn-danger btn-sm" title="Hapus"><i class="fas fa-trash"></i></button></form>
                    </div>
                </div>

                <div x-show="editOpen" x-cloak style="display:none;margin-top:14px;padding:15px;background:#F8FAFC;border-radius:9px;">
                    <form method="POST" action="{{ route('admin.demo-videos.update', $video) }}" enctype="multipart/form-data">
                        @csrf @method('PUT')
                        <div style="display:grid;grid-template-columns:110px minmax(0,1fr) 100px;gap:10px;">
                            <div class="form-group"><label>Kategori</label><select name="category" class="form-control">@foreach(\App\Models\DemoVideo::CATEGORIES as $key => $label)<option value="{{ $key }}" {{ $video->category === $key ? 'selected' : '' }}>{{ $label }}</option>@endforeach</select></div>
                            <div class="form-group"><label>Judul</label><input type="text" name="title" value="{{ $video->title }}" maxlength="150" required class="form-control"></div>
                            <div class="form-group"><label>Urutan</label><input type="number" name="sort_order" value="{{ $video->sort_order }}" min="0" class="form-control"></div>
                        </div>
                        <div class="form-group"><label>Deskripsi</label><textarea name="description" rows="2" maxlength="800" class="form-control">{{ $video->description }}</textarea></div>
                        <div class="form-group"><label>URL Video <small style="font-weight:400;color:var(--muted);">(kosongkan jika tidak berubah)</small></label><input type="url" name="video_url" placeholder="{{ $video->video_url }}" class="form-control"></div>
                        <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px;">
                            <div class="form-group"><label>Ganti File Video</label><input type="file" name="video_file" accept="video/mp4,video/webm,video/quicktime" class="form-control"></div>
                            <div class="form-group"><label>Ganti Poster</label><input type="file" name="poster_file" accept="image/png,image/jpeg,image/webp" class="form-control"></div>
                        </div>
                        <label style="display:flex;gap:8px;align-items:center;font-size:12px;font-weight:600;margin-bottom:13px;"><input type="checkbox" name="is_active" value="1" {{ $video->is_active ? 'checked' : '' }}> Tampilkan di frontend</label>
                        <div style="display:flex;gap:7px;"><button class="btn btn-primary btn-sm" type="submit"><i class="fas fa-save"></i> Simpan</button><button class="btn btn-outline btn-sm" type="button" @click="editOpen=false">Batal</button></div>
                    </form>
                </div>
            </div>
            @empty
            <div style="padding:22px;text-align:center;color:var(--muted);font-size:12px;"><i class="fas fa-film" style="opacity:.3;margin-right:5px;"></i> Belum ada video untuk {{ $categoryLabel }}.</div>
            @endforelse
        </div>
        @endforeach
    </div>

    <div style="position:sticky;top:80px;">
        <div class="card">
            <div style="font-size:14px;font-weight:700;margin-bottom:5px;">Tambah Video Demo</div>
            <p style="font-size:11px;color:var(--muted);line-height:1.5;margin-bottom:15px;">Gunakan URL YouTube/Vimeo atau unggah file video langsung.</p>
            <form method="POST" action="{{ route('admin.demo-videos.store') }}" enctype="multipart/form-data">
                @csrf
                <div class="form-group"><label>Kategori <span style="color:var(--danger);">*</span></label><select name="category" class="form-control" required>@foreach(\App\Models\DemoVideo::CATEGORIES as $key => $label)<option value="{{ $key }}" {{ old('category', '7') === $key ? 'selected' : '' }}>{{ $label }}</option>@endforeach</select>@error('category')<div style="font-size:11px;color:var(--danger);margin-top:3px;">{{ $message }}</div>@enderror</div>
                <div class="form-group"><label>Judul <span style="color:var(--danger);">*</span></label><input type="text" name="title" value="{{ old('title') }}" maxlength="150" required class="form-control" placeholder="Contoh: Basic Grammar - Simple Present">@error('title')<div style="font-size:11px;color:var(--danger);margin-top:3px;">{{ $message }}</div>@enderror</div>
                <div class="form-group"><label>Deskripsi</label><textarea name="description" rows="3" maxlength="800" class="form-control" placeholder="Ringkasan materi dalam video">{{ old('description') }}</textarea></div>
                <div class="form-group"><label>URL Video</label><input type="url" name="video_url" value="{{ old('video_url') }}" class="form-control" placeholder="https://youtube.com/watch?v=...">@error('video_url')<div style="font-size:11px;color:var(--danger);margin-top:3px;">{{ $message }}</div>@enderror</div>
                <div style="text-align:center;font-size:10px;color:var(--muted);margin:-5px 0 10px;">— atau —</div>
                <div class="form-group"><label>Upload Video</label><input type="file" name="video_file" accept="video/mp4,video/webm,video/quicktime" class="form-control"><small style="font-size:10.5px;color:var(--muted);">MP4/WebM/MOV, maksimal 100 MB.</small>@error('video_file')<div style="font-size:11px;color:var(--danger);margin-top:3px;">{{ $message }}</div>@enderror</div>
                <div class="form-group"><label>Poster <small style="font-weight:400;color:var(--muted);">(opsional)</small></label><input type="file" name="poster_file" accept="image/png,image/jpeg,image/webp" class="form-control"></div>
                <div class="form-group"><label>Urutan</label><input type="number" name="sort_order" value="{{ old('sort_order', 0) }}" min="0" max="9999" class="form-control"></div>
                <label style="display:flex;gap:8px;align-items:center;font-size:12px;font-weight:600;margin-bottom:14px;"><input type="checkbox" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}> Langsung tampil di frontend</label>
                <button type="submit" class="btn btn-primary" style="width:100%;justify-content:center;"><i class="fas fa-plus"></i> Tambah Video</button>
            </form>
        </div>
    </div>
</div>

@push('styles')
<style>
@media(max-width:1000px){.demo-admin-layout{grid-template-columns:1fr!important}.demo-admin-layout>div:last-child{position:static!important;grid-row:1}}
@media(max-width:640px){.demo-admin-layout .card>div{max-width:100%}.demo-admin-layout [style*="grid-template-columns:54px"]{grid-template-columns:44px minmax(0,1fr)!important}.demo-admin-layout [style*="grid-template-columns:110px"]{grid-template-columns:1fr!important}}
</style>
@endpush
@endsection
