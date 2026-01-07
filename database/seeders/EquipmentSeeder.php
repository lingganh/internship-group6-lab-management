<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Lab;
use App\Models\Equipment;

class EquipmentSeeder extends Seeder
{
    public function run(): void
    {
        if (Lab::query()->count() === 0) {
            throw new \RuntimeException('Chưa có labs. Hãy chạy LabSeeder trước.');
        }

        // Ví dụ: mỗi lab 20 thiết bị
        foreach (Lab::query()->pluck('id') as $labId) {
            Equipment::factory()->count(20)->create([
                'lab_id' => $labId,
            ]);
        }
    }
}
