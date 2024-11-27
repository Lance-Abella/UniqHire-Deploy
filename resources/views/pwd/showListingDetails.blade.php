@extends('layout')

@section('page-title', 'Job Details')

@section('page-content')
<div class="agency-show-prog">
    <div class="back-btn">
        @if (Route::currentRouteName() == 'job-details')
        <a href="{{ route('pwd-list-job') }}" class="m-1 back-link"><i class='bx bx-left-arrow-alt'></i></a>
        @elseif(Route::currentRouteName() == 'show-details' )
        <a href="{{ route('trainings') }}" class="m-1 back-link"><i class='bx bx-left-arrow-alt'></i></a>
        @endif
    </div>
    <div class="prog-details">
        <div class="header d-flex">
            <div class="mb-3 titles">
                <h3 class="text-cap">{{ $listing->position }}</h3>
                <p class="sub-text text-cap">{{ $listing->employer->userInfo->name }}</p>
                <p class="sub-text prog-loc text-cap" id="location"><i class='bx bx-map sub-text'></i>{{ $listing->location }}</p>
                <input type="hidden" id="lat" value="{{ $listing->latitude }}">
                <input type="hidden" id="lng" value="{{ $listing->longitude }}">
            </div>
            <div class="prog-btn">
            </div>
        </div>
        <div class="mb-5">
            <div class="col">
                {{ $listing->description }}
            </div>
        </div>
        <ul class="nav nav-underline" role="tablist">
            <li class="nav-item">
                <a class="nav-link active" data-bs-toggle="tab" href="#requirements" role="tab">Requirements</a>
            </li>
            @if ($listing->crowdfund)
            <li class="nav-item">
                <a class="nav-link" data-bs-toggle="tab" href="#sponsors" role="tab">Sponsors</a>
            </li>
            @endif
        </ul>
        <div class="tab-content">
            <div class="tab-pane active" id="requirements" role="tabpanel">
                <div class="requirements">
                    <div class="d-flex justify-content-start mb-5">
                        <div class="more-info">
                            <h5>Schedule</h5>
                            <p>
                                @foreach(explode(',', $listing->schedule) as $date)
                                {{ \Carbon\Carbon::parse(trim($date))->format('F d, Y') }}
                                @if(!$loop->last)
                            <p></p>
                            @endif
                            @endforeach
                            </p>
                        </div>
                    </div>
                    <div class="d-flex justify-content-start mb-5">
                        <div class="more-info">
                            <h5>Age</h5>
                            <p class="match-info">{{ $listing->start_age . ' - ' . $listing->end_age . ' Years Old' }}</p>
                        </div>
                        <div class="more-info">
                            <h5>Skills Acquired</h5>
                            <ul>
                                @foreach ($listing->skill as $skill)
                                <li class="match-info mb-2">{{ $skill->title }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                    <div class="d-flex justify-content-start more-info">
                        <div class="more-info">
                            <h5>We Accept</h5>
                            <ul>
                                @foreach ($listing->disability as $disability)
                                <li class="match-info mb-2">{{ $disability->disability_name }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            <div class="tab-pane" id="competencies" role="tabpanel">
            </div>

            <div class="tab-pane enrollees" id="enrollees" role="tabpanel">
                <table class="table table-striped table-hover">
                    <tbody>
                        @forelse ($enrollees as $enrollee)
                        <tr>
                            <td class="name">
                                <a href="{{ route('show-profile', $enrollee->application->user->id) }}">
                                    {{ $enrollee->application->user->userInfo->name }}
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="3" class="text-center">No enrollees yet.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if ($listing->crowdfund)
            <div class="tab-pane" id="sponsors" role="tabpanel">
                <div class="crowdfund-progress mb-3">
                    <p class="sub-text">
                        Goal Amount: &nbsp;&nbsp;<span>{{number_format($listing->crowdfund->goal, 0, '.', ',') . ' PHP'}}</span>
                    </p>
                    <p class="sub-text">
                        Crowdfunding Progress:
                    </p>
                    <div class="progress">
                        <div class="progress-bar" role="progressbar" aria-valuenow="{{ $listing->crowdfund->progress }}" aria-valuemin="0" aria-valuemax="100">{{ $listing->crowdfund->progress }}%</div>
                    </div>
                </div>

                <h5>Sponsors</h5>
                <span class=""></span>
            </div>
            @endif
            <div class="tab-pane" id="reviews" role="tabpanel">
                <div class="border reviews">
                    <div class="header border-bottom d-flex justify-content-between align-items-center">
                        <div class="outer">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function confirmApplication(event, formId) {
            event.preventDefault();
            Swal.fire({
                title: "Confirmation",
                text: "Do you really want to apply for this training program?",
                icon: "question",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "Confirm"
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById(formId).submit();
                }
            });
        }

        function initMap() {
            var lat = parseFloat(document.getElementById('lat').value);
            var lng = parseFloat(document.getElementById('lng').value);
            var latlng = {
                lat: lat,
                lng: lng
            };
            var geocoder = new google.maps.Geocoder();

            // Reverse geocode to get the address
            geocoder.geocode({
                location: latlng
            }, function(results, status) {
                var locationElement = document.getElementById('location');
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
        }

        // Initialize the map and geocoding
        window.onload = initMap;
    </script>

    @endsection