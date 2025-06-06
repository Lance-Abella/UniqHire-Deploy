function initMap() {
    // Default location (e.g., Cebu City, Philippines)
    var initialLocation = {
        lat: 10.3156992,
        lng: 123.8854366,
    };

    // Create the map, centered at the initial location
    var map = new google.maps.Map(document.getElementById("map"), {
        zoom: 8,
        center: initialLocation,
    });

    // Add a draggable marker to the map
    var marker = new google.maps.Marker({
        position: initialLocation,
        map: map,
        draggable: true,
        title: "Drag me to your location!",
    });

    // Set the hidden input fields to the default location when the map is loaded
    document.getElementById("lat").value = initialLocation.lat;
    document.getElementById("long").value = initialLocation.lng;

    // Function to reverse geocode based on lat and lng and get the readable address
    function reverseGeocode(lat, lng) {
        var geocoder = new google.maps.Geocoder();
        var latlng = { lat: parseFloat(lat), lng: parseFloat(lng) };

        geocoder.geocode({ location: latlng }, function (results, status) {
            var locationElement = document.getElementById("loc");
            if (status === "OK") {
                if (results[0]) {
                    var addressParts = results[0].formatted_address.split(",");
                    // Extract the city and country (assuming city at index 1 and country at the end)
                    var city = addressParts[1].trim();
                    var country = addressParts[addressParts.length - 1].trim();
                    locationElement.value = city + ", " + country;
                    console.log(
                        "locationElement value: " + locationElement.value
                    );
                } else {
                    locationElement.value = "No address found";
                }
            } else {
                locationElement.value = "Geocoder failed: " + status;
            }
        });
    }

    // Update hidden inputs and display coordinates
    function updateCoordinates(markerPosition) {
        var lat = markerPosition.lat();
        var lng = markerPosition.lng();
        document.getElementById("lat").value = lat;
        document.getElementById("long").value = lng;
        // document.getElementById("coordinates").innerText =
        //     "Latitude: " + lat + ", Longitude: " + lng;

        reverseGeocode(lat, lng);
    }

    // Call reverseGeocode with the default initial location when the map is initialized
    reverseGeocode(initialLocation.lat, initialLocation.lng);

    // Place the marker where the user clicks on the map
    map.addListener("click", function (event) {
        var clickedLocation = event.latLng;
        marker.setPosition(clickedLocation);
        marker.setMap(map);
        updateCoordinates(clickedLocation);
    });

    // Automatically update the coordinates when the marker is dragged
    marker.addListener("dragend", function () {
        updateCoordinates(marker.getPosition());
    });

    // Create the search box and link it to the UI element
    var input = document.getElementById("pac-input");
    var searchBox = new google.maps.places.SearchBox(input);
    map.controls[google.maps.ControlPosition.TOP_LEFT].push(input);

    // Bias the SearchBox results towards current map's viewport
    map.addListener("bounds_changed", function () {
        searchBox.setBounds(map.getBounds());
    });

    // Listen for the event fired when the user selects a prediction and retrieves more details for that place
    searchBox.addListener("places_changed", function () {
        var places = searchBox.getPlaces();

        if (places.length == 0) {
            return;
        }

        // Clear out the old marker
        marker.setMap(null);

        // For each place, get the icon, name, and location
        var bounds = new google.maps.LatLngBounds();
        places.forEach(function (place) {
            if (!place.geometry || !place.geometry.location) {
                console.log("Returned place contains no geometry");
                return;
            }

            // Create a new marker for the selected place
            marker = new google.maps.Marker({
                position: place.geometry.location,
                map: map,
                draggable: true,
            });

            // Automatically update coordinates when the new marker is dragged
            marker.addListener("dragend", function () {
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
// document.addEventListener("DOMContentLoaded", initMap);
