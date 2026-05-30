<?php

use App\Models\Appointment;
use App\Models\Service;
use App\Models\User;

test('guests are redirected to the login page when trying to access appointment routes', function () {
    $this->get(route('appointments.index'))->assertRedirect(route('login'));
    $this->get(route('appointments.create'))->assertRedirect(route('login'));
    $this->post(route('appointments.store'), [])->assertRedirect(route('login'));
    $this->get(route('appointments.edit', 1))->assertRedirect(route('login'));
    $this->put(route('appointments.update', 1), [])->assertRedirect(route('login'));
    $this->delete(route('appointments.destroy', 1))->assertRedirect(route('login'));
});

test('a patient can only see their own appointments on index', function () {
    $patientA = User::factory()->patient()->create();
    $patientB = User::factory()->patient()->create();

    $service = Service::factory()->create();

    $appointmentA = Appointment::factory()->create([
        'user_id' => $patientA->id,
        'service_id' => $service->id,
    ]);

    $appointmentB = Appointment::factory()->create([
        'user_id' => $patientB->id,
        'service_id' => $service->id,
    ]);

    $this->actingAs($patientA);

    $response = $this->get(route('appointments.index'));

    $response->assertOk();
    $response->assertViewHas('appointments', function ($appointments) use ($appointmentA, $appointmentB) {
        return $appointments->contains($appointmentA) && !$appointments->contains($appointmentB);
    });
});

test('a patient is forbidden from viewing another patient\'s appointment details', function () {
    $patientA = User::factory()->patient()->create();
    $patientB = User::factory()->patient()->create();
    $service = Service::factory()->create();

    $appointmentB = Appointment::factory()->create([
        'user_id' => $patientB->id,
        'service_id' => $service->id,
    ]);

    $this->actingAs($patientA);

    $this->get(route('appointments.show', $appointmentB))->assertForbidden();
});

test('a patient is forbidden from editing, updating, or deleting another patient\'s appointment', function () {
    $patientA = User::factory()->patient()->create();
    $patientB = User::factory()->patient()->create();
    $service = Service::factory()->create();

    $appointmentB = Appointment::factory()->create([
        'user_id' => $patientB->id,
        'service_id' => $service->id,
    ]);

    $this->actingAs($patientA);

    $this->get(route('appointments.edit', $appointmentB))->assertForbidden();
    $this->put(route('appointments.update', $appointmentB), [])->assertForbidden();
    $this->delete(route('appointments.destroy', $appointmentB))->assertForbidden();
});

test('a patient can successfully create an appointment which defaults to pending and their own user id', function () {
    \Illuminate\Support\Facades\Mail::fake();

    $patient = User::factory()->patient()->create();
    $service = Service::factory()->create();
    $date = now()->addDays(5)->format('Y-m-d H:i:s');

    $this->actingAs($patient);

    $response = $this->post(route('appointments.store'), [
        'service_id' => $service->id,
        'appointment_date' => $date,
        'notes' => 'Looking forward to the consultation.',
    ]);

    $response->assertRedirect(route('appointments.index'));

    $this->assertDatabaseHas('appointments', [
        'user_id' => $patient->id,
        'service_id' => $service->id,
        'appointment_date' => $date,
        'status' => 'pending',
        'notes' => 'Looking forward to the consultation.',
    ]);

    \Illuminate\Support\Facades\Mail::assertSent(\App\Mail\AppointmentConfirmed::class, function ($mail) use ($patient, $service) {
        return $mail->hasTo($patient->email) &&
               $mail->appointment->service_id === $service->id;
    });
});

