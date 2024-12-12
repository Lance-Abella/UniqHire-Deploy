@extends('layout')

@section('page-title', 'Manage Program Criteria')
@section('page-content')
<div class="users-container">
    <div class="mt-4 mb-2 border-bottom d-flex justify-content-between pb-2">
        <div class="text-start header-texts back-link-container ">
            Manage Program Criteria.
        </div> 
        <div class="text-end">
            <form action="{{route('prog-criteria-reset')}}" id="reset-form" method="POST">
                @csrf
                <button type="submit" class="deny-btn border-0" onclick="confirmReset(event, 'reset-form')">Reset weights</button>
            </form>
        </div>      
    </div>
    <table class="table table-striped table-hover">
        <thead>
            <tr>
                <td class="table-head">Name</td>
                <td class="table-head">Weight</td>
                <td class="table-head" colspan="2">Actions</td>
            </tr>
        </thead>
        <tbody class="table-group-divider text-center">
            @forelse ($criteria as $criterion)
            <tr>
                <td>{{ $criterion->name }}</td>
                <td>{{ $criterion->weight }}</td>
                <td colspan="2">
                    <a href="{{ route('prog-criteria-edit', $criterion->id) }}" class="submit-btn border-0">Edit</a>
                </td>              
            </tr>
            @empty
            <tr>
                <td colspan="7">No criteria found.</td>
            </tr>
            @endforelse
            <tr>
                <td>Total</td>
                <td>{{$total}}</td>
                <td colspan="2"></td>
            </tr>
        </tbody>
    </table>
</div>
<script>
    function confirmReset(event, formId) {
        event.preventDefault();
        Swal.fire({
            title: "Confirmation",
            text: "Do you really want to reset all weights?",
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