<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Participant;
use App\Models\Prize;
use App\Models\Winner;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DrawController extends Controller
{
    public function index(): View
    {
        $prizes = Prize::where('status', 'pending')->orderBy('sequence')->get();
        $winners = Winner::with('participant.user', 'prize')
            ->where('is_cancelled', false)
            ->orderByDesc('draw_time')
            ->get();

        $stats = [
            'total_peserta' => Participant::count(),
            'sudah_menang' => Participant::where('is_winner', true)->count(),
        ];

        return view('admin.draw', compact('prizes', 'winners', 'stats'));
    }

    /**
     * Pick a random, still-eligible participant for the given prize and
     * record the win. Uses random_int() — never rand() — per the spec.
     */
    public function draw(Request $request): JsonResponse
    {
        $data = $request->validate([
            'prize_id' => ['required', 'exists:prizes,id'],
        ]);

        $prize = Prize::findOrFail($data['prize_id']);

        if ($prize->status === 'drawn') {
            return response()->json(['message' => 'Hadiah ini sudah diundi.'], 422);
        }

        $eligible = Participant::where('is_winner', false)->pluck('id');

        if ($eligible->isEmpty()) {
            return response()->json(['message' => 'Tidak ada peserta yang tersisa untuk diundi.'], 422);
        }

        $winnerParticipantId = $eligible[random_int(0, $eligible->count() - 1)];
        $participant = Participant::with('user')->findOrFail($winnerParticipantId);

        $winner = Winner::create([
            'participant_id' => $participant->id,
            'prize_id' => $prize->id,
            'draw_time' => now(),
            'draw_by' => $request->user()->id,
        ]);

        $participant->update(['is_winner' => true]);
        $prize->update(['status' => 'drawn']);

        AuditLog::record(
            'draw_executed',
            "Undian dilakukan: {$participant->user->name} (No. {$participant->ticket_number}) memenangkan '{$prize->name}'."
        );

        return response()->json([
            'ticket_number' => $participant->ticket_number,
            'name' => $participant->user->name,
            'prize' => $prize->name,
            'remaining_participants' => Participant::where('is_winner', false)->count(),
        ]);
    }

    /**
     * Cancel / redo a draw: unmark the participant as winner and reopen
     * the prize so it can be drawn again.
     */
    public function redo(Winner $winner): JsonResponse
    {
        $winner->update(['is_cancelled' => true]);
        $winner->participant->update(['is_winner' => false]);
        $winner->prize->update(['status' => 'pending']);

        AuditLog::record(
            'draw_redo',
            "Undian untuk hadiah '{$winner->prize->name}' dibatalkan/diundi ulang."
        );

        return response()->json(['message' => 'Undian dibatalkan, hadiah dapat diundi ulang.']);
    }
}
