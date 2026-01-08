<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Lab;
use App\Models\Equipment;
use App\Models\LabEquipmentItem;

class EquipmentSeeder extends Seeder
{
    public function run(): void
    {
        $equipments = Equipment::factory()->count(40)->create();

        $labs = Lab::query()->get();

        foreach ($labs as $lab) {
            $pickCount = min(10, $equipments->count());
            if ($pickCount <= 0) continue;

            $equipments->random($pickCount)->each(function ($eq) use ($lab) {
                $qty = rand(5, 30);
                $broken = rand(0, min(3, $qty));

                LabEquipmentItem::updateOrCreate(
                    ['lab_id' => $lab->id, 'equipment_id' => $eq->id],
                    ['quantity' => $qty, 'broken_quantity' => $broken]
                );
            });
        }
    }
}
