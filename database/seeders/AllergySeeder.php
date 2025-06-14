<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AllergySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $sqlPath = database_path('sql/AllergyData.sql');
        
        if (!file_exists($sqlPath)) {
            $this->command->error("SQL file not found at: {$sqlPath}");
            return;
        }

        try {
            DB::unprepared(file_get_contents($sqlPath));
            $this->command->info('Allergy data seeded successfully.');
        } catch (\Exception $e) {
            $this->command->error("Error seeding allergy data: " . $e->getMessage());
        }
    }
}
