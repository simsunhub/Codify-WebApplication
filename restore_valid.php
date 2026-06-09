<?php

$transcriptPaths = [
    '/Users/macbook/.gemini/antigravity-ide/brain/a5052164-0d4b-4a80-aaa8-e559984e3d8b/.system_generated/logs/transcript.jsonl',
    '/Users/macbook/.gemini/antigravity-ide/brain/f290ae98-5eb0-4b56-ae0d-8ae13341c3fd/.system_generated/logs/transcript.jsonl',
    '/Users/macbook/.gemini/antigravity-ide/brain/2384698e-790d-4d7b-9244-cf0b1374bad8/.system_generated/logs/transcript.jsonl'
];

$targets = [
    'app/Http/Controllers/Auth/GoogleController.php',
    'app/Http/Controllers/CertificateController.php',
    'app/Http/Controllers/Student/DashboardController.php',
    'app/Http/Controllers/Student/CodingController.php',
    'app/Http/Controllers/Student/CartController.php',
    'app/Http/Controllers/Admin/DashboardController.php',
    'app/Http/Controllers/Admin/CourseController.php',
    'app/Http/Controllers/Teacher/LessonController.php',
    'app/Http/Controllers/Teacher/DashboardController.php'
];

$tempFile = '/Users/macbook/Herd/eduplatform/bootstrap/cache/test_syntax.php';

function checkSyntax($code) {
    global $tempFile;
    file_put_contents($tempFile, $code);
    $output = [];
    $returnVar = 0;
    exec("php -l " . escapeshellarg($tempFile) . " 2>&1", $output, $returnVar);
    return ($returnVar === 0);
}

$candidates = [];

foreach ($transcriptPaths as $tPath) {
    if (!file_exists($tPath)) continue;
    echo "Scanning: $tPath\n";
    $handle = fopen($tPath, 'r');
    if (!$handle) continue;

    while (($line = fgets($handle)) !== false) {
        $data = json_decode($line, true);
        if (!$data) continue;

        $step = $data['step_index'] ?? 0;

        // Check in VIEW_FILE
        if (isset($data['type']) && $data['type'] === 'VIEW_FILE' && isset($data['content'])) {
            $content = $data['content'];
            foreach ($targets as $target) {
                if (strpos($content, $target) !== false && strpos($content, 'Showing lines 1 to') !== false) {
                    $rawLines = explode("\n", $content);
                    $lineMap = [];
                    $hasNumbers = false;
                    foreach ($rawLines as $l) {
                        if (preg_match('/^(\d+): (.*)/', $l, $matches)) {
                            $hasNumbers = true;
                            $lineMap[(int)$matches[1]] = $matches[2];
                        }
                    }
                    if (!$hasNumbers) continue;

                    $maxLine = max(array_keys($lineMap));
                    $lines = [];
                    for ($i = 1; $i <= $maxLine; $i++) {
                        $lines[] = isset($lineMap[$i]) ? $lineMap[$i] : '';
                    }
                    $code = implode("\n", $lines);
                    
                    if (checkSyntax($code)) {
                        $candidates[$target][] = [
                            'code' => $code,
                            'lines' => count($lines),
                            'source' => "VIEW_FILE Step $step in " . basename($tPath)
                        ];
                    }
                }
            }
        }

        // Check in tool calls
        if (isset($data['tool_calls'])) {
            foreach ($data['tool_calls'] as $call) {
                if ($call['name'] === 'write_to_file' || $call['name'] === 'replace_file_content') {
                    $args = $call['args'];
                    $tf = str_replace('"', '', $args['TargetFile'] ?? '');
                    foreach ($targets as $target) {
                        if (strpos($tf, $target) !== false) {
                            $code = $args['CodeContent'] ?? '';
                            if ($code && $code[0] === '"') {
                                $code = json_decode($code);
                            }
                            if ($code && checkSyntax($code)) {
                                $candidates[$target][] = [
                                    'code' => $code,
                                    'lines' => count(explode("\n", $code)),
                                    'source' => "{$call['name']} Step $step in " . basename($tPath)
                                ];
                            }
                        }
                    }
                }
            }
        }
    }
    fclose($handle);
}

@unlink($tempFile);

// For each target, find the best candidate (largest line count)
foreach ($targets as $target) {
    if (!isset($candidates[$target]) || empty($candidates[$target])) {
        echo "WARNING: No syntactically valid candidate found for $target!\n";
        continue;
    }

    // Sort by line count descending
    usort($candidates[$target], function($a, $b) {
        return $b['lines'] <=> $a['lines'];
    });

    $best = $candidates[$target][0];
    $fullPath = '/Users/macbook/Herd/eduplatform/' . $target;
    @mkdir(dirname($fullPath), 0755, true);
    file_put_contents($fullPath, $best['code']);
    echo "Restored $target from {$best['source']} (lines: {$best['lines']})\n";
}

echo "Done syntax-valid restoration!\n";
