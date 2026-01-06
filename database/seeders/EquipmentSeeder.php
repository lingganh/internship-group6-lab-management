<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Lab;
use App\Models\Equipment;

class EquipmentSeeder extends Seeder
{
    public function run(): void
    {
        $labs = Lab::all();

        foreach ($labs as $lab) {
            match ($lab->code) {
                'LAB-001' => $this->seedCentralLab($lab),
                'LAB-002' => $this->seedNetworkLab($lab),
                'LAB-003' => $this->seedDataLab($lab),
                'LAB-004' => $this->seedIotLab($lab),
                default => null
            };
        }
    }

    private function seedCentralLab($lab)
    {
        Equipment::create([
            'name' => 'Máy tính cấu hình cao',
            'code' => 'EQ-PC-',
            'type' => 'computer',
            'status' => 'in_use',
            'purchased_date' => now()->subYears(1),
            'specifications' => json_encode(['cpu' => 'Core i7','ram'=>'32GB','ssd'=>'1TB']),
            'notes' => 'Dùng cho học AI',
        ]);

        Equipment::create([
            'name' => 'Máy chiếu Epson',
            'code' => 'EQ-PJ-',
            'type' => 'projector',
            'status' => 'in_use',
            'purchased_date' => now()->subYear(),
            'specifications' => json_encode(['resolution'=>'Full HD']),
            'notes' => null,
        ]);
    }

    private function seedNetworkLab($lab)
    {
        Equipment::create([
            'name' => 'Router Cisco',
            'code' => 'EQ-RT-',
            'type' => 'network',
            'status' => 'available',
            'specifications' => json_encode(['series'=>'Cisco 2900']),
            'notes' => 'Dùng cho môn mạng máy tính',
        ]);

        Equipment::create([
            'name' => 'Switch Layer 3',
            'code' => 'EQ-SW-',
            'type' => 'network',
            'status' => 'maintenance',
            'specifications' => json_encode(['ports'=>'48 ports']),
            'notes' => null,
        ]);
    }

    private function seedDataLab($lab)
    {
        Equipment::create([
            'name' => 'Máy trạm GPU',
            'code' => 'EQ-GPU-',
            'type' => 'workstation',
            'status' => 'available',
            'specifications' => json_encode(['gpu'=>'RTX 4090','ram'=>'64GB']),
            'notes' => 'Chạy Deep Learning',
        ]);

        Equipment::create([
            'name' => 'Màn hình 4K',
            'code' => 'EQ-MON-',
            'type' => 'display',
            'status' => 'available',
            'specifications' => null,
            'notes' => null,
        ]);
    }

    private function seedIotLab($lab)
    {
        Equipment::create([
            'name' => 'Bộ kit Arduino',
            'code' => 'EQ-ARD-',
            'type' => 'iot',
            'status' => 'available',
            'specifications' => null,
            'notes' => 'Dùng cho dự án IoT',
        ]);

        Equipment::create([
            'name' => 'Cảm biến môi trường',
            'code' => 'EQ-SEN-',
            'type' => 'iot',
            'status' => 'broken',
            'specifications' => null,
            'notes' => null,
        ]);
    }
}
