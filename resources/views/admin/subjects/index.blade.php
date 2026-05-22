@extends('admin.layouts.app')
@section('title', 'Mata Pelajaran')

@section('content')

<div class="page-header">
    <div>
        <h2>Mata Pelajaran</h2>
        <p>Kelola daftar mata pelajaran yang tersedia di platform.</p>
    </div>
</div>

<div style="display:grid; grid-template-columns:1fr 340px; gap:20px; align-items:flex-start;">

    {{-- Daftar Subject --}}
    <div>
        <div class="card" style="padding:0; overflow:hidden;">
            <table style="width:100%; border-collapse:collapse; font-size:13px;">
                <thead>
                    <tr style="background:var(--bg); border-bottom:1px solid var(--border);">
                        <th style="padding:12px 16px; text-align:left; font-size:11px; font-weight:700; text-transform:uppercase; letter-spacing:0.5px; color:var(--muted);">Mata Pelajaran</th>
                        <th style="padding:12px 16px; text-align:center; font-size:11px; font-weight:700; text-transform:uppercase; letter-spacing:0.5px; color:var(--muted);">Kelas</th>
                        <th style="padding:12px 16px; text-align:center; font-size:11px; font-weight:700; text-transform:uppercase; letter-spacing:0.5px; color:var(--muted);">Tryout</th>
                        <th style="padding:12px 16px; text-align:center; font-size:11px; font-weight:700; text-transform:uppercase; letter-spacing:0.5px; color:var(--muted);">Status</th>
                        <th style="padding:12px 16px; text-align:right; font-size:11px; font-weight:700; text-transform:uppercase; letter-spacing:0.5px; color:var(--muted);">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($subjects as $subject)
                    <tr style="border-bottom:1px solid var(--border);" x-data="{ editOpen: false }">
                        <td style="padding:12px 16px;">
                            <div style="display:flex; align-items:center; gap:10px;">
                                <div style="width:34px; height:34px; border-radius:8px; background:{{ $subject->color ?? '#2563EB' }}20; display:flex; align-items:center; justify-content:center; flex-shrink:0;">
                                    <i class="fas {{ $subject->icon ?? 'fa-book' }}" style="color:{{ $subject->color ?? '#2563EB' }}; font-size:14px;"></i>
                                </div>
                                <div>
                                    <div style="font-weight:700;">{{ $subject->name }}</div>
                                    <div style="font-size:11px; color:var(--muted);">Urutan: {{ $subject->order }}</div>
                                </div>
                            </div>
                            {{-- Inline edit form --}}
                            <div x-show="editOpen" style="margin-top:10px; padding:14px; background:var(--bg); border-radius:8px; display:none;" x-cloak>
                                <form method="POST" action="{{ route('admin.subjects.update', $subject) }}">
                                    @csrf @method('PUT')
                                    <div style="display:grid; grid-template-columns:1fr 1fr; gap:8px; margin-bottom:8px;">
                                        <div>
                                            <label style="font-size:11px; font-weight:600; color:var(--muted); display:block; margin-bottom:3px;">Nama</label>
                                            <input type="text" name="name" value="{{ $subject->name }}" class="form-control" style="font-size:12.5px;" required>
                                        </div>
                                        <div>
                                            <label style="font-size:11px; font-weight:600; color:var(--muted); display:block; margin-bottom:3px;">Icon (Font Awesome)</label>
                                            <input type="text" name="icon" value="{{ $subject->icon }}" class="form-control" style="font-size:12.5px;" placeholder="fa-book">
                                        </div>
                                        <div>
                                            <label style="font-size:11px; font-weight:600; color:var(--muted); display:block; margin-bottom:3px;">Warna</label>
                                            <div style="display:flex; gap:6px; align-items:center;">
                                                <input type="color" name="color" value="{{ $subject->color ?? '#2563EB' }}" style="width:38px; height:34px; border:1px solid var(--border); border-radius:6px; cursor:pointer; padding:2px;">
                                                <input type="text" value="{{ $subject->color ?? '#2563EB' }}" style="flex:1; font-size:12px; padding:7px 10px; border:1px solid var(--border); border-radius:6px; font-family:monospace;" oninput="this.previousElementSibling.value=this.value" name="_color_text">
                                            </div>
                                        </div>
                                        <div>
                                            <label style="font-size:11px; font-weight:600; color:var(--muted); display:block; margin-bottom:3px;">Urutan</label>
                                            <input type="number" name="order" value="{{ $subject->order }}" class="form-control" style="font-size:12.5px;">
                                        </div>
                                    </div>
                                    <div style="display:flex; gap:6px;">
                                        <button type="submit" class="btn btn-primary btn-sm">Simpan</button>
                                        <button type="button" @click="editOpen=false" class="btn btn-outline btn-sm">Batal</button>
                                    </div>
                                </form>
                            </div>
                        </td>
                        <td style="padding:12px 16px; text-align:center; font-weight:700;">{{ $subject->courses_count }}</td>
                        <td style="padding:12px 16px; text-align:center; font-weight:700;">{{ $subject->tryouts_count }}</td>
                        <td style="padding:12px 16px; text-align:center;">
                            <form method="POST" action="{{ route('admin.subjects.toggle-active', $subject) }}">
                                @csrf @method('PATCH')
                                <button type="submit"
                                        class="badge {{ $subject->is_active ? 'badge-success' : 'badge-warning' }}"
                                        style="border:none; cursor:pointer; font-size:11px; padding:4px 10px;">
                                    {{ $subject->is_active ? 'Aktif' : 'Nonaktif' }}
                                </button>
                            </form>
                        </td>
                        <td style="padding:12px 16px; text-align:right;">
                            <div style="display:flex; gap:6px; justify-content:flex-end;">
                                <button type="button" @click="editOpen=!editOpen" class="btn btn-outline btn-sm">
                                    <i class="fas fa-pen"></i>
                                </button>
                                <form method="POST" action="{{ route('admin.subjects.destroy', $subject) }}"
                                      onsubmit="return confirm('Hapus mata pelajaran {{ $subject->name }}?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm"><i class="fas fa-trash"></i></button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" style="padding:60px; text-align:center; color:var(--muted);">
                            <i class="fas fa-tag" style="font-size:32px; opacity:0.2; display:block; margin-bottom:10px;"></i>
                            Belum ada mata pelajaran.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Form tambah --}}
    <div style="position:sticky; top:80px;">
        <div class="card">
            <div style="font-size:14px; font-weight:700; margin-bottom:16px; padding-bottom:10px; border-bottom:1px solid var(--border);">
                Tambah Mata Pelajaran
            </div>
            <form method="POST" action="{{ route('admin.subjects.store') }}">
                @csrf
                <div class="form-group">
                    <label>Nama <span style="color:var(--danger);">*</span></label>
                    <input type="text" name="name" class="form-control" placeholder="Contoh: Penalaran Umum" required value="{{ old('name') }}">
                    @error('name') <div style="font-size:11px; color:var(--danger); margin-top:3px;">{{ $message }}</div> @enderror
                </div>
                <div class="form-group">
                    <label>Icon <span style="font-size:11px; color:var(--muted); font-weight:400;">(Font Awesome class)</span></label>
                    <input type="text" name="icon" class="form-control" placeholder="fa-book-open" value="{{ old('icon', 'fa-book') }}">
                    <div style="font-size:11px; color:var(--muted); margin-top:3px;">
                        Contoh: fa-calculator, fa-flask, fa-globe, fa-brain
                    </div>
                </div>
                <div class="form-group">
                    <label>Warna</label>
                    <div style="display:flex; gap:8px; align-items:center;">
                        <input type="color" name="color" value="{{ old('color', '#2563EB') }}" id="colorPicker"
                               style="width:42px; height:36px; border:1px solid var(--border); border-radius:6px; cursor:pointer; padding:2px;">
                        <span style="font-size:12px; color:var(--muted);">Pilih warna untuk ikon</span>
                    </div>
                </div>
                <div class="form-group">
                    <label>Urutan</label>
                    <input type="number" name="order" class="form-control" value="{{ old('order', ($subjects->max('order') ?? 0) + 1) }}">
                </div>

                {{-- Preview --}}
                <div style="background:var(--bg); border-radius:8px; padding:12px; margin-bottom:14px; display:flex; align-items:center; gap:10px;">
                    <div id="previewIcon" style="width:36px; height:36px; border-radius:8px; background:#2563EB20; display:flex; align-items:center; justify-content:center;">
                        <i class="fas fa-book" id="previewIconEl" style="color:#2563EB; font-size:15px;"></i>
                    </div>
                    <span id="previewName" style="font-size:13px; font-weight:600; color:var(--text);">Preview</span>
                </div>

                <button type="submit" class="btn btn-primary" style="width:100%; justify-content:center;">
                    <i class="fas fa-plus"></i> Tambah Mata Pelajaran
                </button>
            </form>
        </div>
    </div>

</div>

@endsection

@push('scripts')
<script>
// Live preview icon dan warna
const nameInput  = document.querySelector('input[name="name"]');
const iconInput  = document.querySelector('input[name="icon"]');
const colorInput = document.querySelector('input[name="color"]');
const prevIcon   = document.getElementById('previewIcon');
const prevIconEl = document.getElementById('previewIconEl');
const prevName   = document.getElementById('previewName');

function updatePreview() {
    const icon  = iconInput.value.trim() || 'fa-book';
    const color = colorInput.value || '#2563EB';
    const name  = nameInput.value.trim() || 'Preview';

    prevIcon.style.background  = color + '20';
    prevIconEl.className       = 'fas ' + icon;
    prevIconEl.style.color     = color;
    prevName.textContent       = name;
}

nameInput.addEventListener('input', updatePreview);
iconInput.addEventListener('input', updatePreview);
colorInput.addEventListener('input', updatePreview);
</script>
@endpush
