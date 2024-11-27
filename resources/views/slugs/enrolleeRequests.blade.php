<button type="button" class="submit-btn modal-btn border-0" data-bs-toggle="modal" data-bs-target="#staticBackdrop">Enrollee Requests</button>
<div class="modal fade" id="staticBackdrop" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="staticBackdropLabel">Enrollee Requests</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="request-grid">
                    @forelse ($requests as $request)
                    <input type="hidden" name="program" value="{{ $program->id }}">
                    <div class="request-container">
                        <a href="{{ route('show-profile', $request->user->id) }}">
                            <div class="request-owner mb-2">
                                <div class="request-pic" @if (!empty($request->user->userInfo->profile_path)) style=" background-image: url({{ asset($request->user->userInfo->profile_path) }}); background-repeat: no-repeat; background-size: cover; " @endif>
                                    @if (empty($request->user->userInfo->profile_path))
                                    <span>{{ strtoupper(substr($request->user->userInfo->name, 0, 1)) }}</span>
                                    @endif
                                </div>
                                <div class="owner-name">
                                    <p class="fs-5">{{ $request->user->userInfo->name }}</p>
                                    <input type="hidden" class="lat" value="{{ $request->user->userInfo->latitude }}">
                                    <input type="hidden" class="lng" value="{{ $request->user->userInfo->longitude }}">
                                    <p class="sub-text mb-2 location text-cap" id="location"><i class='bx bx-map sub-text'></i>{{ $request->user->userInfo->location}}</p>
                                    <span class="match-info">{{ $request->user->userInfo->disability->disability_name }}</span>
                                </div>
                            </div>
                            <div class="d-flex align-items-center">
                                <div class="text-end btn-container">
                                    <form action="{{ route('agency-accept') }}" method="POST">
                                        @csrf
                                        <input type="hidden" name="pwd_id" value="{{ $request->user->id }}">
                                        <input type="hidden" name="program_id" value="{{ $program->id }}">
                                        <input type="hidden" name="training_application_id" value="{{ $request->id }}">
                                        <button type="submit" class="submit-btn border-0">Accept</button>
                                    </form>
                                    <button type="button" class="deny-btn border-0">Deny</button>
                                </div>
                                >
                            </div>
                        </a>
                    </div>
                    @empty
                    <div class="text-center">No requests yet.</div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        var geocoder = new google.maps.Geocoder();

        document.querySelectorAll('.request-container').forEach(function(container) {
            var lat = parseFloat(container.querySelector('.lat').value);
            var lng = parseFloat(container.querySelector('.lng').value);
            var locationElement = container.querySelector('.location');

            if (!isNaN(lat) && !isNaN(lng)) {
                var latlng = {
                    lat: lat,
                    lng: lng
                };

                geocoder.geocode({
                    location: latlng
                }, function(results, status) {
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
            } else {
                locationElement.innerHTML = "<i class='bx bx-map sub-text'></i> Invalid coordinates";
            }
        });
    });
</script>