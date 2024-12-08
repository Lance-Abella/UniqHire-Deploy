@extends('layout')

@section('page-title', 'PWDs Users')
@section('page-content')
<div class="users-container">
    <div class="row mt-4 mb-2">
        <div class="text-start header-texts back-link-container border-bottom">
            Person with Disabilities.
        </div>
    </div>
    <table class="table table-striped table-hover">
        <thead>
            <tr>
                <td class="table-head">PWD ID Number</td>
                <td class="table-head">Name</td>
                <td class="table-head">Email</td>
                <td class="table-head">Contact Number</td>
                <td class="table-head">Location</td>
                <td class="table-head">Disability</td>
                <td class="table-head" colspan="2">--</td>
            </tr>
        </thead>
        <tbody class="table-group-divider text-center">
            @forelse ($users as $user)
            <tr>
                <td>{{ $user->userInfo->pwd_id }}</td>
                <!-- <td>{{ $user->userInfo->firstname . ' ' . $user->userInfo->lastname }}</td> -->
                <td>{{ $user->userInfo->name }}</td>
                <td>{{ $user->email }}</td>
                <td>{{ $user->userInfo->contactnumber }}</td>
                <td>{{ $user->userInfo->location }}</td>
                <td>{{ $user->userInfo->disability->disability_name }}</td>
                <td colspan="2">
                    <form id="delete-user-{{ $user->id }}" action="{{ route('user-delete', $user->id) }}" method="POST" style="display:inline;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="deny-btn border-0" onclick="confirmDelete(event, 'delete-user-{{ $user->id }}')">
                            <!-- <i class='bx bx-trash'></i> --> Delete
                        </button>
                    </form>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="7">No PWD users found.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
    <div class="pagination-container">
        <div class="pagination">
            {{ $users->links() }}
        </div>
    </div>
</div>
<script>
    function confirmDelete(event, formId) {
        event.preventDefault();
        Swal.fire({
            title: "Confirmation",
            text: "Do you really want to delete this?",
            icon: "question",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "Confirm"
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById(formId).submit();
            }
        });
    }
</script>
@endsection