<?php

namespace Database\Seeders;

use App\Models\Inventory;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class InventorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        try {
            for ($i=0; $i < 100; $i++) {
                Inventory::create([
                    'code' => microtime(true),
                    'name' => fake()->sentence(2),
                    'price' => fake()->numberBetween($min = 1000, $max = 100000),
                    'stock' => fake()->numberBetween($min = 5, $max = 50),
                ]);
            }
        } catch (\Throwable $th) {
            dd([
                'msg' => $th->getMessage()
            ]);
        }
    }
}
