@extends('layout')
@section('page-title', 'Edit Training Program')
@section('page-content')
<form action="{{ route('programs-edit', $program->id) }}" method="POST" class="container edit-form">
    @csrf
    @method('PUT')
    <div class="row mt-2 mb-2 border-bottom">
        <div class="text-start header-texts back-link-container">
            <a href="{{ route('programs-show', $program->id) }}" class="m-1 back-link"><i class='bx bx-left-arrow-alt'></i></a>
            Edit Training Program.
        </div>
    </div>
    <div class="row">
        <div class="col">
            <div class="form-floating mb-3">
                <input type="text" class="form-control" id="floatingInput" name="title" value="{{ $program->title }}" required placeholder="Title">
                <label for="floatingInput">Title</label>
                @error('title')
                <span class="error-msg">{{ $message }}</span>
                @enderror
            </div>
        </div>
    </div>
    <input type="hidden" id="lat" name="lat" value="{{ $program->latitude }}">
    <input type="hidden" id="long" name="long" value="{{ $program->longitude }}">
    <input type="hidden" id="loc" name="loc" required>
    <input id="pac-input" class="controls" type="text" placeholder="Search Box">
    <label for="map">Select Your Location:</label>
    <div id="map" class="map"></div>
    <p id="coordinates"></p>
    <div class="row">
        <div class="col">
            <div class="form-floating mb-3">
                <textarea class="form-control" placeholder="Description" id="floatingTextarea2" name="description" style="height: 200px">{{ $program->description }}</textarea>
                <label for="floatingTextarea2">Description</label>
                @error('description')
                <span class="error-msg">{{ $message }}</span>
                @enderror
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col">
            <div class="row">
                <div class="col">
                    <div class="form-floating mb-3">
                        <input type="number" class="form-control" id="startAge" name="start_age" value="{{$program->start_age}}" required placeholder="Input Age">
                        <label for="floatingInput">Age Range (from)</label>
                        @error('age')
                        <span class="error-msg">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
                <div class="col">
                    <div class="form-floating mb-3">
                        <input type="number" class="form-control" id="endAge" name="end_age" value="{{$program->end_age}}" required placeholder="Input Age">
                        <label for="floatingInput">Age Range (to)</label>
                        @error('age')
                        <span class="error-msg">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="form-floating mb-3">
                <input type="text" class="form-control date" name="schedule" required placeholder="Choose Date" value="{{$program->schedule}}">
                <label for="floatingInput">Choose Date</label>
            </div>
        </div>
    </div>
    <div class="row mb-3">
        <div class="col">
            <h5>Select Disabilities</h5>
            <div class="req-container">
                @foreach ($disabilities as $disability)
                @if ($disability->disability_name != 'Not Applicable')
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" value="{{ $disability->id }}" id="flexCheckChecked{{ $loop->index }}" name="disabilities[]"
                        @if (isset($program->disability) && $program->disability->contains('disability_name', $disability->disability_name))
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
                        @if (isset($program->skill) && $program->skill->contains('title', $skill->title))
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
    <div class="row">
        <div class="col">
            <div class="form-floating mb-3">
                <input type="text" class="form-control" id="participants" name="participants" value="{{ $program->participants }}" required placeholder="Input Participants" oninput="formatNumber(this)">
                <label for="floatingInput">Number of Participants</label>
                @error('participants')
                <span class="error-msg">{{ $message }}</span>
                @enderror
            </div>
        </div>
        <div class="col">
            <div class="form-floating mb-3">
                <select class="form-select" id="floatingSelect" name="education" aria-label="Floating label select example">
                    @foreach ($levels as $level)
                    @if ($level->education_name != 'Not Applicable')
                    <option value="{{ $level->id }}" @if($program->education->id == $level->id) selected @endif>{{ $level->education_name }}</option>
                    @endif
                    @endforeach
                </select>
                <label for="floatingSelect">Education Level (at least)</label>
            </div>
        </div>
    </div>
    <hr>
    <div>
        <div class="form-check">
            <input class="form-check-input" type="checkbox" value="" id="host-crowdfund" onchange="toggleCrowdfund()" {{ $program->crowdfund ? 'checked' : '' }}>
            <label class="form-check-label" for="flexCheckDefault">
                Host a crowdfunding for this?
            </label>
        </div>
    </div>
    <div class="row" id="crowdfund-section">
        <div class="col">
            <div class="form-floating mb-3">
                <input type="text" class="form-control" id="amount-needed" name="goal" required placeholder="Amount Needed" value="{{ $program->crowdfund->goal ?? '' }}" {{ $program->crowdfund ? '' : 'disabled' }} oninput="formatNumber(this)">
                <label for="floatingInput">Amount Needed</label>
                @error('goal')
                <span class="error-msg">{{ $message }}</span>
                @enderror
            </div>
        </div>
    </div>
    <div class="row border-bottom">
        <div class="col">
            <div class="form-floating mb-3">
                <div id="competencyListContainer">
                    <label for="competencyList">Competencies:</label>
                    <div id="competencyList">
                        @foreach ($program->competencies as $competency)
                        <div class="input-group mb-3">
                            <input type="text" class="form-control" placeholder="Enter competency" name="competencies[]" value="{{ $competency->name }}" required>
                            <button class="btn btn-outline-secondary remove-btn" type="button">Remove</button>
                        </div>
                        @endforeach
                    </div>
                    <button type="button" id="addCompetencyBtn" class="submit-btn add-comp"><i class="bx bx-plus"></i> Add Competency</button>
                </div>
            </div>
        </div>
    </div>
    <div class="d-flex justify-content-evenly mt-3 prog-btn">
        <a href="{{route('programs-show', $program->id)}}" class="deny-btn border-0">Cancel</a>
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

    function toggleCrowdfund() {
        var hostCrowdfund = document.getElementById('host-crowdfund');
        var crowdfundSection = document.getElementById('crowdfund-section');

        if (hostCrowdfund.checked) {
            // crowdfundSection.style.display = 'block';
            document.getElementById('amount-needed').disabled = false;
            document.getElementById('amount-needed').required = true;
        } else {
            // crowdfundSection.style.display = 'none';
            document.getElementById('amount-needed').disabled = true;
            document.getElementById('amount-needed').required = false;
        }
    }

    function sortAndFormatDates(dateInput) {
        let dates = dateInput.val().split(',');

        // Parse and sort the dates
        dates = dates.map(date => new Date(date.trim()));
        dates.sort((a, b) => a - b);

        // Format the dates back to the desired format (mm/dd/yyyy)
        const sortedDates = dates.map(date =>
            ('0' + (date.getMonth() + 1)).slice(-2) + '/' +
            ('0' + date.getDate()).slice(-2) + '/' +
            date.getFullYear()
        );

        // Update the input field with the sorted dates
        dateInput.val(sortedDates.join(','));
    }

    document.addEventListener('DOMContentLoaded', function() {
        $('.date').datepicker({
            multidate: true,
            todayHighlight: true,
        }).on('changeDate', function(e) {
            sortAndFormatDates($(this));
        });

        // Trigger sorting when the input field loses focus
        $('.date').on('blur', function() {
            sortAndFormatDates($(this));
        });

        var amountNeededInput = document.getElementById('amount-needed');
        var participantsInput = document.getElementById('participants');
        if (amountNeededInput) {
            formatNumber(amountNeededInput);
        }
        if (participantsInput) {
            formatNumber(participantsInput);
        }     

        let competencyCount = document.querySelectorAll('#competencyList .input-group').length;
        const addCompetencyBtn = document.getElementById('addCompetencyBtn');
        const competencyList = document.getElementById('competencyList');

        function toggleButtons() {
            if (competencyCount >= 4) {
                addCompetencyBtn.classList.add('d-none');
            } else {
                addCompetencyBtn.classList.remove('d-none');
            }
        }

        addCompetencyBtn.addEventListener('click', function() {
            if (competencyCount < 4) {
                competencyCount++;
                const competencyItem = document.createElement('div');
                competencyItem.className = 'input-group mb-3';
                competencyItem.innerHTML = `
                <input type="text" class="form-control" placeholder="Enter competency" name="competencies[]" required>
                <button class="btn btn-outline-secondary remove-btn" type="button">Remove</button>
            `;
                competencyList.appendChild(competencyItem);

                competencyItem.querySelector('.remove-btn').addEventListener('click', function() {
                    competencyList.removeChild(competencyItem);
                    competencyCount--;
                    toggleButtons();
                });

                competencyItem.querySelector('input').addEventListener('input', toggleButtons);

                toggleButtons();
            }
        });

        document.querySelectorAll('.remove-btn').forEach(button => {
            button.addEventListener('click', function() {
                const competencyItem = this.parentElement;
                competencyList.removeChild(competencyItem);
                competencyCount--;
                toggleButtons();
            });
        });

        toggleCrowdfund();
        toggleButtons(); // Initialize the button states
    });

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

    //      // Function to reverse geocode based on lat and lng
    //     function reverseGeocode(lat, lng) {
    //         var geocoder = new google.maps.Geocoder();
    //         var latlng = { lat: parseFloat(lat), lng: parseFloat(lng) };

    //         // Reverse geocode to get the address
    //         geocoder.geocode({ location: latlng }, function(results, status) {
    //             var locationElement = document.getElementById('loc');
    //             if (status === 'OK') {
    //                 if (results[0]) {
    //                     var addressParts = results[0].formatted_address.split(',');
    //                     // Extract the city and country (assuming city at index 1 and country at the end)
    //                     var city = addressParts[1].trim();
    //                     var country = addressParts[addressParts.length - 1].trim();
    //                     locationElement.value =  city + ", " + country;
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

    //  // Initialize the map and geocoding
    // window.onload = initMap;

    

    
</script>
