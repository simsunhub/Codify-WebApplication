<?php

$transcriptPaths = [
    '/Users/macbook/.gemini/antigravity-ide/brain/a5052164-0d4b-4a80-aaa8-e559984e3d8b/.system_generated/logs/transcript.jsonl',
    '/Users/macbook/.gemini/antigravity-ide/brain/f290ae98-5eb0-4b56-ae0d-8ae13341c3fd/.system_generated/logs/transcript.jsonl',
    '/Users/macbook/.gemini/antigravity-ide/brain/2384698e-790d-4d7b-9244-cf0b1374bad8/.system_generated/logs/transcript.jsonl'
];

$targets = [
    'resources/views/teacher/dashboard.blade.php',
    'resources/views/teacher/layouts/app.blade.php'
];

$lineMaps = [];

foreach ($transcriptPaths as $tPath) {
    if (!file_exists($tPath)) continue;
    echo "Scanning: $tPath\n";
    $handle = fopen($tPath, 'r');
    if (!$handle) continue;

    while (($line = fgets($handle)) !== false) {
        $data = json_decode($line, true);
        if (!$data) continue;

        // Check in VIEW_FILE
        if (isset($data['type']) && $data['type'] === 'VIEW_FILE' && isset($data['content'])) {
            $content = $data['content'];
            foreach ($targets as $target) {
                if (strpos($content, $target) !== false) {
                    $rawLines = explode("\n", $content);
                    foreach ($rawLines as $l) {
                        if (preg_match('/^(\d+): (.*)/', $l, $matches)) {
                            $lineNo = (int)$matches[1];
                            $lineVal = $matches[2];
                            $lineMaps[$target][$lineNo] = $lineVal;
                        }
                    }
                }
            }
        }
    }
    fclose($handle);
}

// Restore merged files
foreach ($targets as $target) {
    if (isset($lineMaps[$target]) && !empty($lineMaps[$target])) {
        $map = $lineMaps[$target];
        $maxLine = max(array_keys($map));
        $lines = [];
        for ($i = 1; $i <= $maxLine; $i++) {
            $lines[] = isset($map[$i]) ? $map[$i] : '';
        }
        $code = implode("\n", $lines);
        
        $fullPath = '/Users/macbook/Herd/eduplatform/' . $target;
        @mkdir(dirname($fullPath), 0755, true);
        file_put_contents($fullPath, $code);
        echo "Successfully restored $target from merged line maps (total lines: $maxLine)\n";
    } else {
        echo "WARNING: Could not find any line maps for $target!\n";
    }
}
echo "Done!\n";