test('a patient can update their own appointment details but cannot alter status or user id', function () {
    $patient = User::factory()->patient()->create();
    $service1 = Service::factory()->create();
    $service2 = Service::factory()->create();
    $date1 = now()->addDays(5)->format('Y-m-d H:i:s');
    $date2 = now()->addDays(6)->format('Y-m-d H:i:s');

    $appointment = Appointment::factory()->create([
        'user_id' => $patient->id,
        'service_id' => $service1->id,
        'appointment_date' => $date1,
        'status' => 'pending',
        'notes' => 'First notes',
    ]);

    $this->actingAs($patient);

    // Patient tries to update, providing a different user_id and confirmed status.
    // These role-specific fields should be ignored/not updated for a patient.
    $otherUser = User::factory()->patient()->create();

    $response = $this->put(route('appointments.update', $appointment), [
        'service_id' => $service2->id,
        'appointment_date' => $date2,
        'notes' => 'Updated notes',
        'user_id' => $otherUser->id,
        'status' => 'confirmed',
    ]);

    $response->assertRedirect(route('appointments.index'));

    $this->assertDatabaseHas('appointments', [
        'id' => $appointment->id,
        'user_id' => $patient->id, // unchanged
        'service_id' => $service2->id, // updated
        'appointment_date' => $date2, // updated
        'status' => 'pending', // unchanged
        'notes' => 'Updated notes', // updated
    ]);
});

test('a patient can cancel their own appointment via destroy', function () {
    $patient = User::factory()->patient()->create();
    $service = Service::factory()->create();

    $appointment = Appointment::factory()->create([
        'user_id' => $patient->id,
        'service_id' => $service->id,
    ]);

    $this->actingAs($patient);

    $response = $this->delete(route('appointments.destroy', $appointment));
    $response->assertRedirect(route('appointments.index'));

    $this->assertDatabaseHas('appointments', [
        'id' => $appointment->id,
        'status' => 'cancelled',
    ]);
});

test('a doctor can see all appointments', function () {
    $doctor = User::factory()->doctor()->create();
    $patientA = User::factory()->patient()->create();
    $patientB = User::factory()->patient()->create();
    $service = Service::factory()->create();

    $appointmentA = Appointment::factory()->create([
        'user_id' => $patientA->id,
        'service_id' => $service->id,
    ]);

    $appointmentB = Appointment::factory()->create([
        'user_id' => $patientB->id,
        'service_id' => $service->id,
    ]);

    $this->actingAs($doctor);

    $response = $this->get(route('appointments.index'));

    $response->assertOk();
    $response->assertViewHas('appointments', function ($appointments) use ($appointmentA, $appointmentB) {
        return $appointments->contains($appointmentA) && $appointments->contains($appointmentB);
    });
});

test('a doctor can view, edit, and cancel any appointment', function () {
    $doctor = User::factory()->doctor()->create();
    $patient = User::factory()->patient()->create();
    $service = Service::factory()->create();

    $appointment = Appointment::factory()->create([
        'user_id' => $patient->id,
        'service_id' => $service->id,
    ]);

    $this->actingAs($doctor);

    $this->get(route('appointments.show', $appointment))->assertOk();
    $this->get(route('appointments.edit', $appointment))->assertOk();

    $response = $this->delete(route('appointments.destroy', $appointment));
    $response->assertRedirect(route('appointments.index'));

    $this->assertDatabaseHas('appointments', [
        'id' => $appointment->id,
        'status' => 'cancelled',
    ]);
});

test('an admin can see all appointments', function () {
    $admin = User::factory()->admin()->create();
    $patientA = User::factory()->patient()->create();
    $patientB = User::factory()->patient()->create();
    $service = Service::factory()->create();

    $appointmentA = Appointment::factory()->create([
        'user_id' => $patientA->id,
        'service_id' => $service->id,
    ]);

    $appointmentB = Appointment::factory()->create([
        'user_id' => $patientB->id,
        'service_id' => $service->id,
    ]);

    $this->actingAs($admin);

    $response = $this->get(route('appointments.index'));

    $response->assertOk();
    $response->assertViewHas('appointments', function ($appointments) use ($appointmentA, $appointmentB) {
        return $appointments->contains($appointmentA) && $appointments->contains($appointmentB);
    });
});

