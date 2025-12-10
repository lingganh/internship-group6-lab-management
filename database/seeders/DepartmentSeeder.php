<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DepartmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        if(!DB::table('departments')->where('name', 'BM Khoa học Máy tính')->exists()){
            DB::table('departments')->insert(['name' => 'BM Khoa học Máy tính']);
        }
        if(!DB::table('departments')->where('name', 'BM Công nghệ Phần mềm')->exists()){
            DB::table('departments')->insert(['name' => 'BM Công nghệ Phần mềm']);
        }
        if(!DB::table('departments')->where('name', 'BM Mạng và Hệ thống thông tin')->exists()){
            DB::table('departments')->insert(['name' => 'BM Mạng và Hệ thống thông tin']);
        }
        if(!DB::table('departments')->where('name', 'Bộ môn Toán ')->exists()){
            DB::table('departments')->insert(['name' => 'Bộ môn Toán ']);
        }
        if(!DB::table('departments')->where('name', 'Bộ môn Vật lý')->exists()){
            DB::table('departments')->insert(['name' => 'Bộ môn Vật lý']);
        }
        if(!DB::table('departments')->where('name', 'Tổ văn phòng')->exists()){
            DB::table('departments')->insert(['name' => 'Tổ văn phòng']);
        }
    }
}
