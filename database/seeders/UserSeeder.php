<?php

namespace Database\Seeders;

use App\Enums\UserStatus;
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
            'status' => UserStatus::Approved->value,
            'email_verified_at' => now(),
        ]);
        self::checkIssetBeforeCreate([
            'code' => 'test',
            'full_name' => 'Test',
            'email' => 'test@st.vn',
            'password' => '123456aA@',
            'role_id' => 2,
            'status' => UserStatus::Approved->value,
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
