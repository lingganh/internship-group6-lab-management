<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        if(!DB::table('roles')->where('name', 'admin')->exists()){
            DB::table('roles')->insert(['name' => 'admin']);
        }
        if(!DB::table('roles')->where('name', 'teacher')->exists()){
            DB::table('roles')->insert(['name' => 'teacher']);
        }
        if(!DB::table('roles')->where('name', 'officer')->exists()){
            DB::table('roles')->insert(['name' => 'officer']);
        }
        if(!DB::table('roles')->where('name', 'student')->exists()){
            DB::table('roles')->insert(['name' => 'student']);
        }
    }
}
