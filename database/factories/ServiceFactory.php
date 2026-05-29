<?php

namespace Database\Factories;

use App\Models\Service;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Service>
 */
class ServiceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
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
            [
                'name' => 'Dermatological Exam',
                'description' => 'Skin consultation, mole screening, and evaluation for dermatological concerns.',
                'duration_minutes' => 20,
                'price' => 70.00,
            ],
            [
                'name' => 'Physiotherapy Session',
                'description' => 'Personalized physical rehabilitation session and exercise program.',
                'duration_minutes' => 60,
                'price' => 90.00,
            ],
        ];

        $service = fake()->randomElement($services);

        return [
            'name' => $service['name'],
            'description' => $service['description'],
            'duration_minutes' => $service['duration_minutes'],
            'price' => $service['price'],
        ];
    }
}
