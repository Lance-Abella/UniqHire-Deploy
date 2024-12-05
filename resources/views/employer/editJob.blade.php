@extends('layout')
@section('page-title', 'Edit Job Listing')
@section('page-content')
<form action="{{ route('jobs-edit', $listing->id) }}" method="POST" class="container edit-form">
    @csrf
    @method('PUT')
    <div class="row mt-2 mb-2 border-bottom">
        <div class="text-start header-texts back-link-container">
            <a href="{{ route('jobs-show', $listing->id) }}" class="m-1 back-link"><i class='bx bx-left-arrow-alt'></i></a>
            Edit Job Details.
        </div>
    </div>
    <div class="row">
        <div class="col">
            <div class="form-floating mb-3">
                <input type="text" class="form-control" id="floatingInput" name="position" value="{{ $listing->position }}" required placeholder="Title">
                <label for="floatingInput">Position</label>
                @error('position')
                <span class="error-msg">{{ $message }}</span>
                @enderror
            </div>
        </div>
    </div>
    <input type="hidden" id="lat" name="lat" value="{{ $listing->latitude }}">
    <input type="hidden" id="long" name="long" value="{{ $listing->longitude }}">
    <input type="hidden" id="loc" name="loc" required>
    <input id="pac-input" class="controls" type="text" placeholder="Search Box">
    <label for="map">Select Your Location:</label>
    <div id="map" class="map"></div>
    <p id="coordinates"></p>
    <div class="row">
        <div class="col">
            <div class="form-floating mb-3">
                <textarea class="form-control" placeholder="Description" id="floatingTextarea2" name="description" style="height: 200px">{{ $listing->description }}</textarea>
                <label for="floatingTextarea2">Description</label>
                @error('description')
                <span class="error-msg">{{ $message }}</span>
                @enderror
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col">
            <div class="form-floating mb-3">
                <input type="text" class="form-control" id="salary" name="salary" value="{{ $listing->salary }}" required placeholder="Salary" oninput="formatNumber(this)">
                <label for="salary">Salary</label>
                @error('salary')
                <span class="error-msg">{{ $message }}</span>
                @enderror
            </div>
        </div>
        <div class="col">
            <div class="form-floating mb-3">
                <input type="date" class="form-control date" name="end_date" value="{{ $listing->end_date }}" required placeholder="Choose Date">
                <label for="floatingInput">End Date (Hiring until)</label>
            </div>
        </div>
    </div>
    <div class="row mb-3">
        <div class="col">
            <div class="form-floating mb-3">
                <select class="form-select" id="floatingSelect" name="setup" aria-label="Floating label select example">
                    @foreach ($setups as $setup)
                    <option value="{{ $setup->id }}" @if($setup->id == $listing->worksetup_id) selected @endif>{{ $setup->name }}</option>
                    @endforeach

                </select>
                <label for="floatingSelect">Work Setup</label>
            </div>
        </div>
        <div class="col">
            <div class="form-floating mb-3">
                <select class="form-select" id="floatingSelect" name="type" aria-label="Floating label select example">
                    @foreach ($types as $type)
                    <option value="{{ $type->id }}" @if( $type->id == $listing->worktype_id) selected @endif>{{ $type->name }}</option>
                    @endforeach

                </select>
                <label for="floatingSelect">Work Type</label>
            </div>
        </div>
    </div>
    <div class="row border-bottom mb-1">
        <div class="col">
            <h5>Select Disabilities</h5>
            <div class="req-container">
                @foreach ($disabilities as $disability)
                @if ($disability->disability_name != 'Not Applicable')
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" value="{{ $disability->id }}" id="flexCheckChecked{{ $loop->index }}" name="disabilities[]"
                        @if (isset($listing->disability) && $listing->disability->contains('disability_name', $disability->disability_name))
                    checked
                    @endif>
                    <label class="form-check-label" for="flexCheckChecked{{$loop->index}}">
                        {{$disability->disability_name}}
                    </label>
                </div>
                @endif
                @endforeach
            </div>
        </div>
        <div class="col">
            <h5>Select Skills</h5>
            <div class="req-container">
                @foreach ($skills as $skill)
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" value="{{$skill->id}}" id="skill{{$loop->index}}" name="skills[]"
                        @if (isset($listing->skill) && $listing->skill->contains('title', $skill->title))
                    checked
                    @endif>
                    <label class="form-check-label" for="skill{{$loop->index}}">
                        {{$skill->title}}
                    </label>
                </div>
                @endforeach
            </div>
        </div>
    </div>
    <div class="d-flex justify-content-evenly mt-3 prog-btn">
        <a href="{{route('jobs-show', $listing->id)}}" class="deny-btn border-0">Cancel</a>
        <button type="submit" class="submit-btn border-0">Update</button>
    </div>
</form>
@endsection

@push('map-scripts')
<script async defer src="https://maps.googleapis.com/maps/api/js?key=AIzaSyA4IdhyGOY2rDNFymY1kGR3qaS6K4RlWEY&libraries=places&loading=async&callback=editInitMap"></script>
<script src="{{ asset('js/editInitMap.js') }}"></script>
@endpush

<script>
    function formatNumber(input) {
        let value = input.value.replace(/,/g, '');
        if (!isNaN(value) && value !== '') {
            input.value = Number(value).toLocaleString();
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        var salaryInput = document.getElementById('salary');
        var participantsInput = document.getElementById('participants');
        if (salaryInput) {
            formatNumber(salaryInput);
        }
    });
</script>