test('an admin can create an appointment for any patient and set status', function () {
    \Illuminate\Support\Facades\Mail::fake();

    $admin = User::factory()->admin()->create();
    $patient = User::factory()->patient()->create();
    $service = Service::factory()->create();
    $date = now()->addDays(5)->format('Y-m-d H:i:s');

    $this->actingAs($admin);

    $response = $this->post(route('appointments.store'), [
        'user_id' => $patient->id,
        'service_id' => $service->id,
        'appointment_date' => $date,
        'status' => 'confirmed',
        'notes' => 'Booked by admin.',
    ]);

    $response->assertRedirect(route('appointments.index'));

    $this->assertDatabaseHas('appointments', [
        'user_id' => $patient->id,
        'service_id' => $service->id,
        'appointment_date' => $date,
        'status' => 'confirmed',
        'notes' => 'Booked by admin.',
    ]);

    \Illuminate\Support\Facades\Mail::assertSent(\App\Mail\AppointmentConfirmed::class, function ($mail) use ($patient, $service) {
        return $mail->hasTo($patient->email) &&
               $mail->appointment->service_id === $service->id;
    });
});

test('an admin can update any appointment, including changing the user id and status', function () {
    $admin = User::factory()->admin()->create();
    $patient1 = User::factory()->patient()->create();
    $patient2 = User::factory()->patient()->create();
    $service = Service::factory()->create();
    $date1 = now()->addDays(5)->format('Y-m-d H:i:s');
    $date2 = now()->addDays(6)->format('Y-m-d H:i:s');

    $appointment = Appointment::factory()->create([
        'user_id' => $patient1->id,
        'service_id' => $service->id,
        'appointment_date' => $date1,
        'status' => 'pending',
    ]);

    $this->actingAs($admin);

    $response = $this->put(route('appointments.update', $appointment), [
        'user_id' => $patient2->id,
        'service_id' => $service->id,
        'appointment_date' => $date2,
        'status' => 'confirmed',
        'notes' => 'Updated by admin.',
    ]);

    $response->assertRedirect(route('appointments.index'));

    $this->assertDatabaseHas('appointments', [
        'id' => $appointment->id,
        'user_id' => $patient2->id,
        'service_id' => $service->id,
        'appointment_date' => $date2,
        'status' => 'confirmed',
        'notes' => 'Updated by admin.',
    ]);
});

test('booking or updating an appointment in the past fails validation', function () {
    $patient = User::factory()->patient()->create();
    $service = Service::factory()->create();
    $pastDate = now()->subDay()->format('Y-m-d H:i:s');

    $this->actingAs($patient);

    // Try store
    $response = $this->post(route('appointments.store'), [
        'service_id' => $service->id,
        'appointment_date' => $pastDate,
        'notes' => 'Past note',
    ]);
    $response->assertSessionHasErrors(['appointment_date']);

    // Try update
    $futureDate = now()->addDays(5)->format('Y-m-d H:i:s');
    $appointment = Appointment::factory()->create([
        'user_id' => $patient->id,
        'service_id' => $service->id,
        'appointment_date' => $futureDate,
        'status' => 'pending',
    ]);

    $response = $this->put(route('appointments.update', $appointment), [
        'service_id' => $service->id,
        'appointment_date' => $pastDate,
        'notes' => 'Updated to past',
    ]);
    $response->assertSessionHasErrors(['appointment_date']);
});

test('a patient can cancel their own appointment via cancel route', function () {
    $patient = User::factory()->patient()->create();
    $service = Service::factory()->create();
    $appointment = Appointment::factory()->create([
        'user_id' => $patient->id,
        'service_id' => $service->id,
        'status' => 'pending',
    ]);

    $this->actingAs($patient);

    $response = $this->put(route('appointments.cancel', $appointment));
    $response->assertRedirect(route('appointments.index'));

    $this->assertDatabaseHas('appointments', [
        'id' => $appointment->id,
        'status' => 'cancelled',
    ]);
});

