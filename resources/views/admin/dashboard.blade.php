@extends('layouts.app')

@section('title', 'Dashboard Panitia')

@section('content')
<h3 class="mb-4">Dashboard Panitia</h3>

<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="card stat-card p-3 text-center">
            <div class="text-muted small">Total Peserta</div>
            <div class="fs-3 fw-bold">{{ $stats['total_peserta'] }}</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card stat-card p-3 text-center">
            <div class="text-muted small">Total Hadiah</div>
            <div class="fs-3 fw-bold">{{ $stats['total_hadiah'] }}</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card stat-card p-3 text-center">
            <div class="text-muted small">Hadiah Terbagi</div>
            <div class="fs-3 fw-bold">{{ $stats['hadiah_terbagi'] }}</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card stat-card p-3 text-center">
            <div class="text-muted small">Total Pemenang</div>
            <div class="fs-3 fw-bold">{{ $stats['total_pemenang'] }}</div>
        </div>
    </div>
</div>

<div class="card stat-card p-4 mb-4 d-flex flex-row justify-content-between align-items-center">
    <div>
        <strong>Status Registrasi:</strong>
        @if($stats['registrasi_terbuka'])
            <span class="badge bg-success">Terbuka</span>
        @else
            <span class="badge bg-secondary">Terkunci</span>
        @endif
        <p class="text-muted small mb-0 mt-1">Kunci registrasi sebelum pengundian dimulai agar data peserta final.</p>
    </div>
    <form action="{{ route('admin.registration.toggle') }}" method="POST">
        @csrf
        <button class="btn {{ $stats['registrasi_terbuka'] ? 'btn-outline-danger' : 'btn-outline-success' }}">
            {{ $stats['registrasi_terbuka'] ? 'Kunci Registrasi' : 'Buka Registrasi' }}
        </button>
    </form>
</div>

<div class="d-flex gap-2">
    <a href="{{ route('admin.prizes.index') }}" class="btn btn-primary">Kelola Hadiah</a>
    <a href="{{ route('admin.draw.index') }}" class="btn btn-success">Mulai Pengundian</a>
</div>
@endsection
