<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DownloadController;

// Route::get('/', function () {
//     return view('welcome');
// });

Route::get('/', function () {
    return redirect('/admin');
});



// Download file route - with proper encoding
Route::get('/download/{filename}', [DownloadController::class, 'downloadFile'])
    ->where('filename', '.+')
    ->name('download.file');

// Fallback download-file route
Route::get('/download-file', [DownloadController::class, 'downloadFileQuery'])->name('download.storage.file');
