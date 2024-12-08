@extends('layout')

@section('page-title', 'Manage Disabilities')
@section('page-content')
<div class="users-container">
    <div class="mt-4 mb-2 border-bottom d-flex justify-content-between pb-2">
        <div class="text-start header-texts back-link-container ">
            Manage Disabilities.
        </div>
        <div class="text-end">
            @include('slugs.createDisability')
        </div>
    </div>

    <table class="table table-striped table-hover">
        <thead>
            <tr>
                <td class="table-head">Disability ID</td>
                <td class="table-head">Name</td>
                <td class="table-head" colspan="2">--</td>
            </tr>
        </thead>
        <tbody class="table-group-divider text-center">
            @forelse ($disabilities as $disability)
            <tr>
                <td>{{ $disability->id }}</td>
                <td>{{ $disability->disability_name }}</td>
                <td colspan="2">
                    <a href="{{ route('disability-edit', $disability->id) }}" class="submit-btn border-0">Edit</a>
                    <form id="delete-form-{{ $disability->id }}" action="{{ route('disability-delete', $disability->id) }}" method="POST" style="display:inline;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="deny-btn border-0" onclick="confirmDelete(event, 'delete-form-{{ $disability->id }}')">
                            <!-- <i class='bx bx-trash'></i> --> Delete
                        </button>
                    </form>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="7">No disabilities found.</td>
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