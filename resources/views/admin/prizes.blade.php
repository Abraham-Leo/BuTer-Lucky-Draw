@extends('layouts.app')

@section('title', 'Kelola Hadiah')

@section('content')
<h3 class="mb-4">Kelola Hadiah</h3>

<div class="card stat-card p-4 mb-4">
    <form action="{{ route('admin.prizes.store') }}" method="POST" class="row g-2">
        @csrf
        <div class="col-md-6">
            <input type="text" name="name" class="form-control" placeholder="Nama hadiah (contoh: TV LED)" required>
        </div>
        <div class="col-md-3">
            <input type="number" name="sequence" class="form-control" placeholder="Urutan (opsional)">
        </div>
        <div class="col-md-3">
            <button class="btn btn-primary w-100">Tambah Hadiah</button>
        </div>
    </form>
</div>

<div class="card stat-card p-0">
    <table class="table mb-0">
        <thead>
            <tr>
                <th>#</th>
                <th>Nama Hadiah</th>
                <th>Urutan</th>
                <th>Status</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            @forelse($prizes as $prize)
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ $prize->name }}</td>
                <td>{{ $prize->sequence }}</td>
                <td>
                    @if($prize->status === 'drawn')
                        <span class="badge bg-success">Sudah Diundi</span>
                    @else
                        <span class="badge bg-secondary">Belum</span>
                    @endif
                </td>
                <td>
                    <form action="{{ route('admin.prizes.destroy', $prize) }}" method="POST" class="d-inline">
                        @csrf @method('DELETE')
                        <button class="btn btn-sm btn-outline-danger" onclick="return confirm('Hapus hadiah ini?')">Hapus</button>
                    </form>
                </td>
            </tr>
            @empty
            <tr><td colspan="5" class="text-center text-muted py-4">Belum ada hadiah.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
