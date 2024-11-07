<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight flex justify-between items-center">
            <span>{{ __('User List') }}</span>
            <a href="{{ route('users.create') }}" class="text-blue-500 hover:text-blue-700">Create New User</a>
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <table id="users-table" class="min-w-full bg-white border text-center">
                        <thead>
                            <tr>
                                <th class="py-2 px-4 border-b">Sl</th>
                                <th class="py-2 px-4 border-b">Name</th>
                                <th class="py-2 px-4 border-b">Email</th>
                                <th class="py-2 px-4 border-b">Role</th>
                                <th class="py-2 px-4 border-b">Image</th>
                                <th class="py-2 px-4 border-b">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $sl = 0; ?>
                            @foreach ($users as $user)
                            <tr>
                                <td class="py-2 px-4 border-b">{{++$sl}}</td>
                                <td class="py-2 px-4 border-b">{{$user->name}}</td>
                                <td class="py-2 px-4 border-b">{{$user->email}}</td>
                                <td class="py-2 px-4 border-b">{{$user->role}}</td>
                                <td class="py-2 px-4 border-b flex justify-center">
                                    @if ($user->image)
                                    <img src="{{ asset('storage/'.$user->image)}}" class="w-20 h-20" alt="">
                                    @endif
                                </td>
                                <td class="py-2 px-4 border-b">
                                    <x-primary-button>
                                        <a href="{{ route('users.edit', $user->id) }}">Edit</a>
                                    </x-primary-button>

                                    <x-danger-button class="delete-button" data-url="{{ route('users.destroy',$user->id) }}">
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

            // Get user ID
            let url = $(this).data('url');

            // Show confirmation dialog
            if (confirm('Are you sure you want to delete this user?')) {
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
                        alert('An error occurred while deleting the user.');
                    }
                });
            }
        });
    });
</script>

