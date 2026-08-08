@extends('layouts.app')

@section('title', 'Registrasi Doorprize')

@section('content')
<div class="d-flex justify-content-center align-items-center" style="min-height: 80vh;">
    <div class="card stat-card p-5 text-center" style="max-width: 420px;">
        <h3 class="mb-3">BuTer Doorprize</h3>
        <p class="mb-3">Doorprize Acara Puncak PNPP 2026</p>
        <p class="text-muted mb-4">Login dengan akun Google Anda untuk mendaftar dan mendapatkan nomor undian.</p>
        <a href="{{ route('auth.google') }}" class="btn btn-primary btn-lg">
            Login dengan Google
        </a>
    </div>
</div>
@endsection
