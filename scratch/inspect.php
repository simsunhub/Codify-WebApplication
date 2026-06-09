<?php

$tPath = '/Users/macbook/.gemini/antigravity-ide/brain/a5052164-0d4b-4a80-aaa8-e559984e3d8b/.system_generated/logs/transcript.jsonl';
$handle = fopen($tPath, 'r');
while (($line = fgets($handle)) !== false) {
    $data = json_decode($line, true);
    if (!$data) continue;
    if (($data['step_index'] ?? 0) == 4467) {
        echo json_encode($data, JSON_PRETTY_PRINT) . "\n";
    }
}
fclose($handle);
