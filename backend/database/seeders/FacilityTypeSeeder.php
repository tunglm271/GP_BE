<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;


class FacilityTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        try {
            $sqlPath = database_path('sql/facility_types.sql');

            if (!file_exists($sqlPath)) {
                $this->command->error("SQL file not found at: {$sqlPath}");
                return;
            }

            $sql = file_get_contents($sqlPath);
        DB::unprepared($sql);
            $this->command->info('Facility type data seeded successfully.');
        } catch (\Exception $e) {
            $this->command->error("Error seeding facility type data: " . $e->getMessage());
        }
    }
}
