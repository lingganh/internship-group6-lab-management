<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use App\Models\Lab;

class LabSeeder extends Seeder
{
    public function run(): void
    {
        Lab::insert([
            [
                'name' => 'Phòng Máy Trung Tâm',
                'code' => 'LAB-001',
                'location' => 'Tòa A1 - Phòng 101',
                'capacity' => 40,
                'description' => 'Phòng máy chính được sử dụng cho các buổi học và nghiên cứu AI.',
                'facilities' => json_encode(['Máy tính cấu hình cao', 'Máy chiếu', 'Điều hòa']),
                'status' => 'active',
                'image_url' => 'labs/lab1.jpg',
                'created_by' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Phòng Lab Mạng Máy Tính',
                'code' => 'LAB-002',
                'location' => 'Tòa B2 - Phòng 203',
                'capacity' => 35,
                'description' => 'Phòng chuyên cho các môn học về mạng máy tính và an toàn thông tin.',
                'facilities' => json_encode(['Router Cisco', 'Switch Layer 3', 'Máy chủ ảo']),
                'status' => 'active',
                'image_url' => 'labs/lab2.jpg',
                'created_by' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Phòng Lab Phân Tích Dữ Liệu',
                'code' => 'LAB-003',
                'location' => 'Tòa A2 - Phòng 305',
                'capacity' => 30,
                'description' => 'Phòng dành cho các dự án phân tích dữ liệu và học máy.',
                'facilities' => json_encode(['Máy chủ GPU', 'Màn hình 4K', 'Bảng trắng']),
                'status' => 'active',
                'image_url' => 'labs/lab3.jpg',
                'created_by' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Phòng Lab IoT',
                'code' => 'LAB-004',
                'location' => 'Tòa C1 - Phòng 201',
                'capacity' => 25,
                'description' => 'Phòng phục vụ thực hành các dự án IoT và vi điều khiển.',
                'facilities' => json_encode(['Bộ kit Arduino', 'Cảm biến', 'Máy in 3D']),
                'status' => 'maintenance',
                'image_url' => 'labs/lab4.jpg',
                'created_by' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
