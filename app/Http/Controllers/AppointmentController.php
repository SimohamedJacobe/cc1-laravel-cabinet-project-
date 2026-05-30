<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Service;
use App\Models\User;
use Illuminate\Http\Request;

class AppointmentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $user = $request->user();
        $patients = [];
        $search = $request->input('search');

        $query = Appointment::query()
            ->with(['user', 'service'])
            ->latest();

        if (in_array($user->role, ['admin', 'doctor'])) {
            $patients = User::where('role', 'patient')->get();
        } else {
            $query->where('user_id', $user->id);
        }

        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->whereHas('user', function ($uq) use ($search) {
                    $uq->where('name', 'like', "%{$search}%")
                       ->orWhere('email', 'like', "%{$search}%");
                })
                ->orWhereHas('service', function ($sq) use ($search) {
                    $sq->where('name', 'like', "%{$search}%");
                })
                ->orWhere('notes', 'like', "%{$search}%");
            });
        }

        $appointments = $query->get();
        $services = Service::all();

        if ($request->ajax() || $request->wantsJson()) {
            return response(view('appointments.partials.rows', compact('appointments'))->render());
        }

        return view('appointments.index', compact('appointments', 'services', 'patients'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $services = Service::all();
        $patients = [];

        if (in_array(auth()->user()->role, ['admin', 'doctor'])) {
            $patients = User::where('role', 'patient')->get();
        }

        return view('appointments.create', compact('services', 'patients'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $user = $request->user();

        $rules = [
            'service_id' => ['required', 'exists:services,id'],
            'appointment_date' => ['required', 'date', 'after:now'],
            'notes' => ['nullable', 'string'],
        ];

        if (in_array($user->role, ['admin', 'doctor'])) {
            $rules['user_id'] = ['required', 'exists:users,id'];
            $rules['status'] = ['required', 'in:pending,confirmed,cancelled'];
        }

        $validated = $request->validate($rules);

        $appointment = new Appointment();
        $appointment->service_id = $validated['service_id'];
        $appointment->appointment_date = $validated['appointment_date'];
        $appointment->notes = $validated['notes'] ?? null;

        if (in_array($user->role, ['admin', 'doctor'])) {
            $appointment->user_id = $validated['user_id'];
            $appointment->status = $validated['status'];
        } else {
            $appointment->user_id = $user->id;
            $appointment->status = 'pending';
        }

        $appointment->save();

        $appointment->load('user');
        \Illuminate\Support\Facades\Mail::to($appointment->user->email)->send(new \App\Mail\AppointmentConfirmed($appointment));

        return redirect()->route('appointments.index')->with('success', 'Appointment created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Appointment $appointment)
    {
        $user = auth()->user();

        if ($user->role === 'patient' && $appointment->user_id !== $user->id) {
            abort(403, 'Unauthorized action.');
        }

        return view('appointments.show', compact('appointment'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Appointment $appointment)
    {
        $user = auth()->user();

        if ($user->role === 'patient' && $appointment->user_id !== $user->id) {
            abort(403, 'Unauthorized action.');
        }

        $services = Service::all();
        $patients = [];

        if (in_array($user->role, ['admin', 'doctor'])) {
            $patients = User::where('role', 'patient')->get();
        }

        return view('appointments.edit', compact('appointment', 'services', 'patients'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Appointment $appointment)
    {
        $user = $request->user();

        if ($user->role === 'patient' && $appointment->user_id !== $user->id) {
            abort(403, 'Unauthorized action.');
        }

        $rules = [
            'service_id' => ['required', 'exists:services,id'],
            'appointment_date' => ['required', 'date', 'after:now'],
            'notes' => ['nullable', 'string'],
        ];

        if (in_array($user->role, ['admin', 'doctor'])) {
            $rules['user_id'] = ['required', 'exists:users,id'];
            $rules['status'] = ['required', 'in:pending,confirmed,cancelled'];
        }

        $validated = $request->validate($rules);

        $appointment->service_id = $validated['service_id'];
        $appointment->appointment_date = $validated['appointment_date'];
        $appointment->notes = $validated['notes'] ?? null;

        if (in_array($user->role, ['admin', 'doctor'])) {
            $appointment->user_id = $validated['user_id'];
            $appointment->status = $validated['status'];
        }

        $appointment->save();

        return redirect()->route('appointments.index')->with('success', 'Appointment updated successfully.');
    }

    /**
     * Cancel the specified appointment.
     */
    public function cancel(Appointment $appointment)
    {
        $user = auth()->user();

        if ($user->role === 'patient' && $appointment->user_id !== $user->id) {
            abort(403, 'Unauthorized action.');
        }

        $appointment->status = 'cancelled';
        $appointment->save();

        return redirect()->route('appointments.index')->with('success', 'Appointment cancelled successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Appointment $appointment)
    {
        return $this->cancel($appointment);
    }
}
