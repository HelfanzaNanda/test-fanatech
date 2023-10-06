<?php

namespace Database\Seeders;

use App\Enum\RoleEnum;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roles = RoleEnum::cases();

        // dd([
        //     'roles' => $roles,
        // ]);

        // $res = [];
        foreach ($roles as $role) {
            Role::create([
                'name' => $role->value
            ]);
        }
    }
}
