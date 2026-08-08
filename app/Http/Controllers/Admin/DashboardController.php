<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Participant;
use App\Models\Prize;
use App\Models\Setting;
use App\Models\Winner;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $stats = [
            'total_peserta' => Participant::count(),
            'registrasi_terbuka' => Setting::registrationOpen(),
            'total_hadiah' => Prize::count(),
            'hadiah_terbagi' => Prize::where('status', 'drawn')->count(),
            'total_pemenang' => Winner::where('is_cancelled', false)->count(),
        ];

        return view('admin.dashboard', compact('stats'));
    }

    public function toggleRegistration(): RedirectResponse
    {
        $isOpen = Setting::registrationOpen();
        Setting::set('registration_open', $isOpen ? '0' : '1');

        AuditLog::record(
            $isOpen ? 'registration_closed' : 'registration_opened',
            $isOpen ? 'Registrasi ditutup/dikunci oleh panitia.' : 'Registrasi dibuka oleh panitia.'
        );

        return back()->with('status', $isOpen ? 'Registrasi telah dikunci.' : 'Registrasi dibuka.');
    }
}
