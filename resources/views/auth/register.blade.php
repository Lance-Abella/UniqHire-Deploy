@extends('layout')

@section('page-title', 'Create Account')

@section('auth-content')
<div class="container register-container vh-100">

    <form method="POST" action="{{ route('register-form') }}" enctype="multipart/form-data">
        <div class="row" style="padding-top:3rem;">
            <div class="col">
                <div class="text-start header-texts back-link-container">
                    <a href="{{ route('login-page') }}" class="m-1 back-link"><i class='bx bx-left-arrow-alt'></i></a>
                    Create an Account.
                </div>
            </div>
            <!-- <div class="col-2">
            <div class="text-center">
                <img src="../images/logo.png" alt="UniqHire Logo" style="height: 3.7rem;">
            </div>
        </div> -->
            <div class="col ">
                <div class="row">
                    <div class="col d-flex align-items-center justify-content-end">
                        <label for="registerAs">Register As:</label>
                    </div>
                    <div class="col">
                        <select class="form-select" name="role[]" id="role" aria-label="Small select example" onchange="togglePWDSection()">
                            @foreach ($roles as $role)
                            @if ($role->role_name !== 'Admin')
                            <option value="{{ $role->id }}">{{ $role->role_name }}</option>
                            @endif
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
            <hr class="mb-4">
            <div>
                @csrf
                <div class="row">
                    <div class="col">
                        <div class="form-floating mb-3">
                            <input type="text" class="form-control" id="name" name="name" value="{{ old('name') }}" required placeholder="Name">
                            <label for="name">Name</label>
                            @error('name')
                            <span class="error-msg">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                    <!-- <div class="col" id="lastname-section">
                        <div class="form-floating mb-3">
                            <input type="text" class="form-control" id="floatingInput" name="lastname" value="{{ old('lastname') }}" placeholder="Last Name">
                            <label for="floatingInput">Last Name</label>
                            @error('lastname')
                            <span class="error-msg">{{ $message }}</span>
                            @enderror
                        </div>
                    </div> -->
                </div>
                <div class="row">
                    <div class="col">
                        <div class="form-floating mb-3">
                            <input type="email" class="form-control" id="email" name="email" placeholder="Email Address">
                            <label for="email">Email Address</label>
                            @error('email')
                            <span class="error-msg">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="col">
                            <input type="checkbox" name="generate_email" id="generate_email" class="form-check-input border border-dark">
                            <label for="generate_email" class="form-check-label">Generate Email Address</label>
                        </div>
                    </div>
                    <div class="col">
                        <div class="form-floating mb-3">
                            <input type="text" class="form-control" id="contactnumber" name="contactnumber" value="{{ old('contactnumber') }}" required placeholder="Contact Number" pattern="\d{11}" minlength="11" maxlength="11">
                            <label for="contactnumber">Contact Number</label>
                            @error('contactnumber')
                            <span class="error-msg">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col">
                        <div class="form-floating mb-3">
                            <input type="password" class="form-control" id="floatingPassword" name="password" required placeholder="Password">
                            <label for="floatingPassword">Password</label>
                            @error('password')
                            <span class="error-msg">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                    <div class="col">
                        <div class="form-floating mb-3">
                            <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" required placeholder="Confirm Password">
                            <label for="password_confirmation">Confirm Password</label>
                        </div>
                    </div>
                </div>
                <input type="hidden" id="lat" name="lat" required>
                <input type="hidden" id="long" name="long" required>
                <input id="pac-input" class="controls" type="text" placeholder="Search Box">
                <label for="map">Select Your Location:</label>
                <div id="map" style="height: 400px; width: 100%;"></div>                
                <p id="coordinates"></p>
                <hr>
                <div id="pwd-section">
                    <div class="row">
                        <div class="col">
                            <div class="form-floating mb-3">
                                <input type="number" class="form-control" id="age" name="age" placeholder="Age">
                                <label for="age">Age</label>
                                @error('age')
                                <span class="error-msg">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="col">
                            <div class="form-floating mb-3">
                                <select class="form-select" id="education-level" name="education" aria-label="Floating label select example">
                                    @foreach ($levels as $level)
                                    @if ($level->id != '1')
                                    <option value="{{ $level->id }}">{{ $level->education_name }}</option>
                                    @endif
                                    @endforeach
                                </select>
                                <label for="education-level">Education Level</label>
                            </div>
                        </div>
                        <div class="col">
                            <div class="form-floating mb-3">
                                <select class="form-select" id="disabilities" name="disability" aria-label="Floating label select example">
                                    @foreach ($disabilities as $disability)
                                    @if ($disability->id != '1')
                                    <option value="{{ $disability->id }}">{{ $disability->disability_name }}</option>
                                    @endif
                                    @endforeach

                                </select>
                                <label for="disabilities">Disability</label>
                            </div>
                        </div>
                    </div>
                    <div class="row mb-4">
                        <div class="col">
                            <span>
                                PWD Card
                                <input class="form-control" type="file" id="formFile">
                            </span>
                        </div>
                    </div>
                </div>
                <div id="agency-section" style="display:none;">
                    <div class="row">
                        <div class="col">
                            <div class="form-floating mb-3">
                                <input type="text" class="form-control" id="founder" name="founder" value="{{ old('name') }}" placeholder="Founder">
                                <label for="founder">Founder</label>
                                @error('founder')
                                <span class="error-msg">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="col">
                            <div class="form-floating mb-3">
                                <input type="number" class="form-control" id="year_established" name="year_established" placeholder="Year Established">
                                <label for="year_established">Year Established</label>
                                @error('year_established')
                                <span class="error-msg">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>
                <div class="d-flex justify-content-center align-items-center mb-3">
                    <button type="reset" class="m-2 border-0 bg-transparent deny-btn">
                        Clear
                    </button>
                    <button type="submit" class="m-2 border-0 bg-text submit-btn">
                        Register
                    </button>
                </div>
                <div class="text-center">
                    <hr class="mb-4" style="width: 30rem; margin:0 auto;">
                    <span>
                        Already have an account? <a href="{{ route('login-page') }}" class="link-underline link-underline-opacity-0 highlight-link">Login.</a>
                    </span>
                </div>
            </div>

        </div>
    </form>
