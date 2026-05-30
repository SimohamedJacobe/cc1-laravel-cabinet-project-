<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ApiAppointmentController;

Route::get('appointments', [ApiAppointmentController::class, 'index'])->name('api.appointments.index');
Route::post('appointments', [ApiAppointmentController::class, 'store'])->name('api.appointments.store');
