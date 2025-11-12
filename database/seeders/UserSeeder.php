<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        self::checkIssetBeforeCreate([
            'code' => 'admin',
            'full_name' => 'Super Admin',
            'email' => 'superadmin@st.vn',
            'role_id' => 1,
            'password' => '123456aA@',
            'email_verified_at' => now(),
        ]);
    }

    private function checkIssetBeforeCreate($data): void
    {
        $admin = User::where('email', $data['email'])->first();
        if (empty($admin)) {
            User::create($data);
        } else {
            $admin->update($data);
        }
    }
}
