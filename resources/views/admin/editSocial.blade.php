@extends('layout')

@section('page-title', 'Edit Skill')
@section('page-content')
<div class="users-container container">
    <div class="text-start header-texts back-link-container border-bottom mb-3">
        <a href="{{ route('social-list') }}" class="m-1 back-link"><i class='bx bx-left-arrow-alt'></i></a>
        Edit Social Media Platform.
    </div>
    <form action="{{ route('social-update', $social->id) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="form-floating mb-3">
            <input type="text" class="form-control" id="floatingInput-{{$social->id}}" name="name" value="{{$social->name}}" required placeholder="Social Media Platform">
            <label for="floatingInput-{{$social->id}}">Social Media Platform</label>
            @error('name')
            <span class="error-msg">{{ $message }}</span>
            @enderror
        </div>
        <button type="submit" class="submit-btn border-0">Update Social</button>
    </form>
</div>
@endsection