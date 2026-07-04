@extends('admin.layouts.app')
@section('title', 'Buat Kelas Online')

@section('content')

<div class="page-header">
    <div>
        <h2>Buat Kelas Online</h2>
        <p>Tambahkan jadwal kelas online baru.</p>
    </div>
    <a href="{{ route('admin.live-classes.index') }}" class="btn btn-outline">
        <i class="fas fa-arrow-left"></i> Kembali
    </a>
</div>

<div style="max-width:760px;">
    <form method="POST" action="{{ route('admin.live-classes.store') }}">
        @csrf
        @include('admin.live-classes._form')
        <div style="display:flex; gap:10px; margin-top:24px;">
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save"></i> Simpan Kelas Online
            </button>
            <a href="{{ route('admin.live-classes.index') }}" class="btn btn-outline">Batal</a>
        </div>
    </form>
</div>

@endsection
