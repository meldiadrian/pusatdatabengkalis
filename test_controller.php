<?php
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';

try {
    $app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();
    $controller = $app->make(\App\Http\Controllers\DownloadController::class);
    echo "Controller loaded successfully!\n";
    echo "Class: " . get_class($controller) . "\n";
} catch (\Exception $e) {
    echo "Error loading controller: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . "\n";
    echo "Line: " . $e->getLine() . "\n";
}
