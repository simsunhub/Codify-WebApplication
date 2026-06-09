<?php
$transcriptPaths = [
    '/Users/macbook/.gemini/antigravity-ide/brain/a5052164-0d4b-4a80-aaa8-e559984e3d8b/.system_generated/logs/transcript.jsonl',
    '/Users/macbook/.gemini/antigravity-ide/brain/f290ae98-5eb0-4b56-ae0d-8ae13341c3fd/.system_generated/logs/transcript.jsonl',
    '/Users/macbook/.gemini/antigravity-ide/brain/2384698e-790d-4d7b-9244-cf0b1374bad8/.system_generated/logs/transcript.jsonl'
];

foreach ($transcriptPaths as $tPath) {
    if (!file_exists($tPath)) continue;
    $handle = fopen($tPath, 'r');
    $index = 0;
    while (($line = fgets($handle)) !== false) {
        if (strpos($line, 'quizzes/result.blade.php') !== false) {
            $data = json_decode($line, true);
            if ($data && isset($data['content'])) {
                $content = $data['content'];
                echo "Found in " . basename(dirname(dirname($tPath))) . " line $index | len: " . strlen($content) . "\n";
                echo substr($content, 0, 300) . "\n...\n\n";
            }
        }
        $index++;
    }
    fclose($handle);
}
