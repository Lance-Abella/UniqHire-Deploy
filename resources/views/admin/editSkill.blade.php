@extends('layout')

@section('page-title', 'Edit Skill')
@section('page-content')
<div class="users-container container">
    <div class="text-start header-texts back-link-container border-bottom mb-3">
        <a href="{{ route('skill-list') }}" class="m-1 back-link"><i class='bx bx-left-arrow-alt'></i></a>
        Edit Skill.
    </div>
    <form action="{{ route('skill-update', $skill->id) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="form-floating mb-3">
            <input type="text" class="form-control" id="floatingInput-{{$skill->id}}" name="title" value="{{$skill->title}}" required placeholder="Skill Title">
            <label for="floatingInput-{{$skill->id}}">Skill Title</label>
            @error('title')
            <span class="error-msg">{{ $message }}</span>
            @enderror
        </div>
        <button type="submit" class="submit-btn border-0">Update Skill</button>
    </form>
</div>
@endsection