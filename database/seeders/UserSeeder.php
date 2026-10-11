<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $user = User::create([
            'employee_id' => 1,
            'name' => 'admin',
            'email' => 'admin@admin.com',
            'employee_number' => 0000,
            'password' => Hash::make('0000'),
        ]);

        // إعطاء دور Admin للمستخدم admin
        $adminRole = Role::where('name', 'admin')->first();
        if ($adminRole) {
            $user->assignRole($adminRole);
            $this->command->info("✅ تم إعطاء دور Admin للمستخدم: {$user->name}");
        } else {
            $this->warn("⚠️ دور admin غير موجود!");
        }
    }
}