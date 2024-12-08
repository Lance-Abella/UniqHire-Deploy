@extends('layout')

@section('page-title', 'Edit Disability')
@section('page-content')
<div class="users-container container">
    <div class="text-start header-texts back-link-container border-bottom mb-3">
        <a href="{{ route('disability-list') }}" class="m-1 back-link"><i class='bx bx-left-arrow-alt'></i></a>
        Edit Disability.
    </div>
    <form action="{{ route('disability-update', $disability->id) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="form-floating mb-3">
            <input type="text" class="form-control" id="floatingInput-{{$disability->id}}" name="name" value="{{$disability->disability_name}}" required placeholder="Disability Name">
            <label for="floatingInput-{{$disability->id}}">Disability Name</label>
            @error('name')
            <span class="error-msg">{{ $message }}</span>
            @enderror
        </div>
        <button type="submit" class="submit-btn border-0">Update Disability</button>
    </form>
</div>
@endsection