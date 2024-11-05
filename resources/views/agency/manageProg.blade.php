@extends('layout')

@section('page-title', 'Manage Training Programs')
@section('page-content')
<div class="d-flex flex-column agency-prog-container pb-4">
    <div class="row mb-2">
        <div class="text-start header-texts back-link-container border-bottom">
            Training Programs.
        </div>
    </div>
    <div class="mt-2 prog-grid">
        <div class="add-prog-card d-flex justify-content-center align-items-center ">
            <a href="{{ route('programs-add') }}" class="">+</a>
        </div>
        @foreach ($programs as $program)
        <div class="prog-card" data-program-id="{{ $program->id }}" data-lat="{{ $program->latitude }}" data-lng="{{ $program->longitude }}">
            <a href="{{ route('programs-show', $program->id) }}" class="prog-texts">
                <h3 class="text-cap">{{ $program->title }}</h3>
                <p class="sub-text prog-loc text-cap" id="location-{{ $program->id }}">
                    <i class='bx bx-map sub-text prog-loc'></i>{{ $program->location }}
                </p>
                <input type="hidden" id="lat" value="{{ $program->latitude }}">
                <input type="hidden" id="lng" value="{{ $program->longitude }}">
                <div class="prog-desc-container">
                    <p class="prog-desc mt-3">
                        {{ $program->description }}
                    </p>
                </div>
                @if ($program->crowdfund)
                <div class="crowdfund-progress mb-3">

                    <p class="sub-text">
                        Goal Amount: &nbsp;&nbsp;<span>{{number_format($program->crowdfund->goal, 0, '.', ',') . ' PHP'}}</span>
                    </p>
                    <p class="sub-text">
                        Crowdfunding Progress:
                    </p>
                    <div class="progress" role="progressbar" aria-label="Animated striped example" aria-valuenow="{{ $program->crowdfund->progress }}" aria-valuemin="0" aria-valuemax="100">
                        <div class="progress-bar progress-bar-striped progress-bar-animated" @if ($program->crowdfund->progress > 0)
                            style="width: {{$program->crowdfund->progress}}%;"
                            else
                            style="width: 10%;"
                            @endif >{{ $program->crowdfund->progress }}%</div>
                    </div>
                </div>
                @endif
                <div class="d-flex prog-details">
                    <p class="sub-text">
                        <i class='bx bx-group sub-text'></i> {{ number_format($program->slots) . '/' . number_format($program->participants) }} Remaining
                    </p>
                    <!-- <span class="sub-text period">•</span>  -->

                </div>
            </a>
        </div>
        @endforeach
    </div>
</div>
<script>
    // document.addEventListener('DOMContentLoaded', function() {
    //     var geocoder = new google.maps.Geocoder();

    //     document.querySelectorAll('.prog-card').forEach(function(card) {
    //         var programId = card.getAttribute('data-program-id');
    //         var lat = parseFloat(card.getAttribute('data-lat'));
    //         var lng = parseFloat(card.getAttribute('data-lng'));
    //         var latlng = {
    //             lat: lat,
    //             lng: lng
    //         };

    //         geocoder.geocode({
    //             location: latlng
    //         }, function(results, status) {
    //             var locationElement = document.getElementById('location-' + programId);
    //             if (status === 'OK') {
    //                 if (results[0]) {
    //                     locationElement.innerHTML = "<i class='bx bx-map sub-text'></i> " + results[0].formatted_address;
    //                 } else {
    //                     locationElement.innerHTML = "<i class='bx bx-map sub-text'></i> No address found";
    //                 }
    //             } else {
    //                 locationElement.innerHTML = "<i class='bx bx-map sub-text'></i> Geocoder failed: " + status;
    //             }
    //         });
    //     });
    // });
</script>

@endsection