<?php

namespace Database\Seeders;


use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class CitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        try {
            $sqlPath = database_path('sql/cityData.sql');

            if (!file_exists($sqlPath)) {
                $this->command->error("SQL file not found at: {$sqlPath}");
                return;
            }

            $sql = file_get_contents($sqlPath);
        DB::unprepared($sql);
            $this->command->info('City data seeded successfully.');
        } catch (\Exception $e) {
            $this->command->error("Error seeding city data: " . $e->getMessage());
        }
    }
}