test('a patient cannot cancel another patient\'s appointment', function () {
    $patientA = User::factory()->patient()->create();
    $patientB = User::factory()->patient()->create();
    $service = Service::factory()->create();
    $appointmentB = Appointment::factory()->create([
        'user_id' => $patientB->id,
        'service_id' => $service->id,
        'status' => 'pending',
    ]);

    $this->actingAs($patientA);

    $response = $this->put(route('appointments.cancel', $appointmentB));
    $response->assertForbidden();
});

test('a patient can search their own appointments via ajax and see matching records', function () {
    $patient = User::factory()->patient()->create(['name' => 'Alice Patient']);
    $serviceA = Service::factory()->create(['name' => 'Cardiology']);
    $serviceB = Service::factory()->create(['name' => 'Pediatrics']);

    $appointmentA = Appointment::factory()->create([
        'user_id' => $patient->id,
        'service_id' => $serviceA->id,
        'notes' => 'Heart checkup',
    ]);

    $appointmentB = Appointment::factory()->create([
        'user_id' => $patient->id,
        'service_id' => $serviceB->id,
        'notes' => 'Kid checkup',
    ]);

    $this->actingAs($patient);

    // Search for Cardiology
    $response = $this->get(route('appointments.index', ['search' => 'Cardiology']), [
        'X-Requested-With' => 'XMLHttpRequest',
    ]);

    $response->assertOk();
    $response->assertSee('Cardiology');
    $response->assertDontSee('Pediatrics');

    // Search for notes
    $response2 = $this->get(route('appointments.index', ['search' => 'Kid checkup']), [
        'X-Requested-With' => 'XMLHttpRequest',
    ]);

    $response2->assertOk();
    $response2->assertSee('Pediatrics');
    $response2->assertDontSee('Cardiology');
});

test('a patient search results do not include other patients appointments', function () {
    $patientA = User::factory()->patient()->create(['name' => 'Alice Patient']);
    $patientB = User::factory()->patient()->create(['name' => 'Bob Patient']);
    $service = Service::factory()->create(['name' => 'General Consult']);

    $appointmentA = Appointment::factory()->create([
        'user_id' => $patientA->id,
        'service_id' => $service->id,
    ]);

    $appointmentB = Appointment::factory()->create([
        'user_id' => $patientB->id,
        'service_id' => $service->id,
    ]);

    $this->actingAs($patientA);

    // Search for General Consult
    $response = $this->get(route('appointments.index', ['search' => 'General Consult']), [
        'X-Requested-With' => 'XMLHttpRequest',
    ]);

    $response->assertOk();
    $response->assertSee($patientA->name);
    $response->assertDontSee($patientB->name);
});

test('a doctor or admin can search all appointments via ajax', function () {
    $doctor = User::factory()->doctor()->create();
    $patientA = User::factory()->patient()->create(['name' => 'Alice Patient']);
    $patientB = User::factory()->patient()->create(['name' => 'Bob Patient']);
    $service = Service::factory()->create(['name' => 'General Consult']);

    $appointmentA = Appointment::factory()->create([
        'user_id' => $patientA->id,
        'service_id' => $service->id,
    ]);

    $appointmentB = Appointment::factory()->create([
        'user_id' => $patientB->id,
        'service_id' => $service->id,
    ]);

    $this->actingAs($doctor);

    // Search for Alice
    $response = $this->get(route('appointments.index', ['search' => 'Alice']), [
        'X-Requested-With' => 'XMLHttpRequest',
    ]);

    $response->assertOk();
    $response->assertSee('Alice Patient');
    $response->assertDontSee('Bob Patient');
});

