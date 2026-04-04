<?php

namespace App\Service;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AiAnalysisService
{
    private string $apiKey;
    private string $endpoint = 'https://api.groq.com/openai/v1/chat/completions';
    private string $model    = 'llama3-8b-8192'; // fast, free on Groq

    public function __construct()
    {
        $this->apiKey = config('services.groq.key');
    }

    /**
     * Generate a supportive, non-clinical AI narrative based on quiz results.
     * Returns null on failure — caller handles graceful degradation.
     */
    public function analyze(int $score, int $maxScore, string $severityLabel, string $essayText): ?string
    {
        if (empty($this->apiKey)) {
            Log::warning('AiAnalysisService: GROQ_API_KEY is not set.');
            return null;
        }

        // Keep the prompt tight — we only want a paragraph, not an essay
        $prompt = <<<EOT
You are a compassionate assistant helping interpret self-reported questionnaire results.
IMPORTANT: You are NOT providing a medical diagnosis. This is a portfolio/educational project.

Score: {$score} out of {$maxScore}. Category: "{$severityLabel}".

The respondent optionally shared: "{$essayText}"

Write 2-3 short, warm, non-clinical paragraphs that:
1. Acknowledge their responses with empathy
2. Briefly explain what this score range generally indicates (not a diagnosis)
3. Gently encourage speaking with a qualified mental health professional if they feel they need support

Never mention specific conditions. Never use diagnostic language. Keep tone supportive and gentle.
EOT;

        try {
            $response = Http::withToken($this->apiKey)
                ->timeout(20) // hard timeout — don't hang a user request
                ->post($this->endpoint, [
                    'model'       => $this->model,
                    'messages'    => [
                        ['role' => 'user', 'content' => $prompt],
                    ],
                    'max_tokens'  => 400, // enough for 2-3 paragraphs
                    'temperature' => 0.7,
                ]);

            if ($response->successful()) {
                return $response->json('choices.0.message.content');
            }

            Log::error('AiAnalysisService: Groq API error.', [
                'status' => $response->status(),
                'body'   => $response->body(),
            ]);

            return null;
        } catch (\Throwable $e) {
            Log::error('AiAnalysisService: Exception.', ['message' => $e->getMessage()]);
            return null;
        }
    }
}
