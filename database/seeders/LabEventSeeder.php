<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use App\Models\LabEvent;

class LabEventSeeder extends Seeder
{
    public function run(): void
    {
        $mk = fn() => 'EV-' . now()->format('ymd') . '-' . Str::upper(Str::random(4));

        LabEvent::insert([
            [
                'title' => 'Nghiên cứu AI (Lab 1)',
                'category' => 'work',
                'start' => '2025-12-27 09:00:00',
                'end' => '2025-12-27 11:30:00',
                'description' => 'Nghiên cứu về thuật toán machine learning mới. Cần chuẩn bị slide.',
                'lab_code' => 'LAB-001',
                'user_id' => 1,
                'updated_by' => 1,
                'created_at' => now(),
                'updated_at' => now(),
                'color' => '#3498db'

            ],
            [
                'title' => 'Hội thảo Blockchain',
                'category' => 'seminar',
                'start' => '2025-12-27 14:00:00',
                'end' => '2025-12-27 17:00:00',
                'description' => 'Hội thảo về ứng dụng công nghệ blockchain trong tài chính.',
                'lab_code' => 'LAB-002',
                'user_id' => 1,
                'updated_by' => 1,
                'created_at' => now(),
                'updated_at' => now(),
                'color' => '#3498db'

            ],
            [
                'title' => 'Họp nhóm dự án',
                'category' => 'other',
                'start' => '2025-12-28 17:30:00',
                'end' => '2025-12-28 18:30:00',
                'description' => 'Họp nhanh 1 tiếng chốt deadline tuần.',
                'lab_code' => 'LAB-003',
                'user_id' => 1,
                'updated_by' => 1,
                'created_at' => now(),
                'updated_at' => now(),
                'color' => '#3498db'

            ],
            [
                'title' => 'Bảo trì thiết bị Lab 2',
                'category' => 'work',
                'start' => '2025-12-26 10:00:00',
                'end' => '2025-12-26 12:00:00',
                'description' => 'Bảo trì định kỳ máy chủ GPU.',
                'lab_code' => 'LAB-002',
                'user_id' => 1,
                'updated_by' => 1,
                'created_at' => now(),
                'updated_at' => now(),
                'color' => '#3498db'

            ],
            [
                'title' => 'Seminar: An toàn thông tin',
                'category' => 'seminar',
                'start' => '2026-01-03 09:30:00',
                'end' => '2026-01-03 11:00:00',
                'description' => 'Chuyên gia từ Viettel trình bày.',
                'lab_code' => 'LAB-004',
                'user_id' => 1,
                'updated_by' => 1,
                'created_at' => now(),
                'updated_at' => now(),
                'color' => '#3498db'
            ],
        ]);
    }
}
