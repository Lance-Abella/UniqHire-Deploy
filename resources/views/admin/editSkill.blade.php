@extends('layout')

@section('page-title', 'Edit Skill')
@section('page-content')
<div class="container">
    <h1>Edit Skill</h1>
    <form action="{{ route('skills-update', $skill->id) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="form-group">
            <label for="title">Skill Name</label>
            <input type="text" name="title" id="title" class="form-control" value="{{ $skill->title }}" required>
        </div>
        <button type="submit" class="btn btn-primary mt-3">Update Skill</button>
    </form>
</div>
@endsection