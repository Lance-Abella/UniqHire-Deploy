@extends('layout')

@section('page-title', 'Manage Skills')
@section('page-content')
<div class="users-container">
    <div class="mt-4 mb-2 border-bottom d-flex justify-content-between pb-2">
        <div class="text-start header-texts back-link-container ">
            Manage Social Media Platforms.
        </div>
        <div class="text-end">
            @include('slugs.createSocial')
        </div>
    </div>

    <table class="table table-striped table-hover">
        <thead>
            <tr>
                <!-- <td class="table-head">Social ID</td> -->
                <td class="table-head">Name</td>
                <td class="table-head" colspan="2">Actions</td>
            </tr>
        </thead>
        <tbody class="table-group-divider text-center">
            @forelse ($socials as $social)
            <tr>
                <!-- <td>{{ $social->id }}</td> -->
                <td>{{ $social->name }}</td>
                <td colspan="2">
                    <a href="{{ route('social-edit', $social->id) }}" class="submit-btn border-0">Edit</a>
                    <form id="delete-form-{{ $social->id }}" action="{{ route('social-delete', $social->id) }}" method="POST" style="display:inline;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="deny-btn border-0" onclick="confirmDelete(event, 'delete-form-{{ $social->id }}')">
                            Delete
                        </button>
                    </form>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="7">No social media found.</td>
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
            text: "Do you really want to delete this skill?",
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