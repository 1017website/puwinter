@php $lc = $liveClass ?? null; @endphp

<div style="display:flex; flex-direction:column; gap:16px;">

    {{-- Judul --}}
    <div class="card">
        <div style="font-size:13px; font-weight:700; margin-bottom:14px; padding-bottom:10px; border-bottom:1px solid var(--border);">
            Informasi Dasar
        </div>
        <div class="form-group">
            <label>Judul Kelas Online <span style="color:var(--danger);">*</span></label>
            <input type="text" name="title" class="form-control" value="{{ old('title', $lc->title ?? '') }}"
                   placeholder="Contoh: Pembahasan TPS Penalaran Umum #3" required>
            @error('title') <div class="text-danger" style="font-size:12px; margin-top:4px;">{{ $message }}</div> @enderror
        </div>

        <div style="display:grid; grid-template-columns:1fr 1fr; gap:14px;">
            <div class="form-group">
                <label>Mata Pelajaran <span style="color:var(--danger);">*</span></label>
                <select name="subject_id" class="form-control" required>
                    <option value="">-- Pilih Mapel --</option>
                    @foreach($subjects as $subject)
                        <option value="{{ $subject->id }}" {{ old('subject_id', $lc->subject_id ?? '') == $subject->id ? 'selected' : '' }}>
                            {{ $subject->name }}
                        </option>
                    @endforeach
                </select>
                @error('subject_id') <div class="text-danger" style="font-size:12px; margin-top:4px;">{{ $message }}</div> @enderror
            </div>
            <div class="form-group">
                <label>Kelas / Tingkat</label>
                <select name="grade_id" class="form-control">
                    <option value="">Semua Kelas</option>
                    @foreach($grades as $g)
                        <option value="{{ $g->id }}" {{ old('grade_id', $lc->grade_id ?? '') == $g->id ? 'selected' : '' }}>{{ $g->name }}</option>
                    @endforeach
                </select>
                <small style="color:var(--muted); font-size:11px;">Kosongkan = tampil untuk semua kelas siswa.</small>
                @error('grade_id') <div class="text-danger" style="font-size:12px; margin-top:4px;">{{ $message }}</div> @enderror
            </div>
            <div class="form-group">
                <label>Program <span style="color:var(--danger);">*</span></label>
                <select name="plan_id" class="form-control">
                    <option value="">— Pilih Program —</option>
                    @foreach($plans as $pl)
                        <option value="{{ $pl->id }}" {{ old('plan_id', $lc->plan_id ?? '') == $pl->id ? 'selected' : '' }}>{{ $pl->name }}</option>
                    @endforeach
                </select>
                <small style="color:var(--muted); font-size:11px;">Kelas online ini milik program mana.</small>
            </div>
            <div class="form-group">
                <label>Akses Untuk <span style="color:var(--danger);">*</span></label>
                <select name="access_tier" class="form-control">
                    <option value="paid" {{ old('access_tier', $lc->access_tier ?? 'paid') === 'paid' ? 'selected' : '' }}>Hanya peserta BERBAYAR</option>
                    <option value="both" {{ old('access_tier', $lc->access_tier ?? '') === 'both' ? 'selected' : '' }}>Semua peserta program</option>
                    <option value="free" {{ old('access_tier', $lc->access_tier ?? '') === 'free' ? 'selected' : '' }}>Hanya gratis</option>
                </select>
                <small style="color:var(--muted); font-size:11px;">Kelas online umumnya manfaat berbayar.</small>
            </div>
            <div class="form-group">
                <label>Mentor <span style="color:var(--danger);">*</span></label>
                <select name="mentor_id" class="form-control" required>
                    <option value="">-- Pilih Mentor --</option>
                    @foreach($mentors as $mentor)
                        <option value="{{ $mentor->id }}" {{ old('mentor_id', $lc->mentor_id ?? '') == $mentor->id ? 'selected' : '' }}>
                            {{ $mentor->name }}
                        </option>
                    @endforeach
                </select>
                @error('mentor_id') <div class="text-danger" style="font-size:12px; margin-top:4px;">{{ $message }}</div> @enderror
            </div>
        </div>

        <div class="form-group">
            <label>Kelas Terkait <span style="color:var(--muted); font-weight:400;">(opsional)</span></label>
            <select name="course_id" class="form-control">
                <option value="">-- Tidak terkait kelas --</option>
                @foreach($courses as $course)
                    <option value="{{ $course->id }}" {{ old('course_id', $lc->course_id ?? '') == $course->id ? 'selected' : '' }}>
                        {{ $course->title }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="form-group">
            <label>Deskripsi</label>
            <textarea name="description" class="form-control" rows="3"
                      placeholder="Apa yang akan dipelajari di sesi ini...">{{ old('description', $lc->description ?? '') }}</textarea>
        </div>
    </div>

    {{-- Jadwal --}}
    <div class="card">
        <div style="font-size:13px; font-weight:700; margin-bottom:14px; padding-bottom:10px; border-bottom:1px solid var(--border);">
            Jadwal & Durasi
        </div>
        <div style="display:grid; grid-template-columns:1fr 1fr; gap:14px;">
            <div class="form-group">
                <label>Tanggal & Waktu Mulai <span style="color:var(--danger);">*</span></label>
                <input type="datetime-local" name="scheduled_at" class="form-control"
                       value="{{ old('scheduled_at', $lc ? $lc->scheduled_at->format('Y-m-d\TH:i') : '') }}" required>
                @error('scheduled_at') <div class="text-danger" style="font-size:12px; margin-top:4px;">{{ $message }}</div> @enderror
            </div>
            <div class="form-group">
                <label>Durasi (menit) <span style="color:var(--danger);">*</span></label>
                <input type="number" name="duration_minutes" class="form-control" min="1"
                       value="{{ old('duration_minutes', $lc->duration_minutes ?? 90) }}" required>
                @error('duration_minutes') <div class="text-danger" style="font-size:12px; margin-top:4px;">{{ $message }}</div> @enderror
            </div>
        </div>
    </div>

    {{-- Zoom --}}
    <div class="card">
        <div style="font-size:13px; font-weight:700; margin-bottom:14px; padding-bottom:10px; border-bottom:1px solid var(--border);">
            <i class="fas fa-video" style="color:#2563EB; margin-right:6px;"></i> Link Zoom
        </div>
        <div style="display:grid; grid-template-columns:1fr 1fr; gap:14px;">
            <div class="form-group">
                <label>Zoom Link</label>
                <input type="url" name="zoom_link" class="form-control"
                       value="{{ old('zoom_link', $lc->zoom_link ?? '') }}"
                       placeholder="https://zoom.us/j/...">
                @error('zoom_link') <div class="text-danger" style="font-size:12px; margin-top:4px;">{{ $message }}</div> @enderror
            </div>
            <div class="form-group">
                <label>Meeting ID <span style="color:var(--muted); font-weight:400;">(opsional)</span></label>
                <input type="text" name="zoom_meeting_id" class="form-control"
                       value="{{ old('zoom_meeting_id', $lc->zoom_meeting_id ?? '') }}"
                       placeholder="123 456 7890">
            </div>
        </div>
    </div>

    {{-- Rekaman (hanya di edit) --}}
    @if($lc)
    <div class="card">
        <div style="font-size:13px; font-weight:700; margin-bottom:14px; padding-bottom:10px; border-bottom:1px solid var(--border);">
            <i class="fas fa-film" style="color:#7C3AED; margin-right:6px;"></i> Rekaman (Setelah Live)
        </div>
        <div class="form-group">
            <label>URL Rekaman</label>
            <input type="url" name="recording_url" class="form-control"
                   value="{{ old('recording_url', $lc->recording_url ?? '') }}"
                   placeholder="https://youtube.com/watch?v=... atau URL video lainnya">
            <div style="font-size:11px; color:var(--muted); margin-top:5px;">
                Mendukung YouTube, Google Drive, atau URL MP4 langsung. Rekaman akan otomatis bisa ditonton siswa setelah kelas selesai.
            </div>
            @error('recording_url') <div class="text-danger" style="font-size:12px; margin-top:4px;">{{ $message }}</div> @enderror
        </div>
    </div>
    @endif

    {{-- Status (hanya di edit) + Setting --}}
    <div class="card" x-data="{ ctype: '{{ old('class_type', $lc->class_type ?? 'regular') }}' }">
        <div style="font-size:13px; font-weight:700; margin-bottom:14px; padding-bottom:10px; border-bottom:1px solid var(--border);">
            Pengaturan
        </div>
        <div style="display:grid; grid-template-columns:1fr 1fr; gap:14px;">
            {{-- Tipe Kelas Online --}}
            <div class="form-group">
                <label>Tipe Kelas Online <span style="color:var(--danger);">*</span></label>
                <select name="class_type" class="form-control" x-model="ctype">
                    <option value="regular" {{ old('class_type', $lc->class_type ?? 'regular') === 'regular' ? 'selected' : '' }}>Reguler</option>
                    <option value="private" {{ old('class_type', $lc->class_type ?? '') === 'private' ? 'selected' : '' }}>Private / Eksklusif — wajib premium</option>
                </select>
                <small style="color:var(--muted); font-size:11px;" x-text="ctype === 'private' ? 'Hanya untuk member premium.' : 'Mengikuti flag premium di samping.'"></small>
                @error('class_type') <div class="text-danger" style="font-size:12px; margin-top:4px;">{{ $message }}</div> @enderror
            </div>

            @if($lc)
            <div class="form-group">
                <label>Status</label>
                <select name="status" class="form-control">
                    <option value="scheduled" {{ old('status', $lc->status) === 'scheduled' ? 'selected' : '' }}>Terjadwal</option>
                    <option value="live"      {{ old('status', $lc->status) === 'live'      ? 'selected' : '' }}>Live</option>
                    <option value="ended"     {{ old('status', $lc->status) === 'ended'     ? 'selected' : '' }}>Selesai</option>
                    <option value="cancelled" {{ old('status', $lc->status) === 'cancelled' ? 'selected' : '' }}>Dibatalkan</option>
                </select>
            </div>
            @endif

            <div class="form-group">
                <label>Tipe Akses</label>
                <div style="display:flex; align-items:center; gap:10px; margin-top:8px;">
                    <label style="display:flex; align-items:center; gap:8px; cursor:pointer; font-weight:400; font-size:13px;">
                        <input type="checkbox" name="is_premium" value="1"
                               {{ old('is_premium', $lc->is_premium ?? false) ? 'checked' : '' }}
                               :disabled="ctype === 'private'"
                               x-bind:checked="ctype === 'private' ? true : $el.checked"
                               style="accent-color:var(--primary); width:16px; height:16px;">
                        <span>Premium only <span style="color:var(--muted); font-size:12px;">(siswa harus berlangganan)</span></span>
                    </label>
                </div>
                <small x-show="ctype === 'private'" style="color:var(--muted); font-size:11px;">Private otomatis premium.</small>
            </div>
        </div>
    </div>

</div>
