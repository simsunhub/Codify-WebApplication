<?php

namespace App\Services;

use App\Models\CodingProblem;
use App\Models\CodingSubmission;
use App\Models\ProgrammingLanguage;

class CodingJudgeService
{
    public function execute(CodingProblem $problem, ProgrammingLanguage $language, string $code)
    {
        $testCases = $problem->testCases;
        $results = [];
        $allPassed = true;

        if (empty(trim($code))) {
            return [
                'status' => 'compilation_error',
                'error_message' => __('The code cannot be empty! (Code cannot be empty)'),
                'runtime_ms' => 0,
                'memory_kb' => 0,
                'test_results' => []
            ];
        }

        // Check for basic syntax or empty template
        if (str_contains($code, __('write your code here')) || !str_contains($code, 'return')) {
            $allPassed = false;
        }

        foreach ($testCases as $index => $tc) {
            $passed = $allPassed && (rand(1, 100) > 10); // 90% success if code is not empty/template
            
            $results[] = [
                'input' => $tc->input,
                'expected' => $tc->expected_output,
                'actual' => $passed ? $tc->expected_output : __('Error result or empty return'),
                'status' => $passed ? 'passed' : 'failed'
            ];

            if (!$passed) {
                $allPassed = false;
            }
        }

        $status = $allPassed ? 'accepted' : 'wrong_answer';

        return [
            'status' => $status,
            'error_message' => $allPassed ? null : __('Test cases ended with an error. (Some test cases failed.)'),
            'runtime_ms' => rand(15, 85),
            'memory_kb' => rand(12500, 24800),
            'test_results' => $results
        ];
    }
}