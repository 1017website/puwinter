@extends('admin.layouts.app')
@section('title', 'Program')

@section('content')

<div class="page-header">
    <div>
        <h2>Program</h2>
        <p>Kelola program belajar yang ditawarkan ke siswa.</p>
    </div>
</div>

<div style="display:grid; grid-template-columns:1fr 360px; gap:20px; align-items:flex-start;">

    {{-- Daftar Program --}}
    <div style="display:flex; flex-direction:column; gap:14px;">
        @forelse($plans as $plan)
        <div class="card" style="padding:0; overflow:hidden; {{ !$plan->is_active ? 'opacity:0.65;' : '' }}" x-data="{ editOpen: false }">

            {{-- Header --}}
            <div style="padding:16px 20px; display:flex; align-items:center; gap:14px; border-bottom:1px solid var(--border);">
                @if($plan->flyer_image)
                <img src="{{ asset('storage/'.$plan->flyer_image) }}" alt="flyer"
                     style="width:46px; height:64px; object-fit:cover; border-radius:6px; border:1px solid var(--border); flex-shrink:0; cursor:pointer;"
                     onclick="window.open('{{ asset('storage/'.$plan->flyer_image) }}','_blank')">
                @endif
                <div style="flex:1; min-width:0;">
                    <div style="display:flex; align-items:center; gap:8px; flex-wrap:wrap; margin-bottom:4px;">
                        <span style="font-size:15px; font-weight:800;">{{ $plan->name }}</span>
                        @if(($plan->tier ?? 'regular') === 'exclusive')
                            <span class="badge" style="font-size:10px; background:#EDE9FE; color:#6D28D9;"><i class="fas fa-star" style="font-size:9px;"></i> Exclusive</span>
                        @endif
                        @if($plan->is_popular)
                            <span class="badge badge-warning" style="font-size:10px;"><i class="fas fa-fire" style="font-size:9px;"></i> Populer</span>
                        @endif
                        @if(!$plan->is_active)
                            <span class="badge badge-warning" style="font-size:10px;">Nonaktif</span>
                        @endif
                    </div>
                    <div style="display:flex; align-items:center; gap:16px; font-size:12px; color:var(--muted); flex-wrap:wrap;">
                        <span><i class="fas fa-calendar" style="margin-right:3px;"></i>{{ $plan->duration_months }} bulan</span>
                        <span><i class="fas fa-graduation-cap" style="margin-right:3px;"></i>Kelas: {{ $plan->gradeLabel() }}</span>
                        @if($plan->periodLabel())
                            <span><i class="fas fa-calendar-day" style="margin-right:3px;"></i>{{ $plan->periodLabel() }}</span>
                        @endif
                        <span><i class="fas fa-users" style="margin-right:3px;"></i>{{ $plan->subscriptions_count }} subscriber</span>
                        @if(!is_null($plan->quota))
                            <span style="color:{{ $plan->isQuotaFull() ? 'var(--danger)' : 'var(--muted)' }};">
                                <i class="fas fa-user-check" style="margin-right:3px;"></i>Kuota: {{ $plan->paidCount() }}/{{ $plan->quota }}
                                @if($plan->isQuotaFull()) (PENUH) @endif
                            </span>
                        @endif
                        <span>Urutan: {{ $plan->order }}</span>
                    </div>
                </div>
                <div style="text-align:right; flex-shrink:0;">
                    <div style="font-size:20px; font-weight:800; color:var(--primary);">Rp {{ number_format($plan->price) }}</div>
                    @if($plan->original_price > $plan->price)
                        <div style="font-size:11px; color:var(--muted); text-decoration:line-through;">Rp {{ number_format($plan->original_price) }}</div>
                        <div style="font-size:11px; color:var(--success); font-weight:600;">Hemat {{ $plan->discountPercentage() }}%</div>
                    @endif
                </div>
                <div style="display:flex; gap:6px; flex-shrink:0;">
                    <button @click="editOpen=!editOpen" class="btn btn-outline btn-sm"><i class="fas fa-pen"></i></button>
                    <form method="POST" action="{{ route('admin.plans.destroy', $plan) }}"
                          onsubmit="return confirm('Hapus program {{ $plan->name }}?')">
                        @csrf @method('DELETE')
                        <button type="submit" class="btn btn-danger btn-sm"><i class="fas fa-trash"></i></button>
                    </form>
                </div>
            </div>

            {{-- Fitur --}}
            @if($plan->features)
            <div style="padding:12px 20px; border-bottom:1px solid var(--border);">
                <div style="display:flex; flex-wrap:wrap; gap:6px;">
                    @foreach($plan->features as $feature)
                        <span style="font-size:11px; background:var(--bg); border:1px solid var(--border); padding:3px 8px; border-radius:6px; color:var(--text);">
                            <i class="fas fa-check" style="color:var(--success); font-size:9px; margin-right:3px;"></i>{{ $feature }}
                        </span>
                    @endforeach
                </div>
            </div>
            @endif

            {{-- Edit Form --}}
            <div x-show="editOpen" style="padding:16px 20px; background:var(--bg); display:none;" x-cloak>
                <form method="POST" action="{{ route('admin.plans.update', $plan) }}" enctype="multipart/form-data">
                    @csrf @method('PUT')
                    <div style="display:grid; grid-template-columns:1fr 1fr; gap:12px; margin-bottom:12px;">
                        <div class="form-group" style="margin-bottom:0; grid-column:span 2;">
                            <label style="font-size:11px; font-weight:700; color:var(--muted);">Nama Program</label>
                            <input type="text" name="name" value="{{ $plan->name }}" class="form-control" style="font-size:13px;" required>
                        </div>
                        <div class="form-group" style="margin-bottom:0;">
                            <label style="font-size:11px; font-weight:700; color:var(--muted);">Tipe Program</label>
                            <select name="tier" class="form-control" style="font-size:13px;">
                                <option value="regular" {{ ($plan->tier ?? 'regular') === 'regular' ? 'selected' : '' }}>Regular</option>
                                <option value="exclusive" {{ ($plan->tier ?? '') === 'exclusive' ? 'selected' : '' }}>Exclusive (kuota terbatas)</option>
                            </select>
                        </div>

                        <div class="form-group" style="margin-bottom:0;">
                            <label style="font-size:11px; font-weight:700; color:var(--muted);">Kelas Program</label>
                            <select name="grade_id" class="form-control" style="font-size:13px;">
                                <option value="">Semua Kelas</option>
                                @foreach($grades as $grade)
                                    <option value="{{ $grade->id }}" {{ (int) $plan->grade_id === (int) $grade->id ? 'selected' : '' }}>{{ $grade->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group" style="margin-bottom:0;">
                            <label style="font-size:11px; font-weight:700; color:var(--muted);">Kuota Berbayar <span style="font-weight:400;">(kosong = tanpa batas)</span></label>
                            <input type="number" name="quota" value="{{ $plan->quota }}" class="form-control" style="font-size:13px;" min="1" placeholder="mis. 30">
                        </div>
                        <div class="form-group" style="margin-bottom:0;">
                            <label style="font-size:11px; font-weight:700; color:var(--muted);">Harga (Rp)</label>
                            <input type="number" name="price" value="{{ $plan->price }}" class="form-control" style="font-size:13px;" required>
                        </div>
                        <div class="form-group" style="margin-bottom:0;">
                            <label style="font-size:11px; font-weight:700; color:var(--muted);">Harga Normal (Rp)</label>
                            <input type="number" name="original_price" value="{{ $plan->original_price }}" class="form-control" style="font-size:13px;" required>
                        </div>
                        <div class="form-group" style="margin-bottom:0;">
                            <label style="font-size:11px; font-weight:700; color:var(--muted);">Durasi (bulan)</label>
                            <input type="number" name="duration_months" value="{{ $plan->duration_months }}" class="form-control" style="font-size:13px;" min="1" required>
                        </div>
                        <div class="form-group" style="margin-bottom:0;">
                            <label style="font-size:11px; font-weight:700; color:var(--muted);">Urutan</label>
                            <input type="number" name="order" value="{{ $plan->order }}" class="form-control" style="font-size:13px;">
                        </div>
                        <div class="form-group" style="margin-bottom:0;">
                            <label style="font-size:11px; font-weight:700; color:var(--muted);">Mulai</label>
                            <input type="date" name="start_date" value="{{ optional($plan->start_date)->format('Y-m-d') }}" class="form-control" style="font-size:13px;">
                        </div>
                        <div class="form-group" style="margin-bottom:0;">
                            <label style="font-size:11px; font-weight:700; color:var(--muted);">Selesai</label>
                            <input type="date" name="end_date" value="{{ optional($plan->end_date)->format('Y-m-d') }}" class="form-control" style="font-size:13px;">
                        </div>
                        <div class="form-group" style="margin-bottom:0; grid-column:span 2;">
                            <label style="font-size:11px; font-weight:700; color:var(--muted);">Gambar Pamflet <span style="font-weight:400;">(maks 4MB)</span></label>
                            <input type="file" name="flyer_image" accept="image/*" class="form-control" style="font-size:13px;">
                            @if($plan->flyer_image)
                                <div style="font-size:11px; color:var(--muted); margin-top:4px;">Sudah ada pamflet. Upload baru untuk mengganti.</div>
                            @endif
                        </div>
                        <div class="form-group" style="margin-bottom:0; grid-column:span 2;">
                            <label style="font-size:11px; font-weight:700; color:var(--muted);">Fitur (1 baris = 1 fitur)</label>
                            <textarea name="features" class="form-control" rows="4" style="font-size:12.5px; resize:vertical;">{{ implode("\n", $plan->features ?? []) }}</textarea>
                        </div>
                        <div class="form-group" style="margin-bottom:0; grid-column:span 2;">
                            <label style="font-size:11px; font-weight:700; color:var(--muted);">Bonus (opsional)</label>
                            <input type="text" name="bonus" value="{{ $plan->bonus }}" class="form-control" style="font-size:13px;" placeholder="Contoh: + Akses modul eksklusif">
                        </div>
                        <div style="grid-column:span 2; display:flex; gap:16px;">
                            <label style="display:flex; align-items:center; gap:6px; font-size:13px; cursor:pointer; font-weight:400;">
                                <input type="checkbox" name="is_popular" value="1" {{ $plan->is_popular ? 'checked' : '' }} style="accent-color:var(--primary);"> Tandai Populer
                            </label>
                            <label style="display:flex; align-items:center; gap:6px; font-size:13px; cursor:pointer; font-weight:400;">
                                <input type="checkbox" name="is_active" value="1" {{ $plan->is_active ? 'checked' : '' }} style="accent-color:var(--primary);"> Aktif
                            </label>
                        </div>
                    </div>
                    <div style="display:flex; gap:8px;">
                        <button type="submit" class="btn btn-primary btn-sm">Simpan Perubahan</button>
                        <button type="button" @click="editOpen=false" class="btn btn-outline btn-sm">Batal</button>
                    </div>
                </form>
            </div>
        </div>
        @empty
        <div class="card" style="text-align:center; padding:60px; color:var(--muted);">
            <i class="fas fa-tags" style="font-size:40px; opacity:0.2; display:block; margin-bottom:12px;"></i>
            <p style="font-size:14px; font-weight:600;">Belum ada program.</p>
        </div>
        @endforelse
    </div>

    {{-- Form Tambah --}}
    <div style="position:sticky; top:80px;">
        <div class="card">
            <div style="font-size:14px; font-weight:700; margin-bottom:16px; padding-bottom:10px; border-bottom:1px solid var(--border);">
                Tambah Program Baru
            </div>
            <form method="POST" action="{{ route('admin.plans.store') }}" enctype="multipart/form-data">
                @csrf
                <div class="form-group">
                    <label>Nama Program <span style="color:var(--danger);">*</span></label>
                    <input type="text" name="name" class="form-control" placeholder="TKA Bahasa Inggris 2026" required value="{{ old('name') }}">
                    @error('name') <div style="font-size:11px; color:var(--danger); margin-top:3px;">{{ $message }}</div> @enderror
                </div>
                <div class="form-group">
                    <label>Tipe Program</label>
                    <select name="tier" class="form-control">
                        <option value="regular" {{ old('tier','regular') === 'regular' ? 'selected' : '' }}>Regular</option>
                        <option value="exclusive" {{ old('tier') === 'exclusive' ? 'selected' : '' }}>Exclusive (kuota terbatas)</option>
                    </select>
                </div>

                <div class="form-group">
                    <label>Kelas Program</label>
                    <select name="grade_id" class="form-control">
                        <option value="">Semua Kelas</option>
                        @foreach($grades as $grade)
                            <option value="{{ $grade->id }}" {{ old('grade_id') == $grade->id ? 'selected' : '' }}>{{ $grade->name }}</option>
                        @endforeach
                    </select>
                    <div style="font-size:11px; color:var(--muted); margin-top:4px;">Program hanya tampil untuk siswa pada kelas yang dipilih.</div>
                </div>
                <div style="display:grid; grid-template-columns:1fr 1fr; gap:10px;">
                    <div class="form-group">
                        <label>Harga (Rp) <span style="color:var(--danger);">*</span></label>
                        <input type="number" name="price" class="form-control" placeholder="190000" required value="{{ old('price') }}">
                        @error('price') <div style="font-size:11px; color:var(--danger); margin-top:3px;">{{ $message }}</div> @enderror
                    </div>
                    <div class="form-group">
                        <label>Harga Normal (Rp) <span style="color:var(--danger);">*</span></label>
                        <input type="number" name="original_price" class="form-control" placeholder="190000" required value="{{ old('original_price') }}">
                    </div>
                </div>
                <div style="display:grid; grid-template-columns:1fr 1fr; gap:10px;">
                    <div class="form-group">
                        <label>Durasi (bulan) <span style="color:var(--danger);">*</span></label>
                        <input type="number" name="duration_months" class="form-control" placeholder="3" min="1" required value="{{ old('duration_months', 1) }}">
                    </div>
                    <div class="form-group">
                        <label>Kuota <span style="font-size:11px; color:var(--muted); font-weight:400;">(opsional)</span></label>
                        <input type="number" name="quota" class="form-control" placeholder="kosong = tanpa batas" min="1" value="{{ old('quota') }}">
                    </div>
                </div>
                <div style="display:grid; grid-template-columns:1fr 1fr; gap:10px;">
                    <div class="form-group">
                        <label>Mulai</label>
                        <input type="date" name="start_date" class="form-control" value="{{ old('start_date') }}">
                    </div>
                    <div class="form-group">
                        <label>Selesai</label>
                        <input type="date" name="end_date" class="form-control" value="{{ old('end_date') }}">
                    </div>
                </div>
                <div class="form-group">
                    <label>Gambar Pamflet <span style="font-size:11px; color:var(--muted); font-weight:400;">(maks 4MB)</span></label>
                    <input type="file" name="flyer_image" accept="image/*" class="form-control">
                </div>
                <div class="form-group">
                    <label>Fitur <span style="font-size:11px; color:var(--muted); font-weight:400;">(1 baris = 1 fitur)</span></label>
                    <textarea name="features" class="form-control" rows="5" style="resize:vertical;" placeholder="24 kali pertemuan&#10;Kelas online tiap pekan&#10;Materi PDF">{{ old('features') }}</textarea>
                </div>
                <div class="form-group">
                    <label>Bonus <span style="font-size:11px; color:var(--muted); font-weight:400;">(opsional)</span></label>
                    <input type="text" name="bonus" class="form-control" placeholder="+ Modul eksklusif" value="{{ old('bonus') }}">
                </div>
                <div class="form-group">
                    <label>Urutan</label>
                    <input type="number" name="order" class="form-control" value="{{ old('order', ($plans->max('order') ?? 0) + 1) }}">
                </div>
                <div style="margin-bottom:14px;">
                    <label style="display:flex; align-items:center; gap:8px; cursor:pointer; font-weight:400; font-size:13px;">
                        <input type="checkbox" name="is_popular" value="1" style="accent-color:var(--primary);"> Tandai sebagai Populer
                    </label>
                </div>
                <button type="submit" class="btn btn-primary" style="width:100%; justify-content:center;">
                    <i class="fas fa-plus"></i> Tambah Program
                </button>
            </form>
        </div>
    </div>

</div>

@endsection
