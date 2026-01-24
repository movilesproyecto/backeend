<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Department;

$departments = Department::limit(2)->get();

foreach ($departments as $dept) {
    echo "=== Department: {$dept->name} ===" . PHP_EOL;
    echo "ID: {$dept->id}" . PHP_EOL;

    $imageBinary = $dept->images_binary;
    if (is_resource($imageBinary)) {
        $imageBinary = stream_get_contents($imageBinary);
    }

    if ($imageBinary) {
        echo "Images_binary: YES (" . strlen($imageBinary) . " chars)" . PHP_EOL;
        echo "Preview: " . substr($imageBinary, 0, 50) . "..." . PHP_EOL;
    } else {
        echo "Images_binary: NO" . PHP_EOL;
    }
    echo PHP_EOL;
}
