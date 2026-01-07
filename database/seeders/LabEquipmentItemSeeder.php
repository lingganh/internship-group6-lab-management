<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Lab;
use App\Models\Equipment;
use App\Models\LabEquipmentItem;

class LabEquipmentItemSeeder extends Seeder
{
    public function run(): void
    {
        // Lấy tất cả phòng
        $labs = Lab::all();

        foreach ($labs as $lab) {


            switch ($lab->code) {
                case 'LAB-001':
                    $this->seedLabEquipment($lab, [
                        ['code' => 'EQ-PC-', 'quantity' => 20, 'broken' => 2],
                        ['code' => 'EQ-PJ-', 'quantity' => 5, 'broken' => 0],
                    ]);
                    break;

                case 'LAB-002':
                    $this->seedLabEquipment($lab, [
                        ['code' => 'EQ-RT-', 'quantity' => 10, 'broken' => 1],
                        ['code' => 'EQ-SW-', 'quantity' => 8, 'broken' => 2],
                    ]);
                    break;

                case 'LAB-003':
                    $this->seedLabEquipment($lab, [
                        ['code' => 'EQ-GPU-', 'quantity' => 6, 'broken' => 0],
                        ['code' => 'EQ-MON-', 'quantity' => 12, 'broken' => 1],
                    ]);
                    break;

                case 'LAB-004':
                    $this->seedLabEquipment($lab, [
                        ['code' => 'EQ-ARD-', 'quantity' => 15, 'broken' => 3],
                        ['code' => 'EQ-SEN-', 'quantity' => 10, 'broken' => 5],
                    ]);
                    break;
            }
        }
    }

    private function seedLabEquipment($lab, $equipmentData)
    {
        foreach ($equipmentData as $data) {
            $equipment = Equipment::where('code', $data['code'])->first();

            if (!$equipment) {
                continue;
            }

            LabEquipmentItem::updateOrCreate(
                [
                    'lab_id' => $lab->id,
                    'equipment_id' => $equipment->id,
                ],
                [
                    'quantity' => $data['quantity'],
                    'broken_quantity' => $data['broken'],
                    'actual_quantity' => $data['quantity'] - $data['broken'],
                ]
            );
        }
    }
}
