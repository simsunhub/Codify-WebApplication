<?php

$tPath = '/Users/macbook/.gemini/antigravity-ide/brain/a5052164-0d4b-4a80-aaa8-e559984e3d8b/.system_generated/logs/transcript.jsonl';
$handle = fopen($tPath, 'r');
while (($line = fgets($handle)) !== false) {
    $data = json_decode($line, true);
    if (!$data) continue;
    if (($data['step_index'] ?? 0) == 3393) {
        $content = $data['content'] ?? '';
        $rawLines = explode("\n", $content);
        $lineMap = [];
        foreach ($rawLines as $l) {
            if (preg_match('/^(\d+): (.*)/', $l, $matches)) {
                $lineMap[(int)$matches[1]] = $matches[2];
            }
        }
        $maxLine = max(array_keys($lineMap));
        $lines = [];
        for ($i = 1; $i <= $maxLine; $i++) {
            $lines[] = isset($lineMap[$i]) ? $lineMap[$i] : '';
        }
        file_put_contents('/Users/macbook/Herd/eduplatform/scratch/step_3393_code.blade.php', implode("\n", $lines));
        echo "Saved step 3393 code (lines: " . count($lines) . ")\n";
    }
}
fclose($handle);
