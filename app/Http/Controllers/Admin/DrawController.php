<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Participant;
use App\Models\Prize;
use App\Models\Winner;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class DrawController extends Controller
{
    /**
     * Display the draw page.
     */
    public function index(): View
    {
        // Hanya tampilkan hadiah yang belum diundi
        $prizes = Prize::where('status', 'pending')
            ->orderBy('sequence')
            ->get();

        // Tampilkan hanya winner yang masih aktif
        $winners = Winner::with('participant.user', 'prize')
            ->where('is_cancelled', false)
            ->orderByDesc('draw_time')
            ->get();

        // Statistik peserta
        $stats = [
            'total_peserta' => Participant::count(),
            'sudah_menang' => Participant::where('is_winner', true)->count(),
        ];

        return view('admin.draw', compact(
            'prizes',
            'winners',
            'stats'
        ));
    }

    /**
     * Draw a random eligible participant for a prize.
     *
     * A participant can only win once.
     */
    public function draw(Request $request): JsonResponse
    {
        $data = $request->validate([
            'prize_id' => ['required', 'exists:prizes,id'],
        ]);

        try {
            $result = DB::transaction(function () use ($data, $request) {

                /*
                 * Lock the prize so two draw requests cannot
                 * process the same prize simultaneously.
                 */
                $prize = Prize::where('id', $data['prize_id'])
                    ->lockForUpdate()
                    ->firstOrFail();

                /*
                 * Prevent drawing a prize that has already
                 * been successfully drawn.
                 */
                if ($prize->status === 'drawn') {
                    throw new \RuntimeException(
                        'Hadiah ini sudah diundi.'
                    );
                }

                /*
                 * Get participants who have never won.
                 */
                $eligible = Participant::where('is_winner', false)
                    ->pluck('id');

                if ($eligible->isEmpty()) {
                    throw new \RuntimeException(
                        'Tidak ada peserta yang tersisa untuk diundi.'
                    );
                }

                /*
                 * Select a random participant.
                 *
                 * random_int() is used instead of rand()
                 * for a stronger random selection.
                 */
                $winnerParticipantId = $eligible[
                    random_int(0, $eligible->count() - 1)
                ];

                /*
                 * Lock the selected participant before updating.
                 */
                $participant = Participant::with('user')
                    ->lockForUpdate()
                    ->findOrFail($winnerParticipantId);

                /*
                 * Double-check in case the participant became
                 * a winner while the transaction was processing.
                 */
                if ($participant->is_winner) {
                    throw new \RuntimeException(
                        'Peserta yang dipilih sudah pernah memenangkan hadiah. Silakan lakukan undian kembali.'
                    );
                }

                /*
                 * Create the active winner record.
                 */
                $winner = Winner::create([
                    'participant_id' => $participant->id,
                    'prize_id' => $prize->id,
                    'draw_time' => now(),
                    'draw_by' => $request->user()->id,
                    'is_cancelled' => false,
                ]);

                /*
                 * Mark participant as winner.
                 */
                $participant->update([
                    'is_winner' => true,
                ]);

                /*
                 * Mark prize as already drawn.
                 */
                $prize->update([
                    'status' => 'drawn',
                ]);

                /*
                 * Save audit history.
                 */
                AuditLog::record(
                    'draw_executed',
                    "Undian dilakukan: {$participant->user->name} " .
                    "(No. {$participant->ticket_number}) memenangkan " .
                    "'{$prize->name}'."
                );

                return [
                    'ticket_number' => $participant->ticket_number,
                    'name' => $participant->user->name,
                    'prize' => $prize->name,
                ];
            });

            /*
             * Count remaining participants after
             * the transaction has successfully completed.
             */
            $remainingParticipants = Participant::where(
                'is_winner',
                false
            )->count();

            return response()->json([
                'ticket_number' => $result['ticket_number'],
                'name' => $result['name'],
                'prize' => $result['prize'],
                'remaining_participants' => $remainingParticipants,
            ]);

        } catch (\RuntimeException $e) {

            return response()->json([
                'message' => $e->getMessage(),
            ], 422);

        } catch (\Throwable $e) {

            report($e);

            return response()->json([
                'message' => 'Terjadi kesalahan saat melakukan undian.',
            ], 500);
        }
    }

    /**
     * Cancel / redo an existing draw.
     *
     * The old Winner record is deleted so the participant
     * no longer sees the cancelled prize on their dashboard.
     *
     * The history is preserved through AuditLog.
     */
    public function redo(Winner $winner): JsonResponse
    {
        try {

            DB::transaction(function () use ($winner) {

                /*
                 * Lock the winner record so it cannot be
                 * modified by another request simultaneously.
                 */
                $winner = Winner::with([
                    'participant.user',
                    'prize',
                ])
                    ->lockForUpdate()
                    ->findOrFail($winner->id);

                $participant = $winner->participant;
                $prize = $winner->prize;

                /*
                 * Save information before deleting the Winner
                 * record.
                 */
                $participantName = $participant->user->name;
                $ticketNumber = $participant->ticket_number;
                $prizeName = $prize->name;

                /*
                 * Return participant to the eligible pool.
                 */
                $participant->update([
                    'is_winner' => false,
                ]);

                /*
                 * Re-open the prize so it can be drawn again.
                 */
                $prize->update([
                    'status' => 'pending',
                ]);

                /*
                 * Delete the old active winner.
                 *
                 * This is important because the participant's
                 * dashboard uses the Winner relationship.
                 */
                $winner->delete();

                /*
                 * Preserve the history in AuditLog.
                 */
                AuditLog::record(
                    'draw_redo',
                    "Undian untuk hadiah '{$prizeName}' " .
                    "dibatalkan/diundi ulang. " .
                    "Pemenang sebelumnya: {$participantName} " .
                    "(No. {$ticketNumber})."
                );
            });

            return response()->json([
                'message' => 'Undian dibatalkan. Pemenang lama telah dihapus dan hadiah dapat diundi ulang.',
            ]);

        } catch (\Throwable $e) {

            report($e);

            return response()->json([
                'message' => 'Terjadi kesalahan saat membatalkan undian.',
            ], 500);
        }
    }
}