</div>

@endsection

<script>
    // document.addEventListener('DOMContentLoaded', function() {
    //     fetchProvinces();

    //     document.getElementById('provinceSelect').addEventListener('change', function() {
    //         var provinceCode = this.value;
    //         fetchCities(provinceCode);
    //     });
    // });



    // function fetchProvinces() {
    //     fetch('https://psgc.cloud/api/provinces')
    //         .then(response => response.json())
    //         .then(data => {
    //             var provinceSelect = document.getElementById('provinceSelect');
    //             data.sort((a, b) => a.name.localeCompare(b.name));
    //             data.forEach(province => {
    //                 var option = document.createElement('option');
    //                 option.value = province.name;
    //                 option.text = province.name;
    //                 provinceSelect.appendChild(option);
    //             });
    //         })
    //         .catch(error => console.error('Error fetching provinces:', error));
    // }

    // function fetchCities(provinceCode) {
    //     fetch(`https://psgc.cloud/api/provinces/${provinceCode}/cities-municipalities`)
    //         .then(response => response.json())
    //         .then(data => {
    //             var citySelect = document.getElementById('citySelect');
    //             citySelect.innerHTML = '<option value="">Select City</option>';
    //             data.sort((a, b) => a.name.localeCompare(b.name));
    //             data.forEach(city => {
    //                 var option = document.createElement('option');
    //                 option.value = city.name;
    //                 option.text = city.name;
    //                 citySelect.appendChild(option);
    //             });
    //         })
    //         .catch(error => console.error('Error fetching cities:', error));
    // }

    function togglePWDSection() {
        var roleSelect = document.getElementById('role');
        var pwdSection = document.getElementById('pwd-section');
        var disabilitySelect = document.getElementById('disabilities');
        var agencySection = document.getElementById('agency-section');
        var educationSelect = document.getElementById('education-level');

        if (roleSelect.value === '2') {
            pwdSection.style.display = 'block';
            agencySection.style.display = 'none';

            removeOption(disabilitySelect, '1');
            removeOption(educationSelect, '1');
        } else {
            pwdSection.style.display = 'none';
            addNotApplicableOption(disabilitySelect);
            addNotApplicableOption(educationSelect);
            disabilitySelect.value = '1';
            educationSelect.value = '1';
        }

        if (roleSelect.value === '3') {
            agencySection.style.display = 'block';
        } else {
            agencySection.style.display = 'none';
        }
    }

    function removeOption(selectElement, value) {
        for (var i = 0; i < selectElement.options.length; i++) {
            if (selectElement.options[i].value === value) {
                selectElement.remove(i);
                break;
            }
        }
    }

    function addNotApplicableOption(selectElement) {
        var optionExists = false;
        for (var i = 0; i < selectElement.options.length; i++) {
            if (selectElement.options[i].value === '1') {
                optionExists = true;
                break;
            }
        }

        if (!optionExists) {
            var notApplicableOption = document.createElement('option');
            notApplicableOption.value = '1';
            notApplicableOption.text = 'Not Applicable';
            selectElement.add(notApplicableOption);
        }
    }

    function initMap() {
    // Default location (e.g., Cebu City, Philippines)
    var initialLocation = { lat: 10.31569920, lng: 123.88543660 };

    // Create the map, centered at the initial location
    var map = new google.maps.Map(document.getElementById('map'), {
        zoom: 8,
        center: initialLocation
    });

    // Add a draggable marker to the map
    var marker = new google.maps.Marker({
        position: initialLocation,
        map: map,
        draggable: true,  
        title: 'Drag me to your location!'
    });

    // Set the hidden input fields to the default location when the map is loaded
    document.getElementById('lat').value = initialLocation.lat;
    document.getElementById('long').value = initialLocation.lng;

    // Update hidden inputs and display coordinates
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
}

// Initialize the map when the window loads
window.onload = initMap;


</script>