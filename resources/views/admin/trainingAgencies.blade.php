@extends('layout')

@section('page-title', 'Training Agencies')
@section('page-content')
<div class="users-container">
    <div class="row mt-4 mb-2">
        <div class="text-start header-texts back-link-container border-bottom">
            Training Agencies.
        </div>
    </div>
    <table class="table table-striped table-hover">
        <thead>
            <tr>
                <td class="table-head">TPA Number</td>
                <td class="table-head">Name</td>
                <td class="table-head">Email</td>
                <td class="table-head">Contact Number</td>
                <td class="table-head">Location</td>
                <td class="table-head">Account Status</td>
                <!-- <td class="table-head">Disability</td> -->
                <td class="table-head" colspan="2">Set Status</td>
                <td class="table-head" colspan="2">Actions</td>
            </tr>
        </thead>
        <tbody class="table-group-divider text-center">
            @forelse ($users as $user)
            <tr>
                <td>{{ $user->userInfo->pwd_id }}</td>
                <td>{{ $user->userInfo->name }}</td>
                <td>{{ $user->email }}</td>
                <td>{{ $user->userInfo->contactnumber }}</td>
                <td>{{ $user->userInfo->location }}</td>
                <td>
                    <p class="match-info @if ($user->userInfo->registration_status == 'Pending')
                                pending @elseif ($user->userInfo->registration_status == 'Deactivated')
                                denied
                                @endif">
                        {{ $user->userInfo->registration_status}}
                    </p>
                </td>
                <td colspan="2">
                    @if ($user->userInfo->registration_status == 'Pending')
                    <form id="set-user-status-activated-{{ $user->id }}" action="{{ route('user-set-status', ['id' => $user->id, 'status' => 'Activated']) }}" method="POST" style="display:inline;" onclick="confirmStatus(event, 'set-user-status-activated-{{ $user->id }}')">
                        @csrf
                        @method('PATCH')
                        <button type="submit" class="set-btn border-0">
                            Set to Activate
                        </button>
                    </form>

                    <form id="set-user-status-deactivated-{{ $user->id }}" action="{{ route('user-set-status', ['id' => $user->id, 'status' => 'Deactivated']) }}" method="POST" style="display:inline;" onclick="confirmStatus(event, 'set-user-status-deactivated-{{ $user->id }}')">
                        @csrf
                        @method('PATCH')
                        <button type="submit" class="set-btn border-0">
                            Set to Deactivate
                        </button>
                    </form>
                    @else
                    <form id="toggle-user-status-{{ $user->id }}" action="{{ route('user-toggle-status', $user->id) }}" method="POST" style="display:inline;" onclick="confirmToggle(event, 'toggle-user-status-{{ $user->id }}')">
                        @csrf
                        @method('PATCH')
                        <button type="submit" class="{{ $user->userInfo->registration_status == 'Activated' ? 'deny-btn' : 'activate-btn' }} border-0">
                            {{ $user->userInfo->registration_status == 'Activated' ? 'Deactivate' : 'Activate' }}
                        </button>
                    </form>
                    @endif
                </td>
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
                <td colspan="7">No Training Agency users found.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>
<script>
    function confirmDelete(event, formId) {
        event.preventDefault();
        Swal.fire({
            title: "Confirmation",
            text: "Do you really want to delete this user?",
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

    function confirmToggle(event, formId) {
        event.preventDefault();
        Swal.fire({
            title: "Confirmation",
            text: "Do you want to change the user's account status?",
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

    function confirmStatus(event, formId) {
        event.preventDefault();
        Swal.fire({
            title: "Confirmation",
            text: "Do you really want to set this status?",
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