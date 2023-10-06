<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::beginTransaction();
        try {
            // $admin = User::create([
            //     'name' => "admin",
            //     'email' => "admin@example.com",
            //     'email_verified_at' => now(),
            //     'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password
            //     'remember_token' => Str::random(10),
            // ]);

            // $role = Role::where("name", "SUPERADMIN")->first();

            // $admin->assignRole($role);

            $roles = [];
            for ($i=0; $i < 20; $i++) {
                $user = User::create([
                    'name' => fake()->name(),
                    'email' => fake()->unique()->safeEmail(),
                    'email_verified_at' => now(),
                    'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password
                    'remember_token' => Str::random(10),
                ]);

                // $role = Role::all()->random(1)->first();
                $role = Role::all()->random(1)->first();
                array_push($roles, $role);
                $user->assignRole($role);

            }
            // dd([
            //     'ROLES ' => $roles
            // ]);
            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();
            dd([
                'msg' => $th->getMessage()
            ]);
        }
    }
}
