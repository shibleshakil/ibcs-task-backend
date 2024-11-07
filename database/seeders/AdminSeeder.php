<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('users')->insert([
            [
                "id" => 1,
                "name" => "Super Admin",
                "role" => "Admin",
                "email" => "admin@admin.com",
                "password" => bcrypt('admin'),
                "email_verified_at" => now(),
                "created_at" => now(),
                "updated_at" => now(),
            ]
        ]);
    }
}
