@extends('layout')

@section('page-title', 'Browse Training Programs')

@section('page-content')

<div class="pwd-browse-prog mb-3" style="display:flex; justify-content:center">
    <div class="list">
        <div class="mb-4 searchbar-container">
            <div class="d-flex justify-content-center">
                <form role="search" action="{{ route('pwd-list-program') }}" method="GET" id="searchForm">
                    <div class="d-flex searchbar">
                        <input class="form-control" type="search" placeholder="Search Training Programs" aria-label="Search" id="searchInput" onchange="checkAndSubmit()" name="search" value="{{ request('search') }}">
                        <button class="submit-btn border-0" type="submit">Search</button>
                    </div>
                </form>
            </div>
        </div>
        <div class="d-flex flex-column align-items-center">
            <div class="prog-grid" id="prog-grid" style="width:100%">
                <div class="mb-4">
                    <div class="recommend-label-container">
                        <span class="recommend-label">Recommended</span>
                    </div>
                </div>
                @if ($paginatedItems->isEmpty())
                <div class="sub-text no-result">No results found.</div>
                @else
                <div class="prog-grid-list">
                    @foreach ($paginatedItems as $program)
                    <div class="prog-card" data-program-id="{{ $program->id }}" data-lat="{{ $program->latitude }}" data-lng="{{ $program->longitude }}">
                        <div class="">
                            <a href="{{ route('training-details', $program->id ) }}" class="d-flex prog-texts">
                                <div class="prog-texts-container">
                                    <div class=" d-flex mb-2">
                                        <div class="prog-img" @if (!empty($program->agency->userInfo->profile_path)) style=" background-image: url({{ asset($program->agency->userInfo->profile_path) }}); background-repeat: no-repeat; background-size: cover; " @endif>

                                            @if (empty($program->agency->userInfo->profile_path))
                                            <span>{{ strtoupper(substr($program->agency->userInfo->name, 0, 1)) }}</span>
                                            @endif

                                        </div>
                                        <div class="d-flex justify-content-between prog-head">
                                            <div class="header">
                                                <h4 class="text-cap">{{$program->title}}</h4>
                                                <p class="sub-text text-cap">{{$program->agency->userInfo->name}}</p>
                                                <p class="sub-text text-cap location" id="location-{{ $program->id }}"><i class='bx bx-map sub-text'></i>Loading address...</p>
                                                <input type="hidden" id="lat-{{ $program->id }}" value="{{ $program->latitude }}">
                                                <input type="hidden" id="lng-{{ $program->id }}" value="{{ $program->longitude }}">
                                            </div>
                                            <div class="text-end date-posted">
                                                <p class="text-end">{{ $program->created_at->diffForHumans() }}</p>
                                            </div>
                                        </div>

                                    </div>
                                    <div class="row prog-desc mb-1">
                                        <p>{{$program->description}}</p>
                                    </div>
                                </div>
                                <!-- <div class="fs-3 d-flex flex-column align-items-center justify-content-center">
                                >
                            </div> -->
                            </a>
                        </div>
                    </div>
                    @endforeach
                </div>
                @endif
            </div>
            <div class="pagination-container">
                <div class="pagination">
                    {{ $paginatedItems->links() }}
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Assume userDisabilityId is available from the backend (you can inject it into a script tag or pass it as a data attribute)
        var userDisabilityId = parseInt(document.getElementById('user-disability').value, 10);

        // Get all programs' disability containers
        var disabilityItems = document.querySelectorAll('.disability-item');

        disabilityItems.forEach(function(item) {
            var programDisabilityId = parseInt(item.getAttribute('data-disability-id'), 10);

            // Check if the user's disability matches this program's disability
            if (programDisabilityId === userDisabilityId) {
                item.classList.add('match-info');
                item.classList.remove('notmatch-info');
            } else {
                item.classList.add('notmatch-info');
                item.classList.remove('match-info');
            }
        });

        var geocoder = new google.maps.Geocoder();

        document.querySelectorAll('.prog-card').forEach(function(card) {
            var programId = card.getAttribute('data-program-id');
            var lat = parseFloat(card.getAttribute('data-lat'));
            var lng = parseFloat(card.getAttribute('data-lng'));
            var latlng = {
                lat: lat,
                lng: lng
            };

            geocoder.geocode({
                location: latlng
            }, function(results, status) {
                var locationElement = document.getElementById('location-' + programId);
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

    function submitForm() {
        document.getElementById('filterForm').submit();
    }

    function checkAndSubmit() {
        var searchInput = document.getElementById('searchInput');
        if (searchInput.value.trim() === ' ') {
            document.getElementById('searchForm').submit();
        }
    }
</script>
@endsection