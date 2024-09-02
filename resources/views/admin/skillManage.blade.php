@extends('layout')

@section('page-title', 'Manage Skills')
@section('page-content')
<div class="users-container">
    <div class="mt-4 mb-2 border-bottom d-flex justify-content-between pb-2">
        <div class="text-start header-texts back-link-container ">
            Manage Skills.
        </div>
        <div class="text-end">
            @include('slugs.createSkill')
        </div>
    </div>

    <table class="table table-striped table-hover">
        <thead>
            <tr>
                <td class="table-head">Skill ID</td>
                <td class="table-head">Name</td>
                <td class="table-head" colspan="2">--</td>
            </tr>
        </thead>
        <tbody class="table-group-divider text-center">
            @forelse ($skills as $skill)
            <tr>
                <td>{{ $skill->id }}</td>
                <td>{{ $skill->title }}</td>
                <td colspan="2">
                    <a href="{{ route('skills-edit', $skill->id) }}" class="btn submit-btn">Edit</a>
                    <form action="{{ route('skills-destroy', $skill->id) }}" method="POST" style="display:inline;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="deny-btn border-0">
                            <i class='bx bx-trash'></i>
                        </button>
                    </form>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="7">No skills found.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

@section('scripts')
<script>
    function confirmDelete() {
        return confirm('Are you sure you want to delete this skill?');
    }
</script>
@endsection
@endsection