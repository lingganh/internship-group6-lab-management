<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Lab;
use App\Models\LabEvent;
use App\Models\User;

class LabEventSeeder extends Seeder
{
    public function run(): void
    {
        $lab = Lab::query()->inRandomOrder()->first();
        $user = User::query()->inRandomOrder()->first();

        if (! $lab || ! $user) return;

        $baseDate = now()->startOfWeek(); // tạo trong tuần hiện tại

        $slots = [
            ['07:30:00', '09:00:00'],
            ['09:00:00', '10:30:00'],
            ['13:30:00', '15:00:00'],
            ['15:00:00', '16:30:00'],
        ];

        for ($i = 0; $i < 6; $i++) {
            $day = $baseDate->copy()->addDays($i);
            [$startT, $endT] = $slots[array_rand($slots)];

            LabEvent::create([
                'title'       => 'Event test #' . ($i + 1),
                'category'    => 'work',
                'lab_code'    => $lab->code,
                'user_id'     => $user->id,
                'start'       => $day->copy()->setTimeFromTimeString($startT),
                'end'         => $day->copy()->setTimeFromTimeString($endT),
                'status'      => 'approved',
                'description' => 'Seed để test gửi ý kiến (07:00–18:00)',
            ]);
        }
    }
}