test('switching locale stores selected locale in session and redirects back', function () {
    $response = $this->get(route('locale.switch', 'fr'));
    $response->assertRedirect();
    $this->assertEquals('fr', session('locale'));

    $response2 = $this->get(route('locale.switch', 'en'));
    $response2->assertRedirect();
    $this->assertEquals('en', session('locale'));

    // Test that an invalid locale is not set
    $response3 = $this->get(route('locale.switch', 'invalid'));
    $this->assertNotEquals('invalid', session('locale'));
});

test('app locale changes dynamically based on session locale state', function () {
    $patient = User::factory()->patient()->create();
    
    $this->actingAs($patient);
    
    // Set locale session to 'fr'
    $response = $this->withSession(['locale' => 'fr'])->get(route('appointments.index'));
    $response->assertOk();
    
    // Verify translated strings
    $response->assertSee('Tableau de bord');
    $response->assertSee('Rendez-vous');
    $response->assertSee('Approbations en attente');
});

test('appointment confirmed mailable renders HTML layout with details', function () {
    $patient = User::factory()->patient()->create(['name' => 'Alice Patient']);
    $service = Service::factory()->create(['name' => 'Cardiology Checkup', 'price' => 150]);
    $appointment = Appointment::factory()->create([
        'user_id' => $patient->id,
        'service_id' => $service->id,
        'appointment_date' => now()->addDays(2),
        'notes' => 'Testing email template notes',
    ]);

    $mailable = new \App\Mail\AppointmentConfirmed($appointment);

    $mailable->assertHasSubject(__('Appointment Confirmed'));
    $mailable->assertSeeInHtml('Alice Patient');
    $mailable->assertSeeInHtml('Cardiology Checkup');
    $mailable->assertSeeInHtml('150€');
    $mailable->assertSeeInHtml('Testing email template notes');
});

test('GET /api/appointments returns all appointments in JSON format', function () {
    $patient = User::factory()->patient()->create(['name' => 'Alice Patient']);
    $service = Service::factory()->create(['name' => 'Cardiology']);
    $appointment = Appointment::factory()->create([
        'user_id' => $patient->id,
        'service_id' => $service->id,
    ]);

    $response = $this->getJson(route('api.appointments.index'));

    $response->assertOk();
    $response->assertJsonStructure([
        '*' => [
            'id',
            'user_id',
            'service_id',
            'appointment_date',
            'status',
            'notes',
            'user' => [
                'id',
                'name',
                'email',
            ],
            'service' => [
                'id',
                'name',
                'price',
            ],
        ],
    ]);
    
    $response->assertJsonFragment(['name' => 'Alice Patient']);
    $response->assertJsonFragment(['name' => 'Cardiology']);
});

test('POST /api/appointments with valid data creates appointment and dispatches mailable', function () {
    \Illuminate\Support\Facades\Mail::fake();

    $patient = User::factory()->patient()->create();
    $service = Service::factory()->create();
    $date = now()->addDays(5)->format('Y-m-d H:i:s');

    $response = $this->postJson(route('api.appointments.store'), [
        'user_id' => $patient->id,
        'service_id' => $service->id,
        'appointment_date' => $date,
        'notes' => 'API booking test notes',
    ]);

    $response->assertStatus(201);
    $response->assertJsonFragment(['notes' => 'API booking test notes']);
    
    $this->assertDatabaseHas('appointments', [
        'user_id' => $patient->id,
        'service_id' => $service->id,
        'notes' => 'API booking test notes',
    ]);

    \Illuminate\Support\Facades\Mail::assertSent(\App\Mail\AppointmentConfirmed::class, function ($mail) use ($patient) {
        return $mail->hasTo($patient->email);
    });
});

test('POST /api/appointments with invalid data fails with 422 validation errors JSON', function () {
    $response = $this->postJson(route('api.appointments.store'), [
        'user_id' => 999999, // non-existent user
        'service_id' => null,
    ]);

    $response->assertStatus(422);
    $response->assertJsonValidationErrors(['user_id', 'service_id', 'appointment_date']);
});



