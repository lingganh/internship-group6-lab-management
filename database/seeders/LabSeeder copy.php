<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Lab;
use App\Models\User;

class LabSeeder extends Seeder
{
    public function run(): void
    {
        $creatorId = User::query()->value('id');

        if (! $creatorId) {
            throw new \RuntimeException('Chưa có user để gán created_by. Hãy chạy UserSeeder trước.');
        }

        $labs = [
            [
                'name'        => 'Phòng IT',
                'code'        => 'LAB-IT',
                'location'    => 'Tầng 2 - Khu A',
                'capacity'    => 40,
                'description' => 'Phòng thực hành lập trình và hệ thống.',
                'facilities'  => ['PC', 'Projector', 'LAN'],
                'status'      => 'active',
                'image_url'   => null,
            ],
            [
                'name'        => 'Phòng Điện tử',
                'code'        => 'LAB-EL',
                'location'    => 'Tầng 3 - Khu A',
                'capacity'    => 30,
                'description' => 'Phòng thí nghiệm điện tử cơ bản.',
                'facilities'  => ['Oscilloscope', 'Multimeter', 'Power Supply'],
                'status'      => 'active',
                'image_url'   => null,
            ],
            [
                'name'        => 'Phòng Mạng',
                'code'        => 'LAB-NW',
                'location'    => 'Tầng 2 - Khu B',
                'capacity'    => 25,
                'description' => 'Phòng thực hành mạng và hệ thống.',
                'facilities'  => ['Router', 'Switch', 'LAN'],
                'status'      => 'maintenance',
                'image_url'   => null,
            ],
        ];

        foreach ($labs as $lab) {
            Lab::query()->updateOrCreate(
                ['code' => $lab['code']],
                [
                    'name'        => $lab['name'],
                    'location'    => $lab['location'],
                    'capacity'    => $lab['capacity'],
                    'description' => $lab['description'],
                    'facilities'  => $lab['facilities'], // cast json (nếu bạn set cast trong model)
                    'status'      => $lab['status'],
                    'image_url'   => $lab['image_url'],
                    'created_by'  => $creatorId,
                ]
            );
        }
    }
}
