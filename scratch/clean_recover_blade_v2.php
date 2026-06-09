<?php
$transcriptPaths = [
    '/Users/macbook/.gemini/antigravity-ide/brain/a5052164-0d4b-4a80-aaa8-e559984e3d8b/.system_generated/logs/transcript.jsonl',
    '/Users/macbook/.gemini/antigravity-ide/brain/f290ae98-5eb0-4b56-ae0d-8ae13341c3fd/.system_generated/logs/transcript.jsonl',
    '/Users/macbook/.gemini/antigravity-ide/brain/2384698e-790d-4d7b-9244-cf0b1374bad8/.system_generated/logs/transcript.jsonl',
    '/Users/macbook/.gemini/antigravity-ide/brain/39d3ba5d-f44b-4eb1-aecd-5b68c295803a/.system_generated/logs/transcript.jsonl',
    '/Users/macbook/.gemini/antigravity-ide/brain/672b3003-65be-4229-9c04-a2b5e919a1e1/.system_generated/logs/transcript.jsonl',
    '/Users/macbook/.gemini/antigravity-ide/brain/6f9b70cf-cf52-4ad2-92d5-681366fb93b9/.system_generated/logs/transcript.jsonl'
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

    $step = 0;
    while (($line = fgets($handle)) !== false) {
        $data = json_decode($line, true);
        if (!$data) continue;

        $stepIndex = $data['step_index'] ?? $step;

        if (isset($data['type']) && $data['type'] === 'VIEW_FILE' && isset($data['content'])) {
            $content = $data['content'];
            
            foreach ($targets as $target) {
                $expectedPathMarker = 'File Path: `file:///Users/macbook/Herd/eduplatform/' . $target . '`';
                if (strpos($content, $expectedPathMarker) !== false) {
                    $isCorrupted = false;
                    if (strpos($content, 'Результаты реализации') !== false ||
                        strpos($content, 'Добавление мультиязычности') !== false ||
                        strpos($content, '# Walkthrough') !== false ||
                        strpos($content, '## Что было сделано') !== false ||
                        strpos($content, '#### [MODIFY]') !== false ||
                        strpos($content, '#### [NEW]') !== false ||
                        strpos($content, '# Goal Description') !== false ||
                        strpos($content, 'transcriptPaths') !== false ||
                        strpos($content, 'lineMaps') !== false ||
                        strpos($content, 'targets') !== false ||
                        strpos($content, 'rawLines') !== false) {
                        $isCorrupted = true;
                    }
                    
                    if ($target === 'resources/views/teacher/layouts/app.blade.php') {
                        if (strpos($content, '$p->difficulty') !== false || strpos($content, 'attempts_lbl') !== false) {
                            $isCorrupted = true;
                        }
                    }
                    
                    if (!$isCorrupted) {
                        $rawLines = explode("\n", $content);
                        $linesAdded = 0;
                        foreach ($rawLines as $l) {
                            if (preg_match('/^\s*(\d+):\s?(.*)$/', $l, $matches)) {
                                $lineNo = (int)$matches[1];
                                $lineVal = $matches[2];
                                // We keep the latest chronological clean line view
                                if (!isset($lineMaps[$target][$lineNo]) || $stepIndex >= $lineMaps[$target][$lineNo]['step']) {
                                    $lineMaps[$target][$lineNo] = [
                                        'val' => $lineVal,
                                        'step' => $stepIndex
                                    ];
                                    $linesAdded++;
                                }
                            }
                        }
                        echo "  [CLEAN VIEW] Step $stepIndex | Target: $target | Lines added/updated: $linesAdded\n";
                    } else {
                        echo "  [CORRUPTED VIEW] Step $stepIndex | Target: $target - Ignored\n";
                    }
                }
            }
        }
        $step++;
    }
    fclose($handle);
}

// Restore merged files
foreach ($targets as $target) {
    if (isset($lineMaps[$target]) && !empty($lineMaps[$target])) {
        $map = $lineMaps[$target];
        $maxLine = max(array_keys($map));
        $lines = [];
        $gaps = [];
        for ($i = 1; $i <= $maxLine; $i++) {
            if (isset($map[$i])) {
                $lines[] = $map[$i]['val'];
            } else {
                $lines[] = '';
                $gaps[] = $i;
            }
        }
        $code = implode("\n", $lines);
        
        $fullPath = '/Users/macbook/Herd/eduplatform/' . $target;
        @mkdir(dirname($fullPath), 0755, true);
        file_put_contents($fullPath, $code);
        echo "Successfully restored $target (total lines: $maxLine, gaps: " . count($gaps) . ")\n";
        if (!empty($gaps)) {
            echo "  Gaps count: " . count($gaps) . " | Gaps: " . implode(", ", array_slice($gaps, 0, 30)) . "\n";
        }
    } else {
        echo "WARNING: Could not find any line maps for $target!\n";
    }
}
echo "Recovery Done!\n";
