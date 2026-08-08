@extends('layouts.app')

@section('title', 'Nomor Undian Anda')

@section('content')
<div class="d-flex justify-content-center align-items-center" style="min-height: 70vh;">
    <div class="card stat-card p-5 text-center" style="max-width: 480px;">
        <p class="text-muted mb-1">Nomor Undian Anda</p>
        <div class="ticket-number text-primary mb-3">{{ $participant->ticket_number }}</div>

        @if($participant->is_winner && $participant->winner)
            <div class="alert alert-success">
                🎊 Selamat! Anda memenangkan <strong>{{ $participant->winner->prize->name }}</strong>
            </div>
        @else
            <p class="text-muted">Simpan nomor ini. Anda tidak perlu melakukan apa pun lagi — cukup tunggu proses pengundian.</p>
        @endif

        <small class="text-muted">Terdaftar: {{ $participant->registered_at->translatedFormat('d M Y H:i') }}</small>
    </div>
</div>
@endsection
