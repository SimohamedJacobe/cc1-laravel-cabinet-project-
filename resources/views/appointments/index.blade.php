<x-layouts::app :title="__('Appointments')">
    <div x-data="{ showCreateModal: {{ $errors->any() ? 'true' : 'false' }}, showCancelModal: false, cancelRoute: '' }" class="space-y-6 max-w-7xl mx-auto p-4 sm:p-6 lg:p-8">
        <!-- Header Section -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pb-5 border-b border-zinc-200 dark:border-zinc-800">
            <div>
                <h1 class="text-3xl font-bold tracking-tight text-zinc-950 dark:text-zinc-50 font-sans">
                    {{ __('Appointments') }}
                </h1>
                <p class="mt-1.5 text-sm text-zinc-500 dark:text-zinc-400">
                    {{ __('Manage and monitor all cabinet consultations and schedules.') }}
                </p>
            </div>
            <div>
                <button @click="showCreateModal = true" class="inline-flex items-center justify-center gap-2 rounded-xl bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 transition-colors duration-200">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                    </svg>
                    {{ __('+ Book New Appointment') }}
                </button>
            </div>
        </div>

        @if(session('success'))
            <div class="flex items-center gap-3 p-4 text-sm text-emerald-800 rounded-xl bg-emerald-50 border border-emerald-100 dark:bg-emerald-950/30 dark:text-emerald-400 dark:border-emerald-900/50" role="alert">
                <svg class="h-5 w-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span class="font-medium">{{ session('success') }}</span>
            </div>
        @endif

        <!-- Appointments Stats cards for a premium feel -->
        <div class="grid grid-cols-1 gap-5 sm:grid-cols-3">
            <div class="relative overflow-hidden rounded-2xl border border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-900 p-6 shadow-sm">
                <dt class="text-sm font-medium text-zinc-500 dark:text-zinc-400">{{ __('Total Bookings') }}</dt>
                <dd class="mt-2 text-3xl font-semibold tracking-tight text-zinc-900 dark:text-white">{{ $appointments->count() }}</dd>
            </div>
            <div class="relative overflow-hidden rounded-2xl border border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-900 p-6 shadow-sm">
                <dt class="text-sm font-medium text-zinc-500 dark:text-zinc-400">{{ __('Confirmed') }}</dt>
                <dd class="mt-2 text-3xl font-semibold tracking-tight text-emerald-600 dark:text-emerald-400">
                    {{ $appointments->where('status', 'confirmed')->count() }}
                </dd>
            </div>
            <div class="relative overflow-hidden rounded-2xl border border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-900 p-6 shadow-sm">
                <dt class="text-sm font-medium text-zinc-500 dark:text-zinc-400">{{ __('Pending Approvals') }}</dt>
                <dd class="mt-2 text-3xl font-semibold tracking-tight text-amber-600 dark:text-amber-400">
                    {{ $appointments->where('status', 'pending')->count() }}
                </dd>
            </div>
        </div>

        <!-- Search Input UI -->
        <div class="relative max-w-md">
            <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                <svg class="h-5 w-5 text-zinc-400 dark:text-zinc-500" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.602 10.602z" />
                </svg>
            </div>
            <input type="text" 
                   id="search-appointments" 
                   placeholder="{{ __('Search appointments...') }}" 
                   class="block w-full rounded-xl border border-zinc-300 dark:border-zinc-700 bg-white dark:bg-zinc-950 pl-10 pr-4 py-2.5 text-sm text-zinc-900 dark:text-white placeholder-zinc-400 dark:placeholder-zinc-500 focus:border-indigo-500 focus:ring-indigo-500 focus:outline-none transition duration-150 shadow-sm"
            />
        </div>

        <!-- Table Card -->
        <div class="overflow-hidden rounded-2xl border border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-900 shadow-sm">
            <div class="min-w-full divide-y divide-zinc-200 dark:divide-zinc-800">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-zinc-200 dark:divide-zinc-800">
                        <thead class="bg-zinc-50 dark:bg-zinc-900/50">
                            <tr>
                                <th scope="col" class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-zinc-500 dark:text-zinc-400">
                                    {{ __('Patient Name') }}
                                </th>
                                <th scope="col" class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-zinc-500 dark:text-zinc-400">
                                    {{ __('Service') }}
                                </th>
                                <th scope="col" class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-zinc-500 dark:text-zinc-400">
                                    {{ __('Date & Time') }}
                                </th>
                                <th scope="col" class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-zinc-500 dark:text-zinc-400">
                                    {{ __('Status') }}
                                </th>
                                <th scope="col" class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-zinc-500 dark:text-zinc-400">
                                    {{ __('Notes') }}
                                </th>
                                <th scope="col" class="relative px-6 py-4 text-right text-xs font-semibold uppercase tracking-wider text-zinc-500 dark:text-zinc-400">
                                    <span class="sr-only">{{ __('Actions') }}</span>
                                </th>
                            </tr>
                        </thead>
                        <tbody id="appointments-tbody" class="divide-y divide-zinc-200 dark:divide-zinc-800 bg-white dark:bg-zinc-900">
                            @include('appointments.partials.rows')
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Quick Add Modal -->
        <div x-show="showCreateModal" 
             class="fixed inset-0 z-50 flex items-center justify-center p-4" 
             style="display: none;"
             role="dialog" 
             aria-modal="true">
            
            <!-- Backdrop -->
            <div x-show="showCreateModal" 
                 x-transition:enter="ease-out duration-300"
                 x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100"
                 x-transition:leave="ease-in duration-200"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0"
                 class="fixed inset-0 bg-zinc-950/70 dark:bg-zinc-950/80 backdrop-blur-sm"
                 @click="showCreateModal = false"></div>

            <!-- Modal Content Card -->
            <div x-show="showCreateModal"
                 x-transition:enter="ease-out duration-300"
                 x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                 x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave="ease-in duration-200"
                 x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                 class="relative overflow-hidden rounded-2xl border border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-900 shadow-2xl max-w-xl w-full p-6 sm:p-8 z-10 max-h-[90vh] overflow-y-auto">
                
                <div class="flex justify-between items-center pb-4 border-b border-zinc-200 dark:border-zinc-800 mb-6">
                    <h2 class="text-xl font-bold text-zinc-950 dark:text-zinc-50">{{ __('Book New Appointment') }}</h2>
                    <button @click="showCreateModal = false" class="text-zinc-400 hover:text-zinc-600 dark:hover:text-zinc-200 transition-colors">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                @if ($errors->any())
                    <div class="flex flex-col gap-2 p-4 mb-6 text-sm text-rose-800 rounded-xl bg-rose-50 border border-rose-100 dark:bg-rose-950/30 dark:text-rose-400 dark:border-rose-900/50" role="alert">
                        <ul class="list-disc pl-5 space-y-0.5">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('appointments.store') }}" method="POST" class="space-y-5">
                    @csrf

                    <!-- Patient selection (Only for Admin/Doctor) -->
                    @if(in_array(auth()->user()->role, ['admin', 'doctor']))
                        <div class="space-y-1.5">
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
                        </div>
                    @endif

                    <!-- Service selection -->
                    <div class="space-y-1.5">
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
                    </div>

                    <!-- Date & Time Selection -->
                    <div class="space-y-1.5">
                        <label for="appointment_date" class="block text-sm font-semibold text-zinc-900 dark:text-zinc-200">
                            {{ __('Date & Time') }} <span class="text-rose-500">*</span>
                        </label>
                        <input type="datetime-local" id="appointment_date" name="appointment_date" required value="{{ old('appointment_date') }}" class="block w-full rounded-xl border-zinc-300 dark:border-zinc-700 bg-white dark:bg-zinc-950 text-zinc-900 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm transition duration-150">
                    </div>

                    <!-- Status Select (Only for Admin/Doctor) -->
                    @if(in_array(auth()->user()->role, ['admin', 'doctor']))
                        <div class="space-y-1.5">
                            <label for="status" class="block text-sm font-semibold text-zinc-900 dark:text-zinc-200">
                                {{ __('Booking Status') }} <span class="text-rose-500">*</span>
                            </label>
                            <select id="status" name="status" class="block w-full rounded-xl border-zinc-300 dark:border-zinc-700 bg-white dark:bg-zinc-950 text-zinc-900 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm transition duration-150">
                                <option value="pending" {{ old('status') === 'pending' ? 'selected' : '' }}>{{ __('Pending') }}</option>
                                <option value="confirmed" {{ old('status') === 'confirmed' ? 'selected' : '' }}>{{ __('Confirmed') }}</option>
                                <option value="cancelled" {{ old('status') === 'cancelled' ? 'selected' : '' }}>{{ __('Cancelled') }}</option>
                            </select>
                        </div>
                    @endif

                    <!-- Notes Textarea -->
                    <div class="space-y-1.5">
                        <label for="notes" class="block text-sm font-semibold text-zinc-900 dark:text-zinc-200">
                            {{ __('Additional Notes') }}
                        </label>
                        <textarea id="notes" name="notes" rows="3" placeholder="{{ __('Symptoms or details...') }}" class="block w-full rounded-xl border-zinc-300 dark:border-zinc-700 bg-white dark:bg-zinc-950 text-zinc-900 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm transition duration-150">{{ old('notes') }}</textarea>
                    </div>

                    <!-- Action buttons -->
                    <div class="flex items-center justify-end gap-3 pt-4 border-t border-zinc-200 dark:border-zinc-800">
                        <button type="button" @click="showCreateModal = false" class="inline-flex items-center justify-center rounded-xl border border-zinc-300 dark:border-zinc-700 bg-white dark:bg-zinc-950 px-4 py-2 text-sm font-semibold text-zinc-700 dark:text-zinc-300 shadow-sm hover:bg-zinc-50 dark:hover:bg-zinc-900 transition duration-150">
                            {{ __('Cancel') }}
                        </button>
                        <button type="submit" class="inline-flex items-center justify-center gap-2 rounded-xl bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 transition duration-150">
                            {{ __('Confirm Booking') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Cancellation Confirmation Modal -->
        <div x-show="showCancelModal" 
             class="fixed inset-0 z-50 flex items-center justify-center p-4" 
             style="display: none;"
             role="dialog" 
             aria-modal="true">
            
            <!-- Backdrop -->
            <div x-show="showCancelModal" 
                 x-transition:enter="ease-out duration-300"
                 x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100"
                 x-transition:leave="ease-in duration-200"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0"
                 class="fixed inset-0 bg-zinc-950/70 dark:bg-zinc-950/80 backdrop-blur-sm"
                 @click="showCancelModal = false"></div>

            <!-- Modal Content Card -->
            <div x-show="showCancelModal"
                 x-transition:enter="ease-out duration-300"
                 x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                 x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave="ease-in duration-200"
                 x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                 class="relative overflow-hidden rounded-2xl border border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-900 shadow-2xl max-w-md w-full p-6 z-10">
                
                <div class="flex items-start gap-4">
                    <div class="flex h-10 w-10 flex-shrink-0 items-center justify-center rounded-full bg-rose-50 dark:bg-rose-950/50 text-rose-600 dark:text-rose-400">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                    </div>
                    <div class="space-y-1.5 flex-1">
                        <h2 class="text-lg font-bold text-zinc-950 dark:text-zinc-50">{{ __('Cancel Appointment') }}</h2>
                        <p class="text-sm text-zinc-500 dark:text-zinc-400">
                            {{ __('Êtes-vous sûr de vouloir annuler ce rendez-vous ?') }}
                        </p>
                    </div>
                </div>

                <div class="flex items-center justify-end gap-3 mt-6 pt-4 border-t border-zinc-200 dark:border-zinc-800">
                    <button type="button" @click="showCancelModal = false" class="inline-flex items-center justify-center rounded-xl border border-zinc-300 dark:border-zinc-700 bg-white dark:bg-zinc-950 px-4 py-2 text-sm font-semibold text-zinc-700 dark:text-zinc-300 shadow-sm hover:bg-zinc-50 dark:hover:bg-zinc-900 transition duration-150">
                        {{ __('Go Back') }}
                    </button>
                    
                    <form :action="cancelRoute" method="POST">
                        @csrf
                        @method('PUT')
                        <button type="submit" class="inline-flex items-center justify-center gap-2 rounded-xl bg-rose-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-rose-500 transition duration-150">
                            {{ __('Confirm Cancellation') }}
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Axios and Search Script -->
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const searchInput = document.getElementById('search-appointments');
            const tbody = document.getElementById('appointments-tbody');
            let debounceTimer;

            if (searchInput && tbody) {
                searchInput.addEventListener('input', function (e) {
                    const query = e.target.value;

                    clearTimeout(debounceTimer);
                    debounceTimer = setTimeout(() => {
                        axios.get('{{ route("appointments.index") }}', {
                            params: { search: query },
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest'
                            }
                        })
                        .then(response => {
                            tbody.innerHTML = response.data;
                        })
                        .catch(error => {
                            console.error('Error fetching search results:', error);
                        });
                    }, 300); // 300ms debounce
                });
            }
        });
    </script>
</x-layouts::app>
