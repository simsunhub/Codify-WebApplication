<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AiController extends Controller
{
    /**
     * Send student message to AI Provider (Gemini or OpenAI).
     */
    public function sendMessage(Request $request)
    {
        $request->validate([
            'message' => 'required|string|max:5000',
        ]);

        $userMessage = $request->input('message');
        $provider = strtolower(env('AI_PROVIDER', 'gemini'));
        $apiKey = env('AI_API_KEY', 'your_api_key_here');

        $systemPrompt = __('messages.ai.system_prompt');

        // Check for placeholder/empty API Key to return a useful mock response
        if (empty($apiKey) || $apiKey === 'your_api_key_here') {
            return response()->json([
                'success' => true,
                'reply' => $this->getMockResponse($userMessage),
            ]);
        }

        try {
            if ($provider === 'openai') {
                $response = Http::withHeaders([
                    'Authorization' => 'Bearer ' . $apiKey,
                    'Content-Type' => 'application/json',
                ])->post('https://api.openai.com/v1/chat/completions', [
                    'model' => 'gpt-4o-mini',
                    'messages' => [
                        ['role' => 'system', 'content' => $systemPrompt],
                        ['role' => 'user', 'content' => $userMessage],
                    ],
                    'temperature' => 0.7,
                ]);

                if ($response->successful()) {
                    $reply = $response->json('choices.0.message.content');
                    return response()->json(['success' => true, 'reply' => $reply]);
                }
            } elseif ($provider === 'yandex') {
                // Yandex GPT: Exchange OAuth token for IAM Token
                $iamResponse = Http::post('https://iam.api.cloud.yandex.net/iam/v1/tokens', [
                    'yandexPassportOauthToken' => $apiKey,
                ]);

                if ($iamResponse->successful()) {
                    $iamToken = $iamResponse->json('iamToken');

                    // Detect Folder ID if not configured in .env
                    $folderId = env('AI_FOLDER_ID');
                    if (empty($folderId)) {
                        $foldersResponse = Http::withHeaders([
                            'Authorization' => 'Bearer ' . $iamToken,
                        ])->get('https://resource-manager.api.cloud.yandex.net/resource-manager/v1/folders');

                        if ($foldersResponse->successful()) {
                            $folderId = $foldersResponse->json('folders.0.id');
                        }
                    }

                    if (empty($folderId)) {
                        return response()->json([
                            'success' => true,
                            'reply' => __('messages.ai.error_yandex_folder'),
                        ]);
                    }

                    $response = Http::withHeaders([
                        'Authorization' => 'Bearer ' . $iamToken,
                        'Content-Type' => 'application/json',
                    ])->post('https://llm.api.cloud.yandex.net/foundationModels/v1/completion', [
                        'modelUri' => "gpt://{$folderId}/yandexgpt/latest",
                        'completionOptions' => [
                            'stream' => false,
                            'temperature' => 0.6,
                            'maxTokens' => 2000
                        ],
                        'messages' => [
                            ['role' => 'system', 'text' => $systemPrompt],
                            ['role' => 'user', 'text' => $userMessage],
                        ]
                    ]);

                    if ($response->successful()) {
                        $reply = $response->json('result.alternatives.0.message.text');
                        return response()->json(['success' => true, 'reply' => $reply]);
                    }
                } else {
                    $response = $iamResponse; // Use iamResponse to log error if exchange fails
                }
            } else {
                // Default provider: gemini
                $response = Http::withHeaders([
                    'Content-Type' => 'application/json',
                ])->post("https://generativelanguage.googleapis.com/v1beta/models/gemini-3.1-flash-lite:generateContent?key={$apiKey}", [
                    'contents' => [
                        [
                            'parts' => [
                                ['text' => $userMessage]
                            ]
                        ]
                    ],
                    'systemInstruction' => [
                        'parts' => [
                            ['text' => $systemPrompt]
                        ]
                    ],
                    'generationConfig' => [
                        'temperature' => 0.7,
                    ]
                ]);

                if ($response->successful()) {
                    $reply = $response->json('candidates.0.content.parts.0.text');
                    return response()->json(['success' => true, 'reply' => $reply]);
                }
            }

            // If API response is not successful, log it and fallback
            Log::warning("AI API request failed: Code " . $response->status() . " - " . $response->body());
            return response()->json([
                'success' => true,
                'reply' => __('messages.ai.error_api_failed', [
                    'provider' => $provider,
                    'mock' => $this->getMockResponse($userMessage),
                ]),
            ]);

        } catch (\Exception $e) {
            Log::error("AI Chat Exception: " . $e->getMessage());
            return response()->json([
                'success' => true,
                'reply' => __('messages.ai.error_connection', [
                    'mock' => $this->getMockResponse($userMessage),
                ]),
            ]);
        }
    }

    /**
     * Provide a high-quality simulated response when no API key is available.
     */
    private function getMockResponse(string $message): string
    {
        $message = mb_strtolower($message);

        // Check greeting
        if (str_contains($message, 'merhaba') || str_contains($message, 'selam') || str_contains($message, 'hello') || str_contains($message, 'hi') || str_contains($message, 'привет') || str_contains($message, 'здравствуй')) {
            return __('messages.ai.mock_hello');
        }

        // Check code
        if (str_contains($message, 'kod') || str_contains($message, 'code') || str_contains($message, 'css') || str_contains($message, 'html') || str_contains($message, 'javascript') || str_contains($message, 'php') || str_contains($message, 'python') || str_contains($message, 'program')) {
            return __('messages.ai.mock_code');
        }

        // Check error
        if (str_contains($message, 'hata') || str_contains($message, 'error') || str_contains($message, 'bug') || str_contains($message, 'баг') || str_contains($message, 'çalışmıyor') || str_contains($message, 'ошибка') || str_contains($message, 'не работает')) {
            return __('messages.ai.mock_error');
        }

        return __('messages.ai.mock_default');
    }
}
