<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Article;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class ArticleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        try {
            $sqlPath = database_path('sql/vietnamese_medical_articles_insert.sql');
            
            if (!file_exists($sqlPath)) {
                $this->command->error("SQL file not found at: {$sqlPath}");
                return;
            }

            $sql = file_get_contents($sqlPath);
        DB::unprepared($sql);
            $this->command->info('Article data seeded successfully.');
        } catch (\Exception $e) {
            $this->command->error("Error seeding article data: " . $e->getMessage());
        }
        // Article::factory()->count(20)->create([
        //     'status' => 'published',
        // ]);
    }
}
