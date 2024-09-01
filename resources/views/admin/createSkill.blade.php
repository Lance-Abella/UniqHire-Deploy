@extends('layout')

@section('page-title', 'Add Skill')
@section('page-content')
<div class="container">
    <h1>Add Skill</h1>
    <form action="{{ route('skills-store') }}" method="POST">
        @csrf
        <div class="form-group">
            <label for="title">Skill Name</label>
            <input type="text" name="title" id="title" class="form-control" required>
        </div>
        <button type="submit" class="btn btn-primary mt-3">Add Skill</button>
    </form>
</div>
@endsection