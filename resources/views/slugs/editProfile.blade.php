<form action="{{ route('edit-profile') }}" method="POST" enctype="multipart/form-data">
    @csrf
    @method('PUT')
    <div>
        <button type="button" class="submit-btn border-0" style="width:8rem;" data-bs-toggle="modal" data-bs-target="#staticBackdrop">Edit Profile</button>
        <div class="modal fade" id="staticBackdrop" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-lg">

                <div class="modal-content">
                    <div class="modal-header">
                        <h1 class="modal-title fs-5" id="staticBackdropLabel">Edit Profile</h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="text-danger">
                            @if (!empty($user->userInfo->profile_path))
                            Current file: {{ basename($user->userInfo->profile_path) }}
                            @endif
                        </div>
                        <div class="input-group mb-3">
                            <input type="file" name="profile_picture" class="form-control" id="choose-file" aria-describedby="inputGroupFileAddon04" aria-label="Upload">
                            <button class="btn btn-outline-secondary" type="button" id="inputGroupFileAddon04" onclick="clearFileInput('choose-file')">Remove</button>
                        </div>

                        <div class="form-floating mb-3">
                            <input type="text" class="form-control" id="floatingInput" name="name" placeholder="Name" value="{{ $user->userInfo->name }}">
                            <label for="floatingInput">Name</label>
                            @error('name')
                            <span class="error-msg">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="row">
                            <div class="col">
                                <div class="form-floating mb-3">
                                    <input type="email" class="form-control" id="floatingInput" name="email" placeholder="Email" value="{{ $user->email }}" disabled>
                                    <label for="floatingInput">Email Address</label>
                                    @error('email')
                                    <span class="error-msg">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col">
                                <div class="form-floating mb-3">
                                    <input type="text" class="form-control" id="floatingInput" name="contactnumber" placeholder="Contact Number" value="{{ $user->userInfo->contactnumber }}">
                                    <label for="floatingInput">Contact Number</label>
                                    @error('contactnumber')
                                    <span class="error-msg">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            @if($user->hasRole('PWD'))
                            <div class="col">
                                <div class="form-floating mb-3">
                                    <input type="number" class="form-control" id="floatingInput" name="age" placeholder="Age" value="{{ $user->userInfo->age}}">
                                    <label for="floatingInput">Age</label>
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
                                        <option value="{{ $level->id }}" @if ($user->userInfo->educational_id == $level->id ) selected @endif>{{ $level->education_name }}</option>
                                        @endif
                                        @endforeach
                                    </select>
                                    <label for="education-level">Education Level</label>
                                </div>
                            </div>
                            @elseif ($user->hasRole('Training Agency') || $user->hasRole('Sponsor') || $user->hasRole('Employer'))
                            <div class="col">
                                <div class="form-floating mb-3">
                                    <input type="text" class="form-control" id="floatingInput" name="founder" value="{{ $user->userInfo->founder }}" placeholder="Founder">
                                    <label for="floatingInput">Founder</label>
                                    @error('founder')
                                    <span class="error-msg">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col">
                                <div class="form-floating mb-3">
                                    <input type="number" class="form-control" id="year-established" name="year_established" value="{{ $user->userInfo->year_established }}" min="1000" max="">
                                    <label for="year-established">Year Established</label>
                                    @error('year-established')
                                    <span class="error-msg">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            @endif
                        </div>
                        <input type="hidden" id="lat" name="lat" value="{{ $latitude }}">
                        <input type="hidden" id="long" name="long" value="{{ $longitude }}">
                        <input type="hidden" id="loc" name="loc" required>
                        <input id="pac-input" class="controls" type="text" placeholder="Search Box">
                        <label for="map">Select Your Location:</label>
                        <div id="map" class="map"></div>
                        <p id="coordinates"></p>
                        @if($user->hasRole('PWD'))
                        <div class="form-floating mb-3">
                            <select class="form-select" id="floatingSelect" name="disability" aria-label="Floating label select example">
                                @foreach ($disabilities as $disability)
                                @if ($disability->disability_name != 'Not Applicable')
                                <option value="{{ $disability->id }}" @if ($user->userInfo->disability_id == $disability->id ) selected @endif >{{ $disability->disability_name }}</option>
                                @endif
                                @endforeach

                            </select>
                            <label for="floatingSelect">Disability</label>
                        </div>
                        @endif
                        <hr>
                        <div class="form-floating mb-3">
                            <textarea class="form-control" placeholder="About" id="floatingTextarea2" name="about" style="height: 200px">{{ $user->userInfo->about }}</textarea>
                            <label for="floatingTextarea2">About</label>
                            @error('about')
                            <span class="error-msg">{{ $message }}</span>
                            @enderror
                        </div>
                        @if($user->hasRole('Training Agency'))
                        <hr>
                        <div class="form-floating mb-3">
                            <textarea class="form-control" placeholder="About" id="floatingTextarea2" name="awards" style="height: 100px">{{ $user->userInfo->awards }}</textarea>
                            <label for="floatingTextarea2">Awards</label>
                            @error('awards')
                            <span class="error-msg">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="form-floating mb-3">
                            <textarea class="form-control" placeholder="About" id="floatingTextarea2" name="affiliations" style="height: 100px">{{ $user->userInfo->awards }}</textarea>
                            <label for="floatingTextarea2">Affiliations</label>
                            @error('affiliations')
                            <span class="error-msg">{{ $message }}</span>
                            @enderror
                        </div>
                        @endif
                    </div>
                    <div class="modal-footer">
                        <button type="reset" class="deny-btn border-0">Clear</button>
                        <button type="submit" class="border-0 submit-btn">Save Changes</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>

