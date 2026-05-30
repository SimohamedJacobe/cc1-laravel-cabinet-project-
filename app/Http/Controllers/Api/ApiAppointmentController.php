<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Mail\AppointmentConfirmed;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class ApiAppointmentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $appointments = Appointment::with(['user', 'service'])->latest()->get();

        return response()->json($appointments);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => ['required', 'exists:users,id'],
            'service_id' => ['required', 'exists:services,id'],
            'appointment_date' => ['required', 'date', 'after:now'],
            'notes' => ['nullable', 'string'],
        ]);

        $appointment = new Appointment();
        $appointment->user_id = $validated['user_id'];
        $appointment->service_id = $validated['service_id'];
        $appointment->appointment_date = $validated['appointment_date'];
        $appointment->notes = $validated['notes'] ?? null;
        $appointment->status = 'pending';
        $appointment->save();

        // Load relations for email dispatch and json return
        $appointment->load(['user', 'service']);

        // Send confirmation email
        Mail::to($appointment->user->email)->send(new AppointmentConfirmed($appointment));

        return response()->json($appointment, 201);
    }
}
