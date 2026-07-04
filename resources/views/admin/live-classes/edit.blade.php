@extends('admin.layouts.app')
@section('title', 'Edit Kelas Online')

@section('content')

<div class="page-header">
    <div>
        <h2>Edit Kelas Online</h2>
        <p>{{ $liveClass->title }}</p>
    </div>
    <div style="display:flex; gap:8px;">
        {{-- Quick status --}}
        @if($liveClass->status === 'scheduled')
            <form method="POST" action="{{ route('admin.live-classes.set-status', $liveClass) }}">
                @csrf @method('PATCH')
                <input type="hidden" name="status" value="live">
                <button type="submit" class="btn btn-danger">
                    <i class="fas fa-play"></i> Go Live Sekarang
                </button>
            </form>
        @elseif($liveClass->status === 'live')
            <form method="POST" action="{{ route('admin.live-classes.set-status', $liveClass) }}">
                @csrf @method('PATCH')
                <input type="hidden" name="status" value="ended">
                <button type="submit" class="btn btn-outline">
                    <i class="fas fa-stop"></i> Akhiri Live
                </button>
            </form>
        @endif
        <a href="{{ route('admin.live-classes.index') }}" class="btn btn-outline">
            <i class="fas fa-arrow-left"></i> Kembali
        </a>
    </div>
</div>

<div style="max-width:760px;">
    <form method="POST" action="{{ route('admin.live-classes.update', $liveClass) }}">
        @csrf @method('PUT')
        @include('admin.live-classes._form', ['liveClass' => $liveClass])
        <div style="display:flex; gap:10px; margin-top:24px;">
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save"></i> Simpan Perubahan
            </button>
            <a href="{{ route('admin.live-classes.index') }}" class="btn btn-outline">Batal</a>
        </div>
    </form>
</div>

@endsection