@push('map-scripts')
<script async defer src="https://maps.googleapis.com/maps/api/js?key=AIzaSyA4IdhyGOY2rDNFymY1kGR3qaS6K4RlWEY&libraries=places&loading=async&callback=initMap"></script>
<script src="{{ asset('js/initMap.js') }}"></script>
@endpush

<script>
    function clearFileInput(id) {
        var input = document.getElementById(id);
        input.value = '';
    }

    // function initMap() {
    //     var lat = parseFloat(document.getElementById('lat').value);
    //     var lng = parseFloat(document.getElementById('long').value);
    //     var latlng = {
    //         lat: lat,
    //         lng: lng
    //     };

    //     // Create the map, centered at the initial location
    //     var map = new google.maps.Map(document.getElementById('map'), {
    //         zoom: 8,
    //         center: latlng
    //     });

    //     // Add a draggable marker to the map
    //     var marker = new google.maps.Marker({
    //         position: latlng,
    //         map: map,
    //         draggable: true,
    //         title: 'Drag me to your location!'
    //     });

    //     // // Set the hidden input fields to the default location when the map is loaded
    //     // document.getElementById('lat').value = lat;
    //     // document.getElementById('long').value = lng;

    //     // Function to reverse geocode based on lat and lng
    //     function reverseGeocode(lat, lng) {
    //         var geocoder = new google.maps.Geocoder();
    //         var latlng = {
    //             lat: parseFloat(lat),
    //             lng: parseFloat(lng)
    //         };

    //         // Reverse geocode to get the address
    //         geocoder.geocode({
    //             location: latlng
    //         }, function(results, status) {
    //             var locationElement = document.getElementById('loc');
    //             if (status === 'OK') {
    //                 if (results[0]) {
    //                     var addressParts = results[0].formatted_address.split(',');
    //                     // Extract the city and country (assuming city at index 1 and country at the end)
    //                     var city = addressParts[1].trim();
    //                     var country = addressParts[addressParts.length - 1].trim();
    //                     locationElement.value = city + ", " + country;
    //                     console.log("locationElement value: " + locationElement.value);
    //                 } else {
    //                     locationElement.value = "No address found";
    //                 }
    //             } else {
    //                 locationElement.value = "Geocoder failed: " + status;
    //             }
    //         });
    //     }


    //     function updateCoordinates(markerPosition) {
    //         var lat = markerPosition.lat();
    //         var lng = markerPosition.lng();
    //         document.getElementById('lat').value = lat;
    //         document.getElementById('long').value = lng;
    //         document.getElementById('coordinates').innerText = 'Latitude: ' + lat + ', Longitude: ' + lng;

    //         reverseGeocode(lat, lng);
    //     }

    //     // Call reverseGeocode with the default initial location when the map is initialized
    //     reverseGeocode(latlng.lat, latlng.lng);

    //     // Place the marker where the user clicks on the map
    //     map.addListener('click', function(event) {
    //         var clickedLocation = event.latLng;
    //         marker.setPosition(clickedLocation);
    //         marker.setMap(map);
    //         updateCoordinates(clickedLocation);
    //     });

    //     // Automatically update the coordinates when the marker is dragged
    //     marker.addListener('dragend', function() {
    //         updateCoordinates(marker.getPosition());
    //     });

    //     // Create the search box and link it to the UI element
    //     var input = document.getElementById('pac-input');
    //     var searchBox = new google.maps.places.SearchBox(input);
    //     map.controls[google.maps.ControlPosition.TOP_LEFT].push(input);

    //     // Bias the SearchBox results towards current map's viewport
    //     map.addListener('bounds_changed', function() {
    //         searchBox.setBounds(map.getBounds());
    //     });

    //     // Listen for the event fired when the user selects a prediction and retrieves more details for that place
    //     searchBox.addListener('places_changed', function() {
    //         var places = searchBox.getPlaces();

    //         if (places.length == 0) {
    //             return;
    //         }

    //         // Clear out the old marker
    //         marker.setMap(null);

    //         // For each place, get the icon, name, and location
    //         var bounds = new google.maps.LatLngBounds();
    //         places.forEach(function(place) {
    //             if (!place.geometry || !place.geometry.location) {
    //                 console.log("Returned place contains no geometry");
    //                 return;
    //             }

    //             // Create a new marker for the selected place
    //             marker = new google.maps.Marker({
    //                 position: place.geometry.location,
    //                 map: map,
    //                 draggable: true
    //             });

    //             // Automatically update coordinates when the new marker is dragged
    //             marker.addListener('dragend', function() {
    //                 updateCoordinates(marker.getPosition());
    //             });

    //             // Immediately update the coordinates for the selected place
    //             updateCoordinates(marker.getPosition());

    //             if (place.geometry.viewport) {
    //                 // Only geocodes have viewport
    //                 bounds.union(place.geometry.viewport);
    //             } else {
    //                 bounds.extend(place.geometry.location);
    //             }
    //         });
    //         map.fitBounds(bounds);
    //     });
    // }

    // // Initialize the map and geocoding
    // window.onload = initMap;
</script>