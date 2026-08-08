<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;

class ParticipantController extends Controller
{
    public function dashboard(Request $request): View
    {
        $participant = $request->user()->participant()->with('winner.prize')->first();

        return view('participant.dashboard', compact('participant'));
    }
}
