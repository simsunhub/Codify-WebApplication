<?php
$logPath = '/Users/macbook/.gemini/antigravity-ide/brain/a5052164-0d4b-4a80-aaa8-e559984e3d8b/.system_generated/logs/transcript.jsonl';
$handle = fopen($logPath, 'r');
if (!$handle) {
    die("Could not open log file.\n");
}
while (($line = fgets($handle)) !== false) {
    if (strpos($line, 'admin/users/index.blade.php') !== false) {
        $data = json_decode($line, true);
        if (!$data) continue;
        
        $type = $data['type'] ?? '';
        $step = $data['step_index'] ?? 0;
        
        echo "Found reference to admin/users/index.blade.php at step $step (type: $type)\n";
        
        // Check if this step has content
        $content = $data['content'] ?? '';
        if (strpos($content, '@extends') !== false && strpos($content, 'users') !== false) {
            echo "--> Found view file content in step $step\n";
            file_put_contents("recovered_users_index_step_$step.php", $content);
        }
        
        // Also check inside tool calls
        if (isset($data['tool_calls'])) {
            foreach ($data['tool_calls'] as $tc) {
                $args = $tc['args'] ?? [];
                $codeContent = $args['CodeContent'] ?? $args['ReplacementContent'] ?? '';
                if (strpos($codeContent, '@extends') !== false && strpos($codeContent, 'users') !== false) {
                    echo "--> Found view file content in tool call args at step $step\n";
                    file_put_contents("recovered_users_index_tool_step_$step.php", $codeContent);
                }
            }
        }
    }
}
fclose($handle);
echo "Done.\n";
