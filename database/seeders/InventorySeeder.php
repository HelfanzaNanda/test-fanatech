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
            $res = [];
            for ($i=0; $i < 100; $i++) {
                // array_push($res, fake()->word);
                Inventory::create([
                    'code' => rand(),
                    'name' => "Product " . ($i +1),
                    'price' => fake()->numberBetween($min = 1000, $max = 100000),
                    'stock' => fake()->numberBetween($min = 5, $max = 50),
                ]);
            }
            // dd($res);
        } catch (\Throwable $th) {
            dd([
                'msg' => $th->getMessage()
            ]);
        }
    }
}
