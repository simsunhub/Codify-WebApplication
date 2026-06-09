<?php

$transcriptPaths = [
    '/Users/macbook/.gemini/antigravity-ide/brain/a5052164-0d4b-4a80-aaa8-e559984e3d8b/.system_generated/logs/transcript.jsonl',
    '/Users/macbook/.gemini/antigravity-ide/brain/f290ae98-5eb0-4b56-ae0d-8ae13341c3fd/.system_generated/logs/transcript.jsonl',
    '/Users/macbook/.gemini/antigravity-ide/brain/2384698e-790d-4d7b-9244-cf0b1374bad8/.system_generated/logs/transcript.jsonl'
];

$target = 'CodingController.php';

foreach ($transcriptPaths as $tPath) {
    if (!file_exists($tPath)) continue;
    $handle = fopen($tPath, 'r');
    while (($line = fgets($handle)) !== false) {
        if (strpos($line, $target) !== false) {
            $data = json_decode($line, true);
            if (!$data) continue;

            $content = $data['content'] ?? '';
            if ($data['type'] === 'VIEW_FILE') {
                echo "Step {$data['step_index']} viewed $target: " . substr($content, 0, 150) . "...\n";
                // Let's print out lines around the gap (line 40 to 85) if they exist
                $lines = explode("\n", $content);
                foreach ($lines as $l) {
                    if (preg_match('/^(\d+): (.*)/', $l, $matches)) {
                        $ln = (int)$matches[1];
                        if ($ln >= 40 && $ln <= 85) {
                            echo "  $ln: {$matches[2]}\n";
                        }
                    }
                }
            }
        }
    }
    fclose($handle);
}
