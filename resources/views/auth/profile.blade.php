@extends('layout')

@section('page-title', 'Profile')
@section('page-content')
<div class="profile-container container">
    <div class="profile-info mb-4">
        <div class="profile-pic" @if (!empty($user->userInfo->profile_path)) style=" background-image: url({{ asset($user->userInfo->profile_path) }}); background-repeat: no-repeat; background-size: cover; " @endif>
            @if (!empty($user->userInfo->profile_path))
            <form action="{{route('remove-pic')}}" method="POST" class="d-flex justify-content-center">
                @csrf
                <button type="submit" class="deny-btn border-0"><i class='bx bx-trash'></i></button>
            </form>
            @else
            <span>{{ strtoupper(substr($user->userInfo->name, 0, 1)) }}</span>
            @endif

        </div>
        <div class="d-flex justify-content-between header">
            <div class="details row">
                <div class="col">
                    <p class="text-cap profile-name">{{ $user->userInfo->name }}</p>
                    <p class="text-cap" id="location"><i class='bx bx-map sub-text'></i>Loading address...</p>
                    <input type="hidden" id="lat" value="{{ $latitude }}">
                    <input type="hidden" id="lng" value="{{ $longitude }}">
                </div>
                @if($user->hasRole('PWD'))
                <div class="col">
                    <p class="text-cap age"><strong>Age:</strong>
                        @if ($user->userInfo->age != 0)
                        {{ $user->userInfo->age }} years old
                        @else
                        <span class="about sub-text">No data yet</span>
                        @endif

                    </p>
                    <p class="text-cap"> <strong>Disability:</strong>&nbsp;&nbsp;&nbsp;<span class="match-info">{{ $user->userInfo->disability->disability_name }}</span></p>
                </div>
                @elseif($user->hasRole('Training Agency'))
                <div class="col">
                    <p class="text-cap age"><strong>Founder:</strong>
                        @if ($user->userInfo->founder != null)
                        {{ $user->userInfo->founder }}
                        @else
                        <span class="about sub-text">No data yet</span>
                        @endif
                    </p>
                    <p class="text-cap age"> <strong>Year Established:</strong>
                        @if ($user->userInfo->year_established != 0)
                        {{ $user->userInfo->year_established }}
                        @else
                        <span class="about sub-text">No data yet</span>
                        @endif
                    </p>
                </div>
                @endif
                <div></div>
            </div>
            @include('slugs.editProfile')
        </div>
    </div>
    <div class="more-details d-flex">
        <div class="contact border">
            <h4 class="mb-4">Contact Information</h4>
            <div class="contact-container">
                <div class="contact-item ">
                    <span class="d-flex align-items-center sub-text"><i class='bx bx-envelope side-icon'></i> Email</span>
                    <p><a href="">{{ $user->email }}</a></p>
                </div>
                <div class="contact-item">
                    <span class="d-flex align-items-center sub-text"><i class='bx bx-envelope side-icon'></i> Contact no</span>
                    <p><a href="">{{ $user->userInfo->contactnumber }}</a></p>
                </div>
                <div class="contact-item">
                    <span class="d-flex align-items-center sub-text"><i class='bx bxl-facebook  side-icon'></i> Facebook</span>
                    <p><a href="">{{ 'facebook.com/' . strtolower(substr($user->userInfo->name, 0, 5)) }}</a></p>
                </div>
                <div class="contact-item">
                    <span class="d-flex align-items-center sub-text"><i class='bx bxl-instagram side-icon'></i> Instagram</span>
                    <p><a href="">{{ 'instagram.com/' . strtolower(substr($user->userInfo->name, 0, 5)) }}</a></p>
                </div>
                <div class="contact-item ">
                    <span class="d-flex align-items-center sub-text"><i class='bx bx-globe side-icon'></i> Website</span>
                    <p><a href="">{{ 'website.com/' . strtolower(substr($user->userInfo->name, 0, 5)) }}</a></p>
                </div>
            </div>
        </div>
        <div class="bio">
            <div class="bio-item">
                <h4 class="mb-3">About</h4>
                @if ($user->userInfo->about != null)
                <p>{!! nl2br(e($user->userInfo->about)) !!}</p>
                @else
                <p class="about sub-text">No data yet</p>
                @endif
            </div>
            @if ($user->hasRole('PWD'))
            <div class="bio-item exp">
                <div>
                    @include('slugs.editSkills')
                </div>
                <div>
                    <h4 class="mb-3">Education Level</h4>
                    <p class="match-info">{{$user->userInfo->education->education_name}}</p>
                </div>
            </div>
            <div class="bio-item exp">
                <div>
                    <h4 class="mb-3">Certifications</h4>
                    @forelse($certifications as $certification)
                    <p>
                        <a href="{{ route('download-certificate', $certification->id) }}" class="certify">
                            Certified in {{$certification->program->title}} <i class='bx bx-download'></i>
                        </a>
                    </p>
                    @empty
                    <p class="about sub-text">No certifications yet. <a href="{{ route('pwd-list-program') }}">Enroll first!</a></p>
                    @endforelse
                </div>
                <div>
                    @include('slugs.editExperiences')
                </div>
                @elseif($user->hasRole('Training Agency'))
                <div class="bio-item exp">
                    <div>
                        <h4 class="mb-3">Awards & Recognitions</h4>
                        @if ($user->userInfo->awards != null)
                        <p>{!! nl2br(e($user->userInfo->awards)) !!}</p>
                        @else
                        <p class="about sub-text">No data yet</p>
                        @endif
                    </div>
                    <div>
                        <h4 class="mb-3">Affiliations</h4>
                        @if ($user->userInfo->affiliations != null)
                        <p>{!! nl2br(e($user->userInfo->affiliations)) !!}</p>
                        @else
                        <p class="about sub-text">No data yet</p>
                        @endif
                    </div>
                </div>
                @endif
            </div>
        </div>

    </div>
