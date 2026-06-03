<?php

namespace App\Services\Ai;

use Illuminate\Support\Facades\Http;
use RuntimeException;

class GeminiService
{
    private ?string $apiKey;
    private string $model;

    public function __construct()
    {
        $this->apiKey = config('services.gemini.key');
        $this->model  = config('services.gemini.model', 'gemini-2.5-flash');
    }

    public function isConfigured(): bool
    {
        return filled($this->apiKey);
    }

    /**
     * Draft a ~500 word course description plus a set of modules from the
     * course title (and optional category), returned as structured data.
     *
     * @return array{description: string, modules: array<int, array{title: string, description: string, duration_hours: int}>}
     */
    public function courseContent(string $title, ?string $category = null): array
    {
        $context = $category ? " It falls under the \"{$category}\" category." : '';

        $prompt = "You are creating content for a medical / first-aid training course titled \"{$title}\".{$context}\n\n"
            . "Produce two things for prospective trainees:\n"
            . "1. description: a professional, engaging course description of about 500 words covering what the "
            . "course is about, who should attend, the key topics and practical skills taught, the learning "
            . "outcomes, and the certification earned on completion. Use plain prose split into 3-5 paragraphs; "
            . "separate each paragraph with a blank line. No markdown, headings or bullet symbols.\n"
            . "2. modules: between 4 and 8 logical course modules, each with a concise title, a one-sentence "
            . "description, and an integer duration_hours between 1 and 8.";

        $schema = [
            'type'       => 'object',
            'properties' => [
                'description' => ['type' => 'string'],
                'modules'     => [
                    'type'  => 'array',
                    'items' => [
                        'type'       => 'object',
                        'properties' => [
                            'title'          => ['type' => 'string'],
                            'description'    => ['type' => 'string'],
                            'duration_hours' => ['type' => 'integer'],
                        ],
                        'required' => ['title', 'description', 'duration_hours'],
                    ],
                ],
            ],
            'required' => ['description', 'modules'],
        ];

        $json = $this->generateJson($prompt, $schema);

        $modules = [];
        foreach ($json['modules'] ?? [] as $m) {
            if (blank($m['title'] ?? null)) {
                continue;
            }
            $modules[] = [
                'title'          => (string) $m['title'],
                'description'    => (string) ($m['description'] ?? ''),
                'duration_hours' => max(1, (int) ($m['duration_hours'] ?? 1)),
            ];
        }

        return [
            'description' => trim((string) ($json['description'] ?? '')),
            'modules'     => $modules,
        ];
    }

    /**
     * Send a prompt expecting a JSON object back (uses Gemini structured output).
     *
     * @param  array<string, mixed>  $schema  OpenAPI-style response schema.
     * @return array<string, mixed>
     */
    public function generateJson(string $prompt, array $schema): array
    {
        $raw = $this->request($prompt, [
            'responseMimeType' => 'application/json',
            'responseSchema'   => $schema,
        ]);

        $data = json_decode($raw, true);

        if (! is_array($data)) {
            throw new RuntimeException('Gemini returned malformed JSON.');
        }

        return $data;
    }

    /**
     * Send a single text prompt to Gemini and return the generated plain text.
     *
     * @throws RuntimeException when the key is missing or the API call fails.
     */
    public function generate(string $prompt): string
    {
        return $this->request($prompt);
    }

    /**
     * Low-level call to the Gemini generateContent endpoint.
     *
     * @param  array<string, mixed>  $generationConfig  Extra generationConfig keys.
     */
    private function request(string $prompt, array $generationConfig = []): string
    {
        if (! $this->isConfigured()) {
            throw new RuntimeException('Gemini API key is not configured (set GEMINI_KEY in .env).');
        }

        $endpoint = "https://generativelanguage.googleapis.com/v1beta/models/{$this->model}:generateContent";

        $response = Http::timeout(45)
            ->withQueryParameters(['key' => $this->apiKey])
            ->acceptJson()
            ->post($endpoint, [
                'contents' => [[
                    'parts' => [['text' => $prompt]],
                ]],
                'generationConfig' => array_merge([
                    'temperature'     => 0.7,
                    'maxOutputTokens' => 3000,
                    // Gemini 2.5 "flash" is a thinking model; without this it spends
                    // the output budget on internal reasoning and truncates the answer.
                    'thinkingConfig'  => ['thinkingBudget' => 0],
                ], $generationConfig),
            ]);

        if ($response->failed()) {
            $message = data_get($response->json(), 'error.message', 'HTTP '.$response->status());
            throw new RuntimeException("Gemini request failed: {$message}");
        }

        $text = data_get($response->json(), 'candidates.0.content.parts.0.text');

        if (blank($text)) {
            throw new RuntimeException('Gemini returned an empty response. Please try again.');
        }

        return trim($text);
    }
}
