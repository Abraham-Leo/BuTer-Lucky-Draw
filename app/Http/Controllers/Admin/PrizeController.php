<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Prize;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PrizeController extends Controller
{
    public function index(): View
    {
        $prizes = Prize::orderBy('sequence')->get();

        return view('admin.prizes', compact('prizes'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'sequence' => ['nullable', 'integer', 'min:0'],
        ]);

        $prize = Prize::create([
            'name' => $data['name'],
            'sequence' => $data['sequence'] ?? (Prize::max('sequence') + 1),
            'status' => 'pending',
        ]);

        AuditLog::record('prize_added', "Hadiah '{$prize->name}' ditambahkan.");

        return back()->with('status', 'Hadiah ditambahkan.');
    }

    public function update(Request $request, Prize $prize): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'sequence' => ['nullable', 'integer', 'min:0'],
        ]);

        $prize->update($data);

        return back()->with('status', 'Hadiah diperbarui.');
    }

    public function destroy(Prize $prize): RedirectResponse
    {
        $prize->delete();
        AuditLog::record('prize_deleted', "Hadiah '{$prize->name}' dihapus.");

        return back()->with('status', 'Hadiah dihapus.');
    }
}
