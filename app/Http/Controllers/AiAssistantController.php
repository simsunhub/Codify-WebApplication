<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AiAssistantController extends Controller
{
    public function ask(Request $request)
    {
        $request->validate([
            'question' => 'required|string|max:1000',
            'course_title' => 'required|string',
            'lesson_title' => 'required|string',
        ]);

        $apiKey = config('services.anthropic.api_key');

        if (!$apiKey) {
            return response()->json([
                'success' => false,
                'message' => __('Anthropic API key is not configured. Please add ANTHROPIC_API_KEY to the .env file.')
            ], 500);
        }

        $systemPrompt = "You are an AI teaching assistant for the EduPlatform platform. "
            . "You are helping a student who is currently taking the course '{$request->course_title}', and specifically looking at the lesson '{$request->lesson_title}'. "
            . "Answer the student's questions clearly, concisely, and accurately based on the context of the course and lesson. "
            . "If they ask something unrelated, politely steer them back to the topic. Reply in Russian.";

        try {
            $response = Http::withHeaders([
                'x-api-key' => $apiKey,
                'anthropic-version' => '2023-06-01',
                'content-type' => 'application/json',
            ])->post('https://api.anthropic.com/v1/messages', [
                'model' => 'claude-3-haiku-20240307',
                'max_tokens' => 1024,
                'system' => $systemPrompt,
                'messages' => [
                    [
                        'role' => 'user',
                        'content' => $request->question
                    ]
                ]
            ]);

            if ($response->successful()) {
                $data = $response->json();
                $reply = $data['content'][0]['text'] ?? __('Sorry, I couldn\'t formulate an answer. Try again.');
                return response()->json([
                    'success' => true,
                    'message' => $reply
                ]);
            } else {
                Log::error('Anthropic API Error', ['response' => $response->body()]);
                return response()->json([
                    'success' => false,
                    'message' => __('Sorry, there was an error when accessing the AI ​​assistant.')
                ], 500);
            }
        } catch (\Exception $e) {
            Log::error('Anthropic API Exception: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => __('Internal Server Error. Please try again later.')
            ], 500);
        }
    }
}