<?php

$transcriptPaths = [
    '/Users/macbook/.gemini/antigravity-ide/brain/a5052164-0d4b-4a80-aaa8-e559984e3d8b/.system_generated/logs/transcript.jsonl',
    '/Users/macbook/.gemini/antigravity-ide/brain/f290ae98-5eb0-4b56-ae0d-8ae13341c3fd/.system_generated/logs/transcript.jsonl',
    '/Users/macbook/.gemini/antigravity-ide/brain/2384698e-790d-4d7b-9244-cf0b1374bad8/.system_generated/logs/transcript.jsonl'
];

$target = 'app/Http/Controllers/Auth/GoogleController.php';

foreach ($transcriptPaths as $tPath) {
    if (!file_exists($tPath)) continue;
    $handle = fopen($tPath, 'r');
    if (!$handle) continue;

    while (($line = fgets($handle)) !== false) {
        if (strpos($line, 'VIEW_FILE') !== false && strpos($line, 'GoogleController.php') !== false) {
            $data = json_decode($line, true);
            if (!$data) continue;

            $content = $data['content'] ?? '';
            if (strpos($content, 'Showing lines 1 to') !== false) {
                $lines = explode("\n", $content);
                $codeLines = [];
                foreach ($lines as $l) {
                    if (preg_match('/^\d+: (.*)/', $l, $matches)) {
                        $codeLines[] = $matches[1];
                    }
                }
                $code = implode("\n", $codeLines);
                $tempFile = '/Users/macbook/Herd/eduplatform/bootstrap/cache/test_syntax.php';
                file_put_contents($tempFile, $code);
                $output = [];
                $returnVar = 0;
                exec("php -l " . escapeshellarg($tempFile) . " 2>&1", $output, $returnVar);
                unlink($tempFile);
                if ($returnVar === 0) {
                    echo "Found valid GoogleController in Step {$data['step_index']} of " . basename($tPath) . " (" . count($codeLines) . " lines)!\n";
                    file_put_contents('/Users/macbook/Herd/eduplatform/app/Http/Controllers/Auth/GoogleController.php', $code);
                    fclose($handle);
                    exit(0);
                }
            }
        }
    }
    fclose($handle);
}

echo "No valid full version found in any transcript VIEW_FILE.\n";
