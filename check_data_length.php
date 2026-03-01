<?php
require __DIR__ . '/vendor/autoload.php';

$app = require __DIR__ . '/bootstrap/app.php';
$app->make('bootstrap/app.php');
$app = require __DIR__ . '/bootstrap/app.php';

// Use raw DB query
$db = \Illuminate\Support\Facades\DB::connection();
$result = $db->select("SELECT id, LENGTH(root_data) as full_len, LENGTH(root_brief_data) as brief_len FROM brands WHERE id = 1");

foreach ($result as $row) {
    echo "Brand ID: {$row->id}\n";
    echo "Full content length: {$row->full_len}\n";
    echo "Brief content length: {$row->brief_len}\n";
    echo "Ratio: " . round(($row->brief_len / $row->full_len * 100), 2) . "%\n";
}
