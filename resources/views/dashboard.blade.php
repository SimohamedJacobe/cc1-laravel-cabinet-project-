<x-layouts::app :title="__('Dashboard')">
    <div class="space-y-6 max-w-7xl mx-auto p-4 sm:p-6 lg:p-8">
        <!-- Welcome banner -->
        <div class="relative overflow-hidden rounded-2xl bg-gradient-to-r from-indigo-600 to-violet-700 p-6 sm:p-8 shadow-sm">
            <div class="relative z-10 max-w-md">
                <h1 class="text-3xl font-bold tracking-tight text-white font-sans">
                    {{ __('Welcome back,') }} {{ auth()->user()->name }}!
                </h1>
                <p class="mt-2 text-sm text-indigo-100 leading-relaxed">
                    {{ __('Welcome to your Medical Cabinet. Easily manage your appointment bookings, browse available medical services, and monitor your schedule.') }}
                </p>
                <div class="mt-6 flex flex-wrap gap-3">
                    <a href="{{ route('appointments.index') }}" class="inline-flex items-center justify-center rounded-xl bg-white px-4 py-2 text-sm font-semibold text-indigo-600 shadow-sm hover:bg-indigo-50 transition duration-150">
                        {{ __('View Appointments') }}
                    </a>
                    <a href="{{ route('appointments.create') }}" class="inline-flex items-center justify-center rounded-xl border border-indigo-400 bg-transparent px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-500/30 transition duration-150">
                        {{ __('+ Book New Appointment') }}
                    </a>
                </div>
            </div>
            <!-- Decorative gradient circle -->
            <div class="absolute right-0 top-0 -mr-20 -mt-20 h-80 w-80 rounded-full bg-indigo-500/20 blur-2xl"></div>
        </div>

        <!-- Quick actions / portal routes -->
        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
            <!-- Bookings card -->
            <div class="flex flex-col justify-between p-6 bg-white dark:bg-zinc-900 rounded-2xl border border-zinc-200 dark:border-zinc-800 shadow-sm hover:border-indigo-500 dark:hover:border-indigo-400 transition duration-200 group">
                <div>
                    <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-indigo-50 dark:bg-indigo-950/50 text-indigo-600 dark:text-indigo-400 mb-4 group-hover:scale-110 transition duration-150">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-bold text-zinc-950 dark:text-zinc-50">{{ __('Manage Bookings') }}</h3>
                    <p class="mt-2 text-sm text-zinc-500 dark:text-zinc-400">
                        {{ __('View your schedule, update consultation times, and cancel bookings if necessary.') }}
                    </p>
                </div>
                <div class="mt-6 pt-4 border-t border-zinc-100 dark:border-zinc-800/50">
                    <a href="{{ route('appointments.index') }}" class="text-sm font-semibold text-indigo-600 dark:text-indigo-400 hover:text-indigo-500 flex items-center gap-1.5">
                        {{ __('Go to Appointments') }}
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3" />
                        </svg>
                    </a>
                </div>
            </div>

            <!-- Services portfolio card -->
            <div class="flex flex-col justify-between p-6 bg-white dark:bg-zinc-900 rounded-2xl border border-zinc-200 dark:border-zinc-800 shadow-sm hover:border-indigo-500 dark:hover:border-indigo-400 transition duration-200 group">
                <div>
                    <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-indigo-50 dark:bg-indigo-950/50 text-indigo-600 dark:text-indigo-400 mb-4 group-hover:scale-110 transition duration-150">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h3.75M9 15h3.375c.621 0 1.125-.504 1.125-1.125V11.25c0-1.104-.896-2-2-2H9m1.5-3.375a1.875 1.875 0 11-3.75 0 1.875 1.875 0 013.75 0zm6.562 12.375a1.875 1.875 0 11-3.75 0 1.875 1.875 0 013.75 0zM18 10.5a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0z" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-bold text-zinc-950 dark:text-zinc-50">{{ __('Medical Services') }}</h3>
                    <p class="mt-2 text-sm text-zinc-500 dark:text-zinc-400">
                        {{ __('Browse all available medical services, prices, durations, and cabinet specialties.') }}
                    </p>
                </div>
                <div class="mt-6 pt-4 border-t border-zinc-100 dark:border-zinc-800/50">
                    <a href="{{ Route::has('services.index') ? route('services.index') : '#' }}" class="text-sm font-semibold text-indigo-600 dark:text-indigo-400 hover:text-indigo-500 flex items-center gap-1.5">
                        {{ __('Explore Services') }}
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3" />
                        </svg>
                    </a>
                </div>
            </div>

            <!-- Profile settings card -->
            <div class="flex flex-col justify-between p-6 bg-white dark:bg-zinc-900 rounded-2xl border border-zinc-200 dark:border-zinc-800 shadow-sm hover:border-indigo-500 dark:hover:border-indigo-400 transition duration-200 group">
                <div>
                    <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-indigo-50 dark:bg-indigo-950/50 text-indigo-600 dark:text-indigo-400 mb-4 group-hover:scale-110 transition duration-150">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9.594 3.94c.09-.542.56-.94 1.11-.94h2.593c.55 0 1.02.398 1.11.94l.213 1.281c.063.374.313.686.645.87.074.04.147.083.22.127.324.196.72.257 1.075.124l1.217-.456a1.125 1.125 0 011.37.49l1.296 2.247a1.125 1.125 0 01-.26 1.43l-1.003.828c-.293.241-.438.613-.43.992a7.723 7.723 0 010 .255c-.008.378.137.75.43.991l1.004.827c.424.35.534.954.26 1.43l-1.298 2.247a1.125 1.125 0 01-1.369.491l-1.217-.456c-.355-.133-.75-.072-1.076.124a6.57 6.57 0 01-.22.128c-.331.183-.581.495-.644.869l-.213 1.28c-.09.543-.56.941-1.11.941h-2.594c-.55 0-1.02-.398-1.11-.94l-.213-1.281c-.062-.374-.312-.686-.644-.87a6.52 6.52 0 01-.22-.127c-.325-.196-.72-.257-1.076-.124l-1.217.456a1.125 1.125 0 01-1.369-.49l-1.297-2.247a1.125 1.125 0 01.26-1.43l1.004-.827c.292-.24.437-.613.43-.992a6.932 6.932 0 010-.255c.007-.378-.138-.75-.43-.991l-1.004-.827a1.125 1.125 0 01-.26-1.43l1.297-2.247a1.125 1.125 0 011.37-.491l1.216.456c.356.133.751.072 1.076-.124.072-.044.146-.087.22-.128.332-.183.582-.495.645-.869l.214-1.28z" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-bold text-zinc-950 dark:text-zinc-50">{{ __('Cabinet Settings') }}</h3>
                    <p class="mt-2 text-sm text-zinc-500 dark:text-zinc-400">
                        {{ __('Manage your medical profile settings, security preferences, and update personal account details.') }}
                    </p>
                </div>
                <div class="mt-6 pt-4 border-t border-zinc-100 dark:border-zinc-800/50">
                    <a href="{{ route('profile.edit') }}" class="text-sm font-semibold text-indigo-600 dark:text-indigo-400 hover:text-indigo-500 flex items-center gap-1.5">
                        {{ __('Go to Settings') }}
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3" />
                        </svg>
                    </a>
                </div>
            </div>
        </div>
    </div>
</x-layouts::app>
