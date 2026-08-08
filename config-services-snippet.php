<?php
/*
 * Tambahkan blok 'google' berikut ke array yang dikembalikan
 * config/services.php
 */

'google' => [
    'client_id' => env('GOOGLE_CLIENT_ID'),
    'client_secret' => env('GOOGLE_CLIENT_SECRET'),
    'redirect' => env('GOOGLE_REDIRECT_URI'),
],
