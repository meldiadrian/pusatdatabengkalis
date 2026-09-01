<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DownloadController;

// Route::get('/', function () {
//     return view('welcome');
// });

Route::get('/', function () {
    return redirect('/bulubabi');
});


// Route::middleware('throttle:10,1')->get('/', function () {
//     return redirect('/admin');
// });


// Download routes — dilindungi middleware BlockDangerousFileUpload + throttle
// agar file PHP / script berbahaya tidak bisa diakses walau ada di storage
// throttle:30,1 = maksimal 30 request per menit per IP
Route::middleware([\App\Http\Middleware\BlockDangerousFileUpload::class, 'throttle:10,1'])->group(function () {

    // Download file route - with proper encoding
    Route::get('/download/{filename}', [DownloadController::class, 'downloadFile'])
        ->where('filename', '.+')
        ->name('download.file');

    // Fallback download-file route
    Route::get('/download-file', [DownloadController::class, 'downloadFileQuery'])->name('download.storage.file');

});
