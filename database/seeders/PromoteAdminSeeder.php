<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class PromoteAdminSeeder extends Seeder
{
    /**
     * Jalankan: php artisan db:seed --class=PromoteAdminSeeder
     * setelah Anda login sekali via Google agar akun Anda tercatat,
     * lalu ganti email di bawah ini dan jalankan seeder ini untuk
     * menaikkan role menjadi admin.
     */
    public function run(): void
    {
        User::where('email', 'bramleo1969@gmail.com')
            ->update(['role' => 'admin']);
    }
}
