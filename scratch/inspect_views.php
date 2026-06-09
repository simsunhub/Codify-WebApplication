<?php

$tPath = '/Users/macbook/.gemini/antigravity-ide/brain/a5052164-0d4b-4a80-aaa8-e559984e3d8b/.system_generated/logs/transcript.jsonl';
$handle = fopen($tPath, 'r');
while (($line = fgets($handle)) !== false) {
    $data = json_decode($line, true);
    if (!$data) continue;
    
    // Check tool calls
    if (isset($data['tool_calls'])) {
        foreach ($data['tool_calls'] as $call) {
            if ($call['name'] === 'view_file') {
                $args = $call['args'];
                $ap = str_replace('"', '', $args['AbsolutePath'] ?? '');
                if (strpos($ap, 'teacher/layouts/app.blade.php') !== false) {
                    echo "Step " . ($data['step_index'] ?? 0) . " viewed teacher/layouts/app.blade.php (Start: " . ($args['StartLine'] ?? '') . ", End: " . ($args['EndLine'] ?? '') . ")\n";
                }
            }
        }
    }
}
fclose($handle);
