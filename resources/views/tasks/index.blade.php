<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight flex justify-between items-center">
            <span>{{ __('Task List') }}</span>
            <a href="{{ route('tasks.create') }}" class="text-blue-500 hover:text-blue-700">Create New Task</a>
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <table id="tasks-table" class="min-w-full bg-white border text-center">
                        <thead>
                            <tr>
                                <th class="py-2 px-4 border-b">Sl</th>
                                <th class="py-2 px-4 border-b">User</th>
                                <th class="py-2 px-4 border-b">Project Title</th>
                                <th class="py-2 px-4 border-b">Task Title</th>
                                <th class="py-2 px-4 border-b">Deadline</th>
                                <th class="py-2 px-4 border-b">Status</th>
                                <th class="py-2 px-4 border-b">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $sl = 0; ?>
                            @foreach ($tasks as $task)
                            <tr>
                                <td class="py-2 px-4 border-b">{{++$sl}}</td>
                                <td class="py-2 px-4 border-b">{{$task->user->name ?? ''}}</td>
                                <td class="py-2 px-4 border-b">{{$task->project->title ?? ''}}</td>
                                <td class="py-2 px-4 border-b">{{$task->title}}</td>
                                <td class="py-2 px-4 border-b">{{$task->deadline->format('M d, Y')}}</td>
                                <td class="py-2 px-4 border-b">{{$task->status}}</td>
                                <td class="py-2 px-4 border-b">
                                    <x-primary-button>
                                        <a href="{{ route('tasks.edit', $task->id) }}">Edit</a>
                                    </x-primary-button>

                                    <x-danger-button class="delete-button" data-url="{{ route('tasks.destroy',$task->id) }}">
                                        Delete
                                    </x-danger-button>
                                </td>

                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

<script>
    $(document).ready(function() {
        // Handle delete button click
        $('.delete-button').on('click', function(e) {
            e.preventDefault();

            // Get task ID
            let url = $(this).data('url');

            // Show confirmation dialog
            if (confirm('Are you sure you want to delete this task?')) {
                // Perform AJAX request
                $.ajax({
                    url: url,
                    type: 'DELETE',
                    data: {
                        _token: $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        alert(response.success);
                        // Reload the page or remove the row from the table
                        location.reload();
                    },
                    error: function(response) {
                        alert('An error occurred while deleting the task.');
                    }
                });
            }
        });
    });
</script>

