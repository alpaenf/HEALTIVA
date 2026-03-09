<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\HealthRecord;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'name' => 'Admin HEALTIVA',
            'email' => 'admin@healtiva.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'gender' => 'male',
        ]);

        $user = User::create([
            'name' => 'Demo User',
            'email' => 'user@healtiva.com',
            'password' => Hash::make('password'),
            'role' => 'user',
            'gender' => 'male',
            'date_of_birth' => '1995-05-15',
            'phone' => '081234567890',
        ]);

        $records = [
            ['systolic' => 118, 'diastolic' => 78, 'heart_rate' => 72, 'blood_sugar' => 90, 'weight' => 70, 'height' => 170, 'temperature' => 36.5, 'oxygen_saturation' => 98, 'recorded_at' => now()->subDays(7)],
            ['systolic' => 122, 'diastolic' => 80, 'heart_rate' => 75, 'blood_sugar' => 95, 'weight' => 70.5, 'height' => 170, 'temperature' => 36.7, 'oxygen_saturation' => 97, 'recorded_at' => now()->subDays(5)],
            ['systolic' => 120, 'diastolic' => 79, 'heart_rate' => 70, 'blood_sugar' => 88, 'weight' => 70, 'height' => 170, 'temperature' => 36.6, 'oxygen_saturation' => 98, 'recorded_at' => now()->subDays(3)],
            ['systolic' => 119, 'diastolic' => 77, 'heart_rate' => 73, 'blood_sugar' => 92, 'weight' => 69.8, 'height' => 170, 'temperature' => 36.4, 'oxygen_saturation' => 99, 'recorded_at' => now()->subDays(1)],
        ];

        foreach ($records as $record) {
            HealthRecord::create(array_merge($record, ['user_id' => $user->id]));
        }
    }
}
