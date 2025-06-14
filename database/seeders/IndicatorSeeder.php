<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
class IndicatorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        try {
            $sqlPath = database_path('sql/IndicatorData.sql');
            
            if (!file_exists($sqlPath)) {
                $this->command->error("SQL file not found at: {$sqlPath}");
                return;
            }

            $sql = file_get_contents($sqlPath);
            DB::unprepared($sql);
            $this->command->info('Indicator data seeded successfully.');
        } catch (\Exception $e) {
            $this->command->error("Error seeding indicator data: " . $e->getMessage());
        }
    }
}
