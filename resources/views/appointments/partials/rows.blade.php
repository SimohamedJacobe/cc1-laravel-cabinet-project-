@forelse($appointments as $appointment)
    <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-900/50 transition-colors duration-150">
        <!-- Patient Name -->
        <td class="whitespace-nowrap px-6 py-4 text-sm">
            <div class="flex items-center gap-3">
                <div class="flex h-10 w-10 items-center justify-center rounded-full bg-indigo-50 dark:bg-indigo-950/50 text-indigo-600 dark:text-indigo-400 font-semibold text-sm">
                    {{ $appointment->user->initials() }}
                </div>
                <div>
                    <div class="font-semibold text-zinc-900 dark:text-white">{{ $appointment->user->name }}</div>
                    <div class="text-xs text-zinc-500 dark:text-zinc-400">{{ $appointment->user->email }}</div>
                </div>
            </div>
        </td>
        
        <!-- Service -->
        <td class="whitespace-nowrap px-6 py-4 text-sm text-zinc-700 dark:text-zinc-300">
            <div class="font-medium text-zinc-900 dark:text-white">
                {{ $appointment->service->name }}
            </div>
            <div class="text-xs text-zinc-500 dark:text-zinc-400">
                {{ number_format($appointment->service->price, 0) }}€ &middot; {{ $appointment->service->duration_minutes }} {{ __('mins') }}
            </div>
        </td>

        <!-- Date & Time -->
        <td class="whitespace-nowrap px-6 py-4 text-sm text-zinc-700 dark:text-zinc-300">
            <div class="font-medium text-zinc-900 dark:text-white">
                {{ $appointment->appointment_date ? $appointment->appointment_date->format('M d, Y') : '' }}
            </div>
            <div class="text-xs text-zinc-500 dark:text-zinc-400">
                {{ $appointment->appointment_date ? $appointment->appointment_date->format('h:i A') : '' }}
            </div>
        </td>

        <!-- Status -->
        <td class="whitespace-nowrap px-6 py-4 text-sm">
            <span class="inline-flex items-center gap-1.5 rounded-full px-2.5 py-1 text-xs font-semibold transition-colors duration-150
                {{ $appointment->status === 'confirmed' ? 'bg-emerald-50 text-emerald-700 dark:bg-emerald-950/30 dark:text-emerald-400 border border-emerald-100 dark:border-emerald-900/50' : '' }}
                {{ $appointment->status === 'pending' ? 'bg-amber-50 text-amber-700 dark:bg-amber-950/30 dark:text-amber-400 border border-amber-100 dark:border-amber-900/50' : '' }}
                {{ $appointment->status === 'cancelled' ? 'bg-rose-50 text-rose-700 dark:bg-rose-950/30 dark:text-rose-400 border border-rose-100 dark:border-rose-900/50' : '' }}">
                <span class="h-1.5 w-1.5 rounded-full
                    {{ $appointment->status === 'confirmed' ? 'bg-emerald-500' : '' }}
                    {{ $appointment->status === 'pending' ? 'bg-amber-500' : '' }}
                    {{ $appointment->status === 'cancelled' ? 'bg-rose-500' : '' }}">
                </span>
                {{ __(ucfirst($appointment->status)) }}
            </span>
        </td>

        <!-- Notes -->
        <td class="px-6 py-4 text-sm text-zinc-500 dark:text-zinc-400 max-w-xs truncate" title="{{ $appointment->notes }}">
            {{ $appointment->notes ?? '-' }}
        </td>

        <!-- Actions -->
        <td class="whitespace-nowrap px-6 py-4 text-right text-sm font-medium">
            <div class="flex items-center justify-end gap-3">
                <a href="{{ route('appointments.edit', $appointment) }}" class="inline-flex items-center justify-center rounded-lg border border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-950 px-3 py-1.5 text-xs font-semibold text-zinc-700 dark:text-zinc-300 shadow-sm hover:bg-zinc-50 dark:hover:bg-zinc-900 transition-colors duration-150">
                    {{ __('Edit') }}
                </a>
                @if($appointment->status !== 'cancelled')
                    <button @click="cancelRoute = '{{ route('appointments.cancel', $appointment) }}'; showCancelModal = true" type="button" class="inline-flex items-center justify-center rounded-lg border border-rose-200 dark:border-rose-900 bg-rose-50 dark:bg-rose-950/30 px-3 py-1.5 text-xs font-semibold text-rose-700 dark:text-rose-400 shadow-sm hover:bg-rose-100 dark:hover:bg-rose-950/50 transition-colors duration-150">
                        {{ __('Cancel') }}
                    </button>
                @endif
            </div>
        </td>
    </tr>
@empty
    <tr>
        <td colspan="6" class="px-6 py-12 text-center">
            <div class="flex flex-col items-center justify-center">
                <div class="flex h-12 w-12 items-center justify-center rounded-full bg-zinc-100 dark:bg-zinc-800 text-zinc-400 dark:text-zinc-500">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5" />
                    </svg>
                </div>
                <h3 class="mt-4 text-sm font-semibold text-zinc-900 dark:text-white">{{ __('No appointments found') }}</h3>
                <p class="mt-1 text-sm text-zinc-500 dark:text-zinc-400">{{ __('Get started by booking your first medical consultation.') }}</p>
                <div class="mt-6">
                    <button @click="showCreateModal = true" class="inline-flex items-center justify-center gap-2 rounded-xl bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 transition-colors duration-200">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                        </svg>
                        {{ __('+ Book New Appointment') }}
                    </button>
                </div>
            </div>
        </td>
    </tr>
@endforelse
