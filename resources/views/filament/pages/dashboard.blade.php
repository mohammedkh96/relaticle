<x-filament-panels::page>
    <div class="space-y-6">
        <div class="text-center mb-8">
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white">
                Welcome to Invest Expo CRM
            </h1>
            <p class="mt-2 text-gray-600 dark:text-gray-400">
                Manage your events, companies, participations, and visitors
            </p>
        </div>

        <x-filament-widgets::widgets :widgets="$this->getWidgets()" :columns="$this->getColumns()" />

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-8">
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
                    Quick Actions
                </h3>
                <div class="space-y-3">
                    <a href="{{ route('filament.sysadmin.resources.events.create') }}"
                        class="block p-3 bg-blue-50 dark:bg-blue-900/20 rounded-lg hover:bg-blue-100 dark:hover:bg-blue-900/30 transition">
                        <div class="flex items-center">
                            <svg class="w-5 h-5 text-blue-600 dark:text-blue-400 mr-3" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z">
                                </path>
                            </svg>
                            <span class="text-gray-900 dark:text-white font-medium">Create New Event</span>
                        </div>
                    </a>

                    <a href="{{ route('filament.sysadmin.resources.companies.create') }}"
                        class="block p-3 bg-green-50 dark:bg-green-900/20 rounded-lg hover:bg-green-100 dark:hover:bg-green-900/30 transition">
                        <div class="flex items-center">
                            <svg class="w-5 h-5 text-green-600 dark:text-green-400 mr-3" fill="none"
                                stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4">
                                </path>
                            </svg>
                            <span class="text-gray-900 dark:text-white font-medium">Add Company</span>
                        </div>
                    </a>

                    <a href="{{ route('filament.sysadmin.resources.participations.create') }}"
                        class="block p-3 bg-purple-50 dark:bg-purple-900/20 rounded-lg hover:bg-purple-100 dark:hover:bg-purple-900/30 transition">
                        <div class="flex items-center">
                            <svg class="w-5 h-5 text-purple-600 dark:text-purple-400 mr-3" fill="none"
                                stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z">
                                </path>
                            </svg>
                            <span class="text-gray-900 dark:text-white font-medium">Register Participation</span>
                        </div>
                    </a>

                    <a href="{{ route('filament.sysadmin.resources.visitors.create') }}"
                        class="block p-3 bg-orange-50 dark:bg-orange-900/20 rounded-lg hover:bg-orange-100 dark:hover:bg-orange-900/30 transition">
                        <div class="flex items-center">
                            <svg class="w-5 h-5 text-orange-600 dark:text-orange-400 mr-3" fill="none"
                                stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z">
                                </path>
                            </svg>
                            <span class="text-gray-900 dark:text-white font-medium">Add Visitor</span>
                        </div>
                    </a>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
                    Recent Activity
                </h3>
                <div class="space-y-3 text-gray-600 dark:text-gray-400">
                    <p class="text-sm">
                        View your recent events, participations, and visitor registrations in their respective sections.
                    </p>
                    <div class="pt-4 border-t border-gray-200 dark:border-gray-700">
                        <p class="text-xs text-gray-500 dark:text-gray-500">
                            Navigate using the sidebar to manage all aspects of your Invest Expo events.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-filament-panels::page>