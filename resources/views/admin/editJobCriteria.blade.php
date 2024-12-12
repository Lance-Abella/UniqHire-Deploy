@extends('layout')

@section('page-title', 'Edit Criteria')
@section('page-content')
<div class="users-container container">
    <div class="text-start header-texts back-link-container border-bottom mb-3">
        <a href="{{ route('job-criteria-list') }}" class="m-1 back-link"><i class='bx bx-left-arrow-alt'></i></a>
        Edit Disability.
    </div>
    <form action="{{ route('job-criteria-update', $criterion->id) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="form-floating mb-3">
            <input type="number" class="form-control" id="floatingInput-{{$criterion->id}}" name="weight" value="{{$criterion->weight}}" required placeholder="Criterion Weight">
            <label for="floatingInput-{{$criterion->id}}">Weight</label>
            @error('name')
            <span class="error-msg">{{ $message }}</span>
            @enderror
        </div>
        <button type="submit" class="submit-btn border-0">Update Weight</button>
    </form>
</div>
@endsection