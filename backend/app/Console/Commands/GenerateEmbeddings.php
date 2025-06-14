<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use App\Models\MedicalService;

class GenerateEmbeddings extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'generate:embedding';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */

    protected function generateEmbedding($text)
    {
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . env('OPENAI_API_KEY'),
        ])->post('https://api.openai.com/v1/embeddings', [
            'input' => $text,
            'model' => 'text-embedding-3-small',
        ]);

        return $response['data'][0]['embedding'] ?? null;
    }

    public function handle()
    {
        $this->info('Starting embedding generation...');

        $services = MedicalService::select('name')
                    ->groupBy('name')
                    ->get()
                    ->map(function ($item) {
                        return MedicalService::where('name', $item->name)->first();
                    });

        foreach ($services as $service) {
            $text = $service->name . ' ' . $service->description;

            $embedding = $this->generateEmbedding($text);

            if ($embedding) {
                $service->embedding = json_encode($embedding); 
                $service->save();

                $this->info("✅ Embedded: ID {$service->id}");
            } else {
                $this->warn("❌ Failed embedding: ID {$service->id}");
            }

            // Optional: Sleep để tránh rate limit (nếu có nhiều bản ghi)
            usleep(300000); // 300ms
        }

        $this->info('✅ Embedding generation completed.');
    }
}
