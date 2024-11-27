@extends('layout')

@section('page-title', 'Manage Job Postings')
@section('page-content')
<div class="d-flex flex-column agency-prog-container pb-4">
    <div class="row mb-2">
        <div class="text-start header-texts back-link-container border-bottom">
            Job Listings.
        </div>
    </div>
    <div class="manage-outer">
        <div class="mt-2 prog-grid mb-2">
            <div class="add-prog-card d-flex justify-content-center align-items-center ">
                <a href="{{route('add-job')}}" class="">+</a>
            </div>
            @foreach ($jobs as $job)
            <div class="prog-card" data-job-id="{{ $job->id }}" data-lat="{{ $job->latitude }}" data-lng="{{ $job->longitude }}">
                <a href="{{route('jobs-show', $job->id) }}" class="prog-texts">
                    <h3 class="text-cap">{{$job->position}}</h3>
                    <p class="sub-text prog-loc text-cap" id="location-{{ $job->id }}">
                        <i class='bx bx-map sub-text prog-loc'></i>{{$job->location}}
                    </p>
                    <input type="hidden" id="lat" value="{{$job->latitude}}">
                    <input type="hidden" id="lng" value="{{$job->longitude}}">
                    <div class="prog-desc-container">
                        <p class="prog-desc mt-3">
                            {{$job->description}}
                        </p>
                    </div>
                    <div class="d-flex prog-details">
                        <p class="sub-text">
                            <i class='bx bx-calendar sub-text'></i> {{ \Carbon\Carbon::parse($job->end_date)->format('F d, Y') }}
                        </p>
                        <!-- <span class="sub-text period">•</span>  -->

                    </div>
                </a>
            </div>
            @endforeach
        </div>
    </div>

    <div class="pagination-container">
        <div class="pagination">
            {{ $jobs->links() }}
        </div>
    </div>
</div>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        var geocoder = new google.maps.Geocoder();

        document.querySelectorAll('.prog-card').forEach(function(card) {
            var jobId = card.getAttribute('data-job-id');
            var lat = parseFloat(card.getAttribute('data-lat'));
            var lng = parseFloat(card.getAttribute('data-lng'));
            var latlng = {
                lat: lat,
                lng: lng
            };

            geocoder.geocode({
                location: latlng
            }, function(results, status) {
                var locationElement = document.getElementById('location-' + jobId);
                if (status === 'OK') {
                    if (results[0]) {
                        var addressParts = results[0].formatted_address.split(',');
                        // Extract the city and country (assuming the city is at index 1 and the country at index 3)
                        var city = addressParts[1].trim(); // City (e.g., "Cebu City")
                        var country = addressParts[addressParts.length - 1].trim();
                        locationElement.innerHTML = "<i class='bx bx-map sub-text'></i> " + city + ", " + country;
                    } else {
                        locationElement.innerHTML = "<i class='bx bx-map sub-text'></i> No address found";
                    }
                } else {
                    locationElement.innerHTML = "<i class='bx bx-map sub-text'></i> Geocoder failed: " + status;
                }
            });
        });
    });
</script>

@endsection