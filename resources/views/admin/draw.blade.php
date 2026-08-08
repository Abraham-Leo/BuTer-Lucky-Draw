@extends('layouts.app')

@section('title', 'Pengundian Doorprize')

@section('content')
<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="card stat-card p-3 text-center">
            <div class="text-muted small">Total Peserta</div>
            <div class="fs-3 fw-bold" id="stat-total">{{ $stats['total_peserta'] }}</div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card stat-card p-3 text-center">
            <div class="text-muted small">Sudah Menang</div>
            <div class="fs-3 fw-bold" id="stat-winners">{{ $stats['sudah_menang'] }}</div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card stat-card p-3 text-center">
            <div class="text-muted small">Sisa Peserta</div>
            <div class="fs-3 fw-bold" id="stat-remaining">{{ $stats['total_peserta'] - $stats['sudah_menang'] }}</div>
        </div>
    </div>
</div>

<div class="card stat-card p-5 text-center mb-4">
    <select id="prize-select" class="form-select mb-4" style="max-width: 400px; margin: 0 auto;">
        @forelse($prizes as $prize)
            <option value="{{ $prize->id }}">{{ $prize->name }}</option>
        @empty
            <option disabled>Tidak ada hadiah tersisa</option>
        @endforelse
    </select>

    <div class="ticket-number mb-3" id="draw-display">----</div>
    <div id="draw-result" class="mb-3"></div>

    <button id="start-btn" class="btn btn-success btn-lg px-5" @if($prizes->isEmpty()) disabled @endif>
        START
    </button>
</div>

<h5>Riwayat Pemenang</h5>
<div class="card stat-card p-0">
    <table class="table mb-0" id="winners-table">
        <thead>
            <tr><th>Nomor</th><th>Nama</th><th>Hadiah</th><th>Waktu</th><th></th></tr>
        </thead>
        <tbody>
            @forelse($winners as $winner)
            <tr data-winner-id="{{ $winner->id }}">
                <td>{{ $winner->participant->ticket_number }}</td>
                <td>{{ $winner->participant->user->name }}</td>
                <td>{{ $winner->prize->name }}</td>
                <td>{{ $winner->draw_time->format('H:i') }}</td>
                <td>
                    <button class="btn btn-sm btn-outline-warning redo-btn" data-id="{{ $winner->id }}">Undi Ulang</button>
                </td>
            </tr>
            @empty
            <tr><td colspan="5" class="text-center text-muted py-4">Belum ada pemenang.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>

@push('scripts')
<script>
const startBtn = document.getElementById('start-btn');
const display = document.getElementById('draw-display');
const resultBox = document.getElementById('draw-result');
const prizeSelect = document.getElementById('prize-select');

startBtn.addEventListener('click', async () => {
    if (!prizeSelect.value) return;
    startBtn.disabled = true;
    resultBox.innerHTML = '';

    // Animasi angka acak sebelum hasil akhir muncul
    let ticks = 0;
    const anim = setInterval(() => {
        display.textContent = String(Math.floor(Math.random() * 10000)).padStart(4, '0');
        ticks++;
        if (ticks > 15) clearInterval(anim);
    }, 80);

    try {
        const res = await fetch('{{ route("admin.draw.execute") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
            },
            body: JSON.stringify({ prize_id: prizeSelect.value }),
        });
        const data = await res.json();

        setTimeout(() => {
            clearInterval(anim);
            if (!res.ok) {
                resultBox.innerHTML = `<div class="alert alert-danger">${data.message}</div>`;
                display.textContent = '----';
                startBtn.disabled = false;
                return;
            }

            display.textContent = data.ticket_number;
            resultBox.innerHTML = `<div class="alert alert-success">🎉 <strong>${data.name}</strong> memenangkan <strong>${data.prize}</strong></div>`;

            document.getElementById('stat-winners').textContent = parseInt(document.getElementById('stat-winners').textContent) + 1;
            document.getElementById('stat-remaining').textContent = data.remaining_participants;

            // Hapus opsi hadiah yang sudah diundi & baris baru ke tabel riwayat
            prizeSelect.querySelector(`option[value="${prizeSelect.value}"]`)?.remove();
            if (prizeSelect.options.length === 0) startBtn.disabled = true;
            else startBtn.disabled = false;

            location.reload(); // refresh sederhana agar riwayat & status hadiah sinkron
        }, 1300);
    } catch (e) {
        clearInterval(anim);
        resultBox.innerHTML = `<div class="alert alert-danger">Terjadi kesalahan. Coba lagi.</div>`;
        startBtn.disabled = false;
    }
});

document.querySelectorAll('.redo-btn').forEach(btn => {
    btn.addEventListener('click', async () => {
        if (!confirm('Batalkan undian ini dan undi ulang?')) return;
        const id = btn.dataset.id;
        await fetch(`/admin/draw/${id}/redo`, {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
        });
        location.reload();
    });
});
</script>
@endpush
@endsection
