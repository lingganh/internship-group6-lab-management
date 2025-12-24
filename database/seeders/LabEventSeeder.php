<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon; // Import Carbon

class LabEventSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        $now = Carbon::now();

        DB::table('lab_events')->insert([
            [
                'title' => 'Nghiên cứu AI (Lab 1)',
                'category' => 'work',
                'start' => $now->copy()->setHour(9)->setMinute(0)->setSecond(0), // 9:00 hôm nay
                'end' => $now->copy()->setHour(11)->setMinute(30)->setSecond(0), // 11:30 hôm nay
                'description' => 'Nghiên cứu về thuật toán machine learning mới. Cần chuẩn bị slide.',
                 'created_at' => $now,
                'updated_at' => $now,
                'user_id' =>1
            ],
            [
                'title' => 'Hội thảo Blockchain',
                'category' => 'seminar',
                'start' => $now->copy()->setHour(14)->setMinute(0)->setSecond(0),
                'end' => $now->copy()->setHour(17)->setMinute(0)->setSecond(0),
                'description' => 'Hội thảo về ứng dụng công nghệ blockchain trong tài chính.',
                 'created_at' => $now,
                'updated_at' => $now,
                'user_id' =>1
            ],
            [
                'title' => 'Họp nhóm dự án',
                'category' => 'other',
                'start' => $now->copy()->addDay()->setHour(17)->setMinute(30)->setSecond(0),
                'end' => $now->copy()->addDay()->setHour(18)->setMinute(30)->setSecond(0),
                'description' => 'Họp nhanh 1 tiếng chốt deadline tuần.',
                 'created_at' => $now,
                'updated_at' => $now,
                'user_id' =>1
            ],
            [
                'title' => 'Bảo trì thiết bị Lab 2',
                'category' => 'work',
                'start' => $now->copy()->subDay()->setHour(10)->setMinute(0)->setSecond(0),
                'end' => $now->copy()->subDay()->setHour(12)->setMinute(0)->setSecond(0),
                'description' => 'Bảo trì định kỳ máy chủ GPU.',
                 'created_at' => $now,
                'updated_at' => $now,
                'user_id' =>1
            ],
            [
                'title' => 'Seminar: An toàn thông tin',
                'category' => 'seminar',
                'start' => $now->copy()->addWeek()->setHour(9)->setMinute(30)->setSecond(0),
                'end' => $now->copy()->addWeek()->setHour(11)->setMinute(0)->setSecond(0),
                'description' => 'Chuyên gia từ Viettel trình bày.',
                 'created_at' => $now,
                'updated_at' => $now,
                'user_id' =>1
            ],
        ]);
    }
}
