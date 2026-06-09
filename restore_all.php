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

$bestVersions = [];

foreach ($transcriptPaths as $tPath) {
    if (!file_exists($tPath)) continue;
    echo "Processing transcript: $tPath\n";
    $handle = fopen($tPath, 'r');
    if (!$handle) continue;

    while (($line = fgets($handle)) !== false) {
        $data = json_decode($line, true);
        if (!$data) continue;

        if (isset($data['type']) && $data['type'] === 'VIEW_FILE' && isset($data['content'])) {
            $content = $data['content'];
            foreach ($targets as $target) {
                // Check if content is showing lines of target
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

                    if (!isset($bestVersions[$target])) {
                        $bestVersions[$target] = [];
                    }
                    // Populate bestVersions with any line we don't have yet,
                    // or if the line map has more lines in general.
                    // Actually, let's keep the one that has the most lines,
                    // or merge them if they have disjoint lines (e.g. from paginated views).
                    foreach ($lineMap as $ln => $text) {
                        // Let's store it. In case of duplicate lines, let's keep the one we got first (usually original)
                        if (!isset($bestVersions[$target][$ln])) {
                            $bestVersions[$target][$ln] = $text;
                        }
                    }
                }
            }
        }
    }
    fclose($handle);
}

// Write the files
foreach ($bestVersions as $target => $lineMap) {
    if (empty($lineMap)) continue;
    $maxLine = max(array_keys($lineMap));
    $lines = [];
    for ($i = 1; $i <= $maxLine; $i++) {
        $lines[] = isset($lineMap[$i]) ? $lineMap[$i] : '';
    }
    $code = implode("\n", $lines);
    $fullPath = '/Users/macbook/Herd/eduplatform/' . $target;
    @mkdir(dirname($fullPath), 0755, true);
    file_put_contents($fullPath, $code);
    echo "Restored $target (up to $maxLine lines)\n";
}

echo "Done batch restoration!\n";
