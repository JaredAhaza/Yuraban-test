<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\County;

class CountiesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        County::create([
            'name' => 'County A',
            'sub_counties' => ['Sub-county A1', 'Sub-county A2', 'Sub-county A3'],
        ]);

        County::create([
            'name' => 'County B',
            'sub_counties' => ['Sub-county B1', 'Sub-county B2', 'Sub-county B3'],
        ]);
    }
}
