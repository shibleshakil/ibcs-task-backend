<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <!-- Statistics Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <!-- Total Tasks Card -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        <h3 class="text-lg font-semibold">{{ __('Total Tasks') }}</h3>
                        <p class="text-2xl font-bold">{{ $totalTasks }}</p>
                    </div>
                </div>

                <!-- Pending Tasks Card -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        <h3 class="text-lg font-semibold">{{ __('Pending Tasks') }}</h3>
                        <p class="text-2xl font-bold">{{ $pendingTasks }}</p>
                    </div>
                </div>

                <!-- Processing Tasks Card -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        <h3 class="text-lg font-semibold">{{ __('Processing Tasks') }}</h3>
                        <p class="text-2xl font-bold">{{ $processingTasks }}</p>
                    </div>
                </div>

                <!-- Completed Tasks Card -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        <h3 class="text-lg font-semibold">{{ __('Completed Tasks') }}</h3>
                        <p class="text-2xl font-bold">{{ $completedTasks }}</p>
                    </div>
                </div>
            </div>

            <!-- Upcoming Tasks Table -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mt-6">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-semibold mb-4">{{ __('Upcoming Tasks') }}</h3>
                    <table class="min-w-full bg-white border border-gray-300">
                        <thead>
                            <tr>
                                <th class="px-4 py-2 border-b text-left text-sm font-medium text-gray-700">User</th>
                                <th class="px-4 py-2 border-b text-left text-sm font-medium text-gray-700">Title</th>
                                <th class="px-4 py-2 border-b text-left text-sm font-medium text-gray-700">Status</th>
                                <th class="px-4 py-2 border-b text-left text-sm font-medium text-gray-700">Deadline</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($upcomingTasks as $task)
                                <tr>
                                    <td class="px-4 py-2 border-b text-sm text-gray-700">{{ $task->user->name ?? '' }}</td>
                                    <td class="px-4 py-2 border-b text-sm text-gray-700">{{ $task->title }}</td>
                                    <td class="px-4 py-2 border-b text-sm text-gray-700">{{ ucfirst($task->status) }}</td>
                                    <td class="px-4 py-2 border-b text-sm text-gray-700">{{ $task->deadline->format('M d, Y') }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="px-4 py-2 border-b text-sm text-gray-500 text-center">{{ __('No upcoming tasks') }}</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
