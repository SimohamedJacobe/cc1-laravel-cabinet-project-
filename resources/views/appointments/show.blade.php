<x-layouts::app :title="__('Appointment Details')">
    <div class="max-w-3xl mx-auto p-4 sm:p-6 lg:p-8">
        <!-- Header Section -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pb-5 border-b border-zinc-200 dark:border-zinc-800 mb-6">
            <div>
                <h1 class="text-3xl font-bold tracking-tight text-zinc-950 dark:text-zinc-50 font-sans">
                    {{ __('Appointment Details') }}
                </h1>
                <p class="mt-1.5 text-sm text-zinc-500 dark:text-zinc-400">
                    {{ __('View details and status for appointment #') }}{{ $appointment->id }}.
                </p>
            </div>
            <div>
                <a href="{{ route('appointments.index') }}" class="inline-flex items-center justify-center gap-2 rounded-xl border border-zinc-300 dark:border-zinc-700 bg-white dark:bg-zinc-950 px-4 py-2.5 text-sm font-semibold text-zinc-700 dark:text-zinc-300 shadow-sm hover:bg-zinc-50 dark:hover:bg-zinc-900 transition duration-150">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
                    </svg>
                    {{ __('Back to List') }}
                </a>
            </div>
        </div>

        @if(session('success'))
            <div class="flex items-center gap-3 p-4 mb-6 text-sm text-emerald-800 rounded-xl bg-emerald-50 border border-emerald-100 dark:bg-emerald-950/30 dark:text-emerald-400 dark:border-emerald-900/50" role="alert">
                <svg class="h-5 w-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span class="font-medium">{{ session('success') }}</span>
            </div>
        @endif

        <div class="overflow-hidden rounded-2xl border border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-900 shadow-sm">
            <div class="p-6 sm:p-8 space-y-6">
                <!-- Patient Name & Initials -->
                <div class="flex items-center gap-4">
                    <div class="flex h-14 w-14 items-center justify-center rounded-full bg-indigo-50 dark:bg-indigo-950/50 text-indigo-600 dark:text-indigo-400 font-bold text-lg">
                        {{ $appointment->user->initials() }}
                    </div>
                    <div>
                        <div class="text-xs font-semibold uppercase tracking-wider text-zinc-400">{{ __('Patient') }}</div>
                        <div class="text-xl font-bold text-zinc-900 dark:text-white mt-0.5">{{ $appointment->user->name }}</div>
                        <div class="text-sm text-zinc-500 dark:text-zinc-400">{{ $appointment->user->email }}</div>
                    </div>
                </div>

                <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 border-t border-zinc-100 dark:border-zinc-800/50 pt-6">
                    <!-- Service -->
                    <div>
                        <span class="block text-xs font-semibold uppercase tracking-wider text-zinc-400">{{ __('Medical Service') }}</span>
                        <span class="block text-base font-semibold text-zinc-900 dark:text-white mt-1">{{ $appointment->service->name }}</span>
                        <span class="text-sm text-zinc-500 dark:text-zinc-400 mt-0.5">{{ number_format($appointment->service->price, 0) }}€ &middot; {{ $appointment->service->duration_minutes }} {{ __('minutes') }}</span>
                    </div>

                    <!-- Date & Time -->
                    <div>
                        <span class="block text-xs font-semibold uppercase tracking-wider text-zinc-400">{{ __('Date & Time') }}</span>
                        <span class="block text-base font-semibold text-zinc-900 dark:text-white mt-1">
                            {{ $appointment->appointment_date ? $appointment->appointment_date->format('l, F d, Y') : '' }}
                        </span>
                        <span class="text-sm text-zinc-500 dark:text-zinc-400 mt-0.5">
                            {{ $appointment->appointment_date ? $appointment->appointment_date->format('h:i A') : '' }}
                        </span>
                    </div>

                    <!-- Status -->
                    <div>
                        <span class="block text-xs font-semibold uppercase tracking-wider text-zinc-400">{{ __('Booking Status') }}</span>
                        <span class="mt-2 inline-flex items-center gap-1.5 rounded-full px-3 py-1 text-xs font-semibold transition-colors duration-150
                            {{ $appointment->status === 'confirmed' ? 'bg-emerald-50 text-emerald-700 dark:bg-emerald-950/30 dark:text-emerald-400 border border-emerald-100 dark:border-emerald-900/50' : '' }}
                            {{ $appointment->status === 'pending' ? 'bg-amber-50 text-amber-700 dark:bg-amber-950/30 dark:text-amber-400 border border-amber-100 dark:border-amber-900/50' : '' }}
                            {{ $appointment->status === 'cancelled' ? 'bg-rose-50 text-rose-700 dark:bg-rose-950/30 dark:text-rose-400 border border-rose-100 dark:border-rose-900/50' : '' }}">
                            <span class="h-1.5 w-1.5 rounded-full
                                {{ $appointment->status === 'confirmed' ? 'bg-emerald-500' : '' }}
                                {{ $appointment->status === 'pending' ? 'bg-amber-500' : '' }}
                                {{ $appointment->status === 'cancelled' ? 'bg-rose-500' : '' }}">
                            </span>
                            {{ ucfirst($appointment->status) }}
                        </span>
                    </div>

                    <!-- Created At -->
                    <div>
                        <span class="block text-xs font-semibold uppercase tracking-wider text-zinc-400">{{ __('Booked On') }}</span>
                        <span class="block text-sm text-zinc-900 dark:text-white mt-2 font-medium">
                            {{ $appointment->created_at ? $appointment->created_at->format('M d, Y \a\t h:i A') : '' }}
                        </span>
                    </div>
                </div>

                <!-- Notes Section -->
                <div class="border-t border-zinc-100 dark:border-zinc-800/50 pt-6">
                    <span class="block text-xs font-semibold uppercase tracking-wider text-zinc-400">{{ __('Additional Notes') }}</span>
                    <div class="mt-2 text-sm text-zinc-700 dark:text-zinc-300 bg-zinc-50 dark:bg-zinc-950 rounded-xl p-4 border border-zinc-200 dark:border-zinc-800/50 whitespace-pre-line leading-relaxed">
                        {{ $appointment->notes ?? __('No notes were provided for this appointment.') }}
                    </div>
                </div>

                <!-- Form Buttons -->
                <div class="flex items-center justify-end gap-4 pt-6 border-t border-zinc-200 dark:border-zinc-800">
                    @if($appointment->status !== 'cancelled')
                        <form action="{{ route('appointments.cancel', $appointment) }}" method="POST" onsubmit="return confirm('{{ __('Are you sure you want to cancel this booking?') }}')">
                            @csrf
                            @method('PUT')
                            <button type="submit" class="inline-flex items-center justify-center gap-2 rounded-xl border border-rose-200 dark:border-rose-900 bg-rose-50 dark:bg-rose-950/30 px-4 py-2.5 text-sm font-semibold text-rose-700 dark:text-rose-400 shadow-sm hover:bg-rose-100 dark:hover:bg-rose-950/50 transition duration-150">
                                {{ __('Cancel Booking') }}
                            </button>
                        </form>
                    @endif
                    <a href="{{ route('appointments.edit', $appointment) }}" class="inline-flex items-center justify-center gap-2 rounded-xl bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 transition duration-150">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L6.83 20.013a4.5 4.5 0 01-1.897 1.13l-2.685.8.8-2.686a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" />
                        </svg>
                        {{ __('Edit Details') }}
                    </a>
                </div>
            </div>
        </div>
    </div>
</x-layouts::app>