</div>
@endsection

<script>
    document.addEventListener('DOMContentLoaded', function() {
        fetchProvinces().then(() => {
            var selectedProvince = "{{ $user->userInfo->state }}";
            var provinceSelect = document.getElementById('provinceSelect');
            if (selectedProvince) {
                provinceSelect.value = selectedProvince;
                fetchCities(selectedProvince).then(() => {
                    var selectedCity = "{{ $user->userInfo->city }}";
                    var citySelect = document.getElementById('citySelect');
                    if (selectedCity) {
                        citySelect.value = selectedCity;
                    }
                });
            }
        });

        document.getElementById('provinceSelect').addEventListener('change', function() {
            var provinceCode = this.value;
            fetchCities(provinceCode);
        });

        // Set max year for the year established input
        var yearEstablishedInput = document.getElementById('year-established');
        var currentYear = new Date().getFullYear();
        yearEstablishedInput.max = currentYear;
    });

    function fetchProvinces() {
        return fetch('https://psgc.cloud/api/provinces')
            .then(response => response.json())
            .then(data => {
                var provinceSelect = document.getElementById('provinceSelect');
                provinceSelect.innerHTML = '<option value="">Select Province</option>';
                data.sort((a, b) => a.name.localeCompare(b.name));
                data.forEach(province => {
                    var option = document.createElement('option');
                    option.value = province.name; // Ensure this matches your database value
                    option.text = province.name;
                    provinceSelect.appendChild(option);
                });
            })
            .catch(error => console.error('Error fetching provinces:', error));
    }

    function fetchCities(provinceCode) {
        return fetch(`https://psgc.cloud/api/provinces/${provinceCode}/cities-municipalities`)
            .then(response => response.json())
            .then(data => {
                var citySelect = document.getElementById('citySelect');
                citySelect.innerHTML = '<option value="">Select City</option>';
                data.sort((a, b) => a.name.localeCompare(b.name));
                data.forEach(city => {
                    var option = document.createElement('option');
                    option.value = city.name.trim();
                    option.text = city.name.trim();
                    citySelect.appendChild(option);
                });

                var userCity = "{{ $user->userInfo->city }}".trim().toLowerCase();

                Array.from(citySelect.options).forEach(option => {
                    if (option.value.trim().toLowerCase() === userCity) {
                        option.selected = true;
                    }
                });
            })
            .catch(error => console.error('Error fetching cities:', error));
    }

    function clearFileInput(id) {
        var input = document.getElementById(id);
        input.value = '';
    }

    function initMap() {
        var lat = parseFloat(document.getElementById('lat').value);
        var lng = parseFloat(document.getElementById('long').value);
        var latlng = { lat: lat, lng: lng };

        // Create the map, centered at the initial location
        var map = new google.maps.Map(document.getElementById('map'), {
            zoom: 8,
            center: latlng
        });

        // Add a draggable marker to the map
        var marker = new google.maps.Marker({
            position: latlng,
            map: map,
            draggable: true,  
            title: 'Drag me to your location!'
        });

        // // Set the hidden input fields to the default location when the map is loaded
        // document.getElementById('lat').value = lat;
        // document.getElementById('long').value = lng;

        function updateCoordinates(markerPosition) {
            var lat = markerPosition.lat();
            var lng = markerPosition.lng();
            document.getElementById('lat').value = lat;
            document.getElementById('long').value = lng;
            document.getElementById('coordinates').innerText = 'Latitude: ' + lat + ', Longitude: ' + lng;
        }

         // Place the marker where the user clicks on the map
        map.addListener('click', function(event) {
            var clickedLocation = event.latLng;
            marker.setPosition(clickedLocation); 
            marker.setMap(map); 
            updateCoordinates(clickedLocation); 
        });

        // Automatically update the coordinates when the marker is dragged
        marker.addListener('dragend', function() {
            updateCoordinates(marker.getPosition());
        });

        // Create the search box and link it to the UI element
        var input = document.getElementById('pac-input');
        var searchBox = new google.maps.places.SearchBox(input);
        map.controls[google.maps.ControlPosition.TOP_LEFT].push(input);

        // Bias the SearchBox results towards current map's viewport
        map.addListener('bounds_changed', function() {
            searchBox.setBounds(map.getBounds());
        });

        // Listen for the event fired when the user selects a prediction and retrieves more details for that place
        searchBox.addListener('places_changed', function() {
            var places = searchBox.getPlaces();

            if (places.length == 0) {
                return;
            }

            // Clear out the old marker
            marker.setMap(null);

            // For each place, get the icon, name, and location
            var bounds = new google.maps.LatLngBounds();
            places.forEach(function(place) {
                if (!place.geometry || !place.geometry.location) {
                    console.log("Returned place contains no geometry");
                    return;
                }

                // Create a new marker for the selected place
                marker = new google.maps.Marker({
                    position: place.geometry.location,
                    map: map,
                    draggable: true
                });

                // Automatically update coordinates when the new marker is dragged
                marker.addListener('dragend', function() {
                    updateCoordinates(marker.getPosition());
                });

                // Immediately update the coordinates for the selected place
                updateCoordinates(marker.getPosition());

                if (place.geometry.viewport) {
                    // Only geocodes have viewport
                    bounds.union(place.geometry.viewport);
                } else {
                    bounds.extend(place.geometry.location);
                }
            });
            map.fitBounds(bounds);
        });

        var geocoder = new google.maps.Geocoder();

        // Reverse geocode to get the address
        geocoder.geocode({ location: latlng }, function(results, status) {
            var locationElement = document.getElementById('location');
            if (status === 'OK') {
                if (results[0]) {
                    locationElement.innerHTML = "<i class='bx bx-map sub-text'></i> " + results[0].formatted_address;
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