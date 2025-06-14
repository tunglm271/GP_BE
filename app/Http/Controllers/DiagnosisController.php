<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Http;

use Illuminate\Http\Request;
use App\Models\MedicalService;

class DiagnosisController extends Controller
{
    protected function  generateEmbedding($text) {
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . env('OPENAI_API_KEY'),
        ])->post('https://api.openai.com/v1/embeddings', [
            'input' => $text,
            'model' => 'text-embedding-3-small',
        ]);

        return $response['data'][0]['embedding'] ?? null;
    }

    public function cosineSimilarity(array $vec1, array $vec2)
    {
        $dot = 0.0;
        $normA = 0.0;
        $normB = 0.0;

        for ($i = 0; $i < count($vec1); $i++) {
            $dot += $vec1[$i] * $vec2[$i];
            $normA += $vec1[$i] ** 2;
            $normB += $vec2[$i] ** 2;
        }

        return $dot / (sqrt($normA) * sqrt($normB) + 1e-10);
    }

    protected function generateExplanation($prompt, $serviceName, $description)
    {
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . env('OPENAI_API_KEY'),
        ])->post('https://api.openai.com/v1/chat/completions', [
            'model' => 'gpt-3.5-turbo',
            'messages' => [
                [
                    'role' => 'system',
                    'content' => 'Bạn là một trợ lý y tế thân thiện, có nhiệm vụ giải thích lý do tại sao người dùng nên đi khám một dịch vụ y tế cụ thể dựa trên nhu cầu đã mô tả.',
                ],
                [
                    'role' => 'user',
                    'content' => "Tôi có nhu cầu: \"$prompt\"\nDịch vụ phù hợp là \"$serviceName\".\nMô tả dịch vụ: $description\nHãy viết một đoạn ngắn (2-3 câu) thuyết phục tôi nên đi khám dịch vụ này.",
                ],
            ],
            'temperature' => 0.7,
        ]);

        return $response['choices'][0]['message']['content'] ?? null;
    }

    public function query(Request $request)
    {
        $prompt = $request->input('prompt');

        if (!$prompt) {
            return response()->json(['error' => 'Prompt is required'], 400);
        }

        // Step 1: Lấy embedding của prompt
        $embedding = $this->generateEmbedding($prompt);

        if (!$embedding) {
            return response()->json(['error' => 'Failed to get embedding'], 500);
        }

        // Step 2: Truy xuất toàn bộ embeddings từ DB
        $results = MedicalService::whereNotNull('embedding')->distinct('name')->get();

        // Step 3: Tính cosine similarity và sắp xếp
        $scored = $results->map(callback: function ($item) use ($embedding) {
            $score = $this->cosineSimilarity(
                json_decode($item->embedding),
                $embedding
            );
            $item->similarity = $score;
            return $item;
        })->sortByDesc('similarity')->take(3); // Lấy Top 3 kết quả

        // Step 4: Generate explanation for each service
        $recommendedServices = $scored->map(function ($service) use ($prompt) {
            $explanation = $this->generateExplanation($prompt, $service->name, $service->description);
            return [
                'service' => $service,
                'explanation' => $explanation,
                'similarity_score' => $service->similarity
            ];
        })->values()->toArray();

        // Step 5: Generate overall explanation
        $overallExplanation = $this->generateOverallExplanation($prompt, collect($recommendedServices));

        return response()->json([
            'recommended_services' => $recommendedServices,
            'overall_explanation' => $overallExplanation
        ]);
    }

    protected function generateOverallExplanation($prompt, $recommendedServices)
    {
        $servicesList = $recommendedServices->map(function ($item) {
            return $item['service']->name;
        })->join(', ');

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . env('OPENAI_API_KEY'),
        ])->post('https://api.openai.com/v1/chat/completions', [
            'model' => 'gpt-4o-mini',
            'messages' => [
                [
                    'role' => 'system',
                    'content' => 'Bạn là một trợ lý y tế thân thiện, có nhiệm vụ giải thích tổng quan về các dịch vụ y tế được đề xuất dựa trên nhu cầu của người dùng.',
                ],
                [
                    'role' => 'user',
                    'content' => "Dựa trên nhu cầu: \"$prompt\"\nChúng tôi đề xuất các dịch vụ sau: $servicesList\nHãy viết một đoạn ngắn (3-4 câu) giải thích tổng quan về lý do tại sao những dịch vụ này phù hợp với nhu cầu của người dùng.",
                ],
            ],
            'temperature' => 0.5,
        ]);

        return $response['choices'][0]['message']['content'] ?? null;
    }


    protected function ragQuery(Request $request)
    {
        $prompt = $request->input('prompt');
        if (!$prompt) {
            return response()->json(['error' => 'Prompt is required'], 400);
        }
        $services = MedicalService::distinct('name')->get();
        $response = Http::post(env('RAG_API_URL') . '/suggest', [
            'user_input' => $prompt,
            'services' => $services->toArray(),
        ]);
        return $response->json();
    }
}
