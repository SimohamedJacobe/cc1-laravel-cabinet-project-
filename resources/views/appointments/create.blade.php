<x-layouts::app :title="__('Book Appointment')">
    <div class="max-w-3xl mx-auto p-4 sm:p-6 lg:p-8">
        <!-- Header Section -->
        <div class="pb-5 border-b border-zinc-200 dark:border-zinc-800 mb-6">
            <h1 class="text-3xl font-bold tracking-tight text-zinc-950 dark:text-zinc-50 font-sans">
                {{ __('Book Appointment') }}
            </h1>
            <p class="mt-1.5 text-sm text-zinc-500 dark:text-zinc-400">
                {{ __('Create a new appointment scheduling in the cabinet.') }}
            </p>
        </div>

        @if ($errors->any())
            <div class="flex flex-col gap-2 p-4 mb-6 text-sm text-rose-800 rounded-xl bg-rose-50 border border-rose-100 dark:bg-rose-950/30 dark:text-rose-400 dark:border-rose-900/50" role="alert">
                <div class="flex items-center gap-2 font-semibold">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                    <span>{{ __('Please correct the errors below:') }}</span>
                </div>
                <ul class="list-disc pl-5 space-y-0.5">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="rounded-2xl border border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-900 shadow-sm p-6 sm:p-8">
            <form action="{{ route('appointments.store') }}" method="POST" class="space-y-6">
                @csrf

                <!-- Patient selection (Only for Admin/Doctor) -->
                @if(in_array(auth()->user()->role, ['admin', 'doctor']))
                    <div class="space-y-2">
                        <label for="user_id" class="block text-sm font-semibold text-zinc-900 dark:text-zinc-200">
                            {{ __('Patient Name') }} <span class="text-rose-500">*</span>
                        </label>
                        <select id="user_id" name="user_id" required class="block w-full rounded-xl border-zinc-300 dark:border-zinc-700 bg-white dark:bg-zinc-950 text-zinc-900 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm transition duration-150">
                            <option value="">{{ __('Select a Patient') }}</option>
                            @foreach($patients as $patient)
                                <option value="{{ $patient->id }}" {{ old('user_id') == $patient->id ? 'selected' : '' }}>
                                    {{ $patient->name }} ({{ $patient->email }})
                                </option>
                            @endforeach
                        </select>
                        @error('user_id')
                            <p class="text-xs text-rose-600 dark:text-rose-400 mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                @endif

                <!-- Service selection -->
                <div class="space-y-2">
                    <label for="service_id" class="block text-sm font-semibold text-zinc-900 dark:text-zinc-200">
                        {{ __('Medical Service') }} <span class="text-rose-500">*</span>
                    </label>
                    <select id="service_id" name="service_id" required class="block w-full rounded-xl border-zinc-300 dark:border-zinc-700 bg-white dark:bg-zinc-950 text-zinc-900 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm transition duration-150">
                        <option value="">{{ __('Select a Service') }}</option>
                        @foreach($services as $service)
                            <option value="{{ $service->id }}" {{ old('service_id') == $service->id ? 'selected' : '' }}>
                                {{ $service->name }} - {{ number_format($service->price, 0) }}€
                            </option>
                        @endforeach
                    </select>
                    @error('service_id')
                        <p class="text-xs text-rose-600 dark:text-rose-400 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Date & Time Selection -->
                <div class="space-y-2">
                    <label for="appointment_date" class="block text-sm font-semibold text-zinc-900 dark:text-zinc-200">
                        {{ __('Date & Time') }} <span class="text-rose-500">*</span>
                    </label>
                    <input type="datetime-local" id="appointment_date" name="appointment_date" required value="{{ old('appointment_date') }}" class="block w-full rounded-xl border-zinc-300 dark:border-zinc-700 bg-white dark:bg-zinc-950 text-zinc-900 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm transition duration-150">
                    <p class="text-xs text-zinc-500 dark:text-zinc-400 mt-1">
                        {{ __('Must be a future date and time.') }}
                    </p>
                    @error('appointment_date')
                        <p class="text-xs text-rose-600 dark:text-rose-400 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Status Select (Only for Admin/Doctor) -->
                @if(in_array(auth()->user()->role, ['admin', 'doctor']))
                    <div class="space-y-2">
                        <label for="status" class="block text-sm font-semibold text-zinc-900 dark:text-zinc-200">
                            {{ __('Booking Status') }} <span class="text-rose-500">*</span>
                        </label>
                        <select id="status" name="status" class="block w-full rounded-xl border-zinc-300 dark:border-zinc-700 bg-white dark:bg-zinc-950 text-zinc-900 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm transition duration-150">
                            <option value="pending" {{ old('status') === 'pending' ? 'selected' : '' }}>{{ __('Pending') }}</option>
                            <option value="confirmed" {{ old('status') === 'confirmed' ? 'selected' : '' }}>{{ __('Confirmed') }}</option>
                            <option value="cancelled" {{ old('status') === 'cancelled' ? 'selected' : '' }}>{{ __('Cancelled') }}</option>
                        </select>
                        @error('status')
                            <p class="text-xs text-rose-600 dark:text-rose-400 mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                @endif

                <!-- Notes Textarea -->
                <div class="space-y-2">
                    <label for="notes" class="block text-sm font-semibold text-zinc-900 dark:text-zinc-200">
                        {{ __('Additional Notes') }} <span class="text-zinc-400 text-xs font-normal">({{ __('Optional') }})</span>
                    </label>
                    <textarea id="notes" name="notes" rows="4" placeholder="{{ __('Describe any specific symptoms, preferences or details here...') }}" class="block w-full rounded-xl border-zinc-300 dark:border-zinc-700 bg-white dark:bg-zinc-950 text-zinc-900 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm transition duration-150">{{ old('notes') }}</textarea>
                    @error('notes')
                        <p class="text-xs text-rose-600 dark:text-rose-400 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Action buttons -->
                <div class="flex items-center justify-end gap-4 pt-4 border-t border-zinc-200 dark:border-zinc-800">
                    <a href="{{ route('appointments.index') }}" class="inline-flex items-center justify-center rounded-xl border border-zinc-300 dark:border-zinc-700 bg-white dark:bg-zinc-950 px-4 py-2.5 text-sm font-semibold text-zinc-700 dark:text-zinc-300 shadow-sm hover:bg-zinc-50 dark:hover:bg-zinc-900 transition duration-150">
                        {{ __('Cancel') }}
                    </a>
                    <button type="submit" class="inline-flex items-center justify-center gap-2 rounded-xl bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 transition duration-150">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                        </svg>
                        {{ __('Confirm Booking') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-layouts::app>
