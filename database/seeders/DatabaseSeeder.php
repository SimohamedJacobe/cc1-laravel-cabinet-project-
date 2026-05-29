<?php

namespace Database\Seeders;

use App\Models\Appointment;
use App\Models\Service;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Create 4 default medical services
        $services = [
            [
                'name' => 'General Consultation',
                'description' => 'A comprehensive general health checkup and medical consultation with a general practitioner.',
                'duration_minutes' => 30,
                'price' => 50.00,
            ],
            [
                'name' => 'Cardiology Checkup',
                'description' => 'Detailed cardiac evaluation including ECG and consultation with a cardiologist.',
                'duration_minutes' => 45,
                'price' => 120.00,
            ],
            [
                'name' => 'Dental Cleaning',
                'description' => 'Professional scaling, polishing, and teeth cleaning by a dental hygienist.',
                'duration_minutes' => 45,
                'price' => 80.00,
            ],
            [
                'name' => 'Pediatric Consultation',
                'description' => 'Specialized medical checkup and growth monitoring for children and infants.',
                'duration_minutes' => 30,
                'price' => 60.00,
            ],
        ];

        foreach ($services as $service) {
            Service::create($service);
        }

        // 2. Create specific Admin, Doctor, and Patient users
        User::factory()->create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
        ]);

        User::factory()->create([
            'name' => 'Doctor User',
            'email' => 'doctor@example.com',
            'password' => Hash::make('password'),
            'role' => 'doctor',
        ]);

        User::factory()->create([
            'name' => 'Patient User',
            'email' => 'patient@example.com',
            'password' => Hash::make('password'),
            'role' => 'patient',
        ]);

        // 3. Create 10 additional random patient users
        User::factory(10)->create([
            'role' => 'patient',
        ]);

        // 4. Create 25 random appointments linked to the existing patients and services
        $patients = User::where('role', 'patient')->get();
        $dbServices = Service::all();

        Appointment::factory(25)->create([
            'user_id' => fn () => $patients->random()->id,
            'service_id' => fn () => $dbServices->random()->id,
        ]);
    }
}
