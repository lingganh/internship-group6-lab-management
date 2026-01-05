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
            'lab_id' => $lab->id,
            'name' => 'Máy tính cấu hình cao',
            'code' => 'EQ-PC-'.$lab->id.'-01',
            'type' => 'computer',
            'status' => 'in_use',
            'quantity' => 40,
            'broken_quantity' => 3,
            'actual_quantity' => 37,
            'purchased_date' => now()->subYears(1),
            'specifications' => json_encode(['cpu' => 'Core i7','ram'=>'32GB','ssd'=>'1TB']),
            'notes' => 'Dùng cho học AI',
        ]);

        Equipment::create([
            'lab_id' => $lab->id,
            'name' => 'Máy chiếu Epson',
            'code' => 'EQ-PJ-'.$lab->id.'-01',
            'type' => 'projector',
            'status' => 'in_use',
            'quantity' => 1,
            'broken_quantity' => 0,
            'actual_quantity' => 1,
            'purchased_date' => now()->subYear(),
            'specifications' => json_encode(['resolution'=>'Full HD']),
            'notes' => null,
        ]);
    }

    private function seedNetworkLab($lab)
    {
        Equipment::create([
            'lab_id' => $lab->id,
            'name' => 'Router Cisco',
            'code' => 'EQ-RT-'.$lab->id.'-01',
            'type' => 'network',
            'status' => 'available',
            'quantity' => 15,
            'broken_quantity' => 1,
            'actual_quantity' => 14,
            'specifications' => json_encode(['series'=>'Cisco 2900']),
            'notes' => 'Dùng cho môn mạng máy tính',
        ]);

        Equipment::create([
            'lab_id' => $lab->id,
            'name' => 'Switch Layer 3',
            'code' => 'EQ-SW-'.$lab->id.'-01',
            'type' => 'network',
            'status' => 'maintenance',
            'quantity' => 10,
            'broken_quantity' => 2,
            'actual_quantity' => 8,
            'specifications' => json_encode(['ports'=>'48 ports']),
            'notes' => null,
        ]);
    }

    private function seedDataLab($lab)
    {
        Equipment::create([
            'lab_id' => $lab->id,
            'name' => 'Máy trạm GPU',
            'code' => 'EQ-GPU-'.$lab->id.'-01',
            'type' => 'workstation',
            'status' => 'available',
            'quantity' => 20,
            'broken_quantity' => 1,
            'actual_quantity' => 19,
            'specifications' => json_encode(['gpu'=>'RTX 4090','ram'=>'64GB']),
            'notes' => 'Chạy Deep Learning',
        ]);

        Equipment::create([
            'lab_id' => $lab->id,
            'name' => 'Màn hình 4K',
            'code' => 'EQ-MON-'.$lab->id.'-01',
            'type' => 'display',
            'status' => 'available',
            'quantity' => 20,
            'broken_quantity' => 0,
            'actual_quantity' => 20,
            'specifications' => null,
            'notes' => null,
        ]);
    }

    private function seedIotLab($lab)
    {
        Equipment::create([
            'lab_id' => $lab->id,
            'name' => 'Bộ kit Arduino',
            'code' => 'EQ-ARD-'.$lab->id.'-01',
            'type' => 'iot',
            'status' => 'available',
            'quantity' => 25,
            'broken_quantity' => 5,
            'actual_quantity' => 20,
            'specifications' => null,
            'notes' => 'Dùng cho dự án IoT',
        ]);

        Equipment::create([
            'lab_id' => $lab->id,
            'name' => 'Cảm biến môi trường',
            'code' => 'EQ-SEN-'.$lab->id.'-01',
            'type' => 'iot',
            'status' => 'broken',
            'quantity' => 15,
            'broken_quantity' => 6,
            'actual_quantity' => 9,
            'specifications' => null,
            'notes' => null,
        ]);
    }
}
