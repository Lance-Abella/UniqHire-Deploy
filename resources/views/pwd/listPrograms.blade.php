@extends('layout')

@section('page-title', 'Browse Training Programs')

@section('page-content')

<div class="pwd-browse-prog mb-3">
    <div class="filter-container">
        <form action="{{ route('pwd-list-program') }}" method="GET" id="filterForm">
            <div class="d-flex justify-content-between mb-3">
                <h3>Filter</h3>
                <i class='bx bx-filter-alt fs-3 sub-text text-end'></i>
            </div>
            <div class="mb-5">
                <span>
                    <p>Education Level</p>
                </span>
                @foreach($educations as $education)
                @if($education->id !== 1)
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" value="{{$education->education_name}}" id="flexCheckChecked{{$loop->index}}" name="education[]" onchange="submitForm()" {{ in_array($education->education_name, request()->input('education', [])) ? 'checked' : '' }}>
                    <label class="form-check-label" for="flexCheckChecked{{$loop->index}}">
                        {{$education->education_name}} &nbsp;<span class="count sub-text">({{ $educationCounts[$education->id]->program_count }})</span>
                    </label>
                </div>
                @endif
                @endforeach
            </div>
            <div class="d-flex justify-content-center align-items-center">
                <button type="submit" class="submit-btn border-0">Apply Filters</button>
            </div>
        </form>
    </div>
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
            <div class="prog-grid" id="prog-grid">
                <div class="mb-4">
                    <div class="recommend-label-container">
                        <span class="recommend-label">Recommended</span>
                    </div>
                </div>
                @if ($paginatedItems->isEmpty())
                <div class="sub-text no-result">No results found.</div>
                @else
                <div class="prog-grid-list">
                    @foreach ($paginatedItems as $ranked)
                    <div class="prog-card" data-program-id="{{ $ranked['program']->id }}" data-lat="{{ $ranked['program']->latitude }}" data-lng="{{ $ranked['program']->longitude }}">
                        <input type="hidden" name="" value="{{$ranked['similarity']}}" id="">
                        <div class="">
                            <a href="{{ route('training-details', $ranked['program']->id ) }}" class="d-flex prog-texts">
                                <div class="prog-texts-container">
                                    <div class=" d-flex mb-2">
                                        <div class="prog-img" @if (!empty($ranked['program']->agency->userInfo->profile_path)) style=" background-image: url({{ asset($ranked['program']->agency->userInfo->profile_path) }}); background-repeat: no-repeat; background-size: cover; " @endif>

                                            @if (empty($ranked['program']->agency->userInfo->profile_path))
                                            <span>{{ strtoupper(substr($ranked['program']->agency->userInfo->name, 0, 1)) }}</span>
                                            @endif

                                        </div>
                                        <div class="d-flex justify-content-between prog-head">
                                            <div class="header">
                                                <h4 class="text-cap">{{$ranked['program']->title}}</h4>
                                                <p class="sub-text text-cap">{{$ranked['program']->agency->userInfo->name}}</p>
                                                <p class="sub-text text-cap location" id="location-{{ $ranked['program']->id }}"><i class='bx bx-map sub-text'></i>{{$ranked['program']->location}}</p>
                                                <input type="hidden" id="lat-{{ $ranked['program']->id }}" value="{{ $ranked['program']->latitude }}">
                                                <input type="hidden" id="lng-{{ $ranked['program']->id }}" value="{{ $ranked['program']->longitude }}">
                                            </div>
                                            <div class="text-end date-posted">
                                                @php
                                                $diff = $ranked['program']->created_at->diffInSeconds(now());
                                                @endphp
                                                <p class="text-end">
                                                    @if ($diff < 60)
                                                        {{ $diff }}s
                                                        @elseif ($diff < 3600)
                                                        {{ floor($diff / 60) }}m
                                                        @elseif ($diff < 86400)
                                                        {{ floor($diff / 3600) }}h
                                                        @else
                                                        {{ $ranked['job']->created_at->diffForHumans() }}
                                                        @endif
                                                        </p>
                                            </div>
                                        </div>
                                    </div>

                                </div>
                                <div class="row prog-desc mb-1">
                                    <p>{{$ranked['program']->description}}</p>
                                </div>
                                <div class="infos">
                                    <input type="hidden" id="user-disability" value="{{Auth::user()->userInfo->disability_id}}">
                                    @foreach ($ranked['program']->disability as $disability)
                                    <div class="disability-item" data-disability-id="{{ $disability->id }}">
                                        {{$disability->disability_name}}
                                    </div>
                                    @endforeach

                                    <div class="match-info @if (Auth::user()->userInfo->education->id != $ranked['program']->education->id) notmatch-info @endif">
                                        {{$ranked['program']->education->education_name}}
                                    </div>
                                    <div class="match-info @if (Auth::user()->userInfo->age < $ranked['program']->start_age || Auth::user()->userInfo->age > $ranked['program']->end_age) notmatch-info @endif">
                                        {{$ranked['program']->start_age . ' - ' . $ranked['program']->end_age}}
                                    </div>
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
<!-- <div class="filter-container">
        <form action="{{ route('pwd-list-program') }}" method="GET" id="filterForm">
            <div class="d-flex justify-content-between mb-3">
                <h3>Filter</h3>
                <i class='bx bx-filter-alt fs-3 sub-text'></i>
            </div>
            <div class="mb-3">
                <span>
                    <p>Education Level</p>
                </span>
                @foreach($educations as $education)
                @if($education->id !== 1)
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" value="{{$education->education_name}}" id="flexCheckChecked{{$loop->index}}" name="education[]" onchange="submitForm()" {{ in_array($education->education_name, request()->input('education', [])) ? 'checked' : '' }}>
                    <label class="form-check-label" for="flexCheckChecked{{$loop->index}}">
                        {{$education->education_name}} &nbsp;<span class="count sub-text">({{ $educationCounts[$education->id]->program_count }})</span>
                    </label>
                </div>
                @endif
                @endforeach
            </div>
        </form>
    </div>
    <div class="d-flex flex-column align-items-center browse-area">
        <div class="mb-4 searchbar-container">
            <div class="col d-flex justify-content-center">
                <form role="search" action="{{ route('pwd-list-program') }}" method="GET" id="searchForm">
                    <div class="d-flex searchbar">
                        <input class="form-control" type="search" placeholder="Search Training Programs" aria-label="Search" id="searchInput" onchange="checkAndSubmit()" name="search" value="{{ request('search') }}">
                        <button class="submit-btn border-0" type="submit">Search</button>
                    </div>
                </form>
            </div>
        </div>
        <div class="outer">

            <div class="prog-grid" id="prog-grid">
                <div class="mb-4">
                    <div class="recommend-label-container">
                        <span class="recommend-label">Recommended</span>
                    </div>
                </div>
                @forelse ($paginatedItems as $ranked)
                <div class="row prog-card mb-2" data-program-id="{{ $ranked['program']->id }}" data-lat="{{ $ranked['program']->latitude }}" data-lng="{{ $ranked['program']->longitude }}">
                    <input type="hidden" name="" value="{{$ranked['similarity']}}" id="">
                    <div class="col ">
                        <a href="{{ route('training-details', $ranked['program']->id ) }}" class="d-flex prog-texts">
                            <div class="prog-texts-container">
                                <div class=" d-flex mb-2">
                                    <div class="prog-img" @if (!empty($ranked['program']->agency->userInfo->profile_path)) style=" background-image: url({{ asset($ranked['program']->agency->userInfo->profile_path) }}); background-repeat: no-repeat; background-size: cover; " @endif>

                                        @if (empty($ranked['program']->agency->userInfo->profile_path))
                                        <span>{{ strtoupper(substr($ranked['program']->agency->userInfo->name, 0, 1)) }}</span>
                                        @endif

                                    </div>
                                    <div class="d-flex justify-content-between prog-head">
                                        <div class="header">
                                            <h4 class="text-cap">{{$ranked['program']->title}}</h4>
                                            <p class="sub-text text-cap">{{$ranked['program']->agency->userInfo->name}}</p>
                                            <p class="sub-text text-cap location" id="location-{{ $ranked['program']->id }}"><i class='bx bx-map sub-text'></i>Loading address...</p>
                                            <input type="hidden" id="lat-{{ $ranked['program']->id }}" value="{{ $ranked['program']->latitude }}">
                                            <input type="hidden" id="lng-{{ $ranked['program']->id }}" value="{{ $ranked['program']->longitude }}">
                                        </div>
                                        <div class="text-end date-posted">
                                            <p class="text-end">{{ $ranked['program']->created_at->diffForHumans() }}</p>
                                        </div>
                                    </div>

                                </div>
                                <div class="row prog-desc mb-1">
                                    <p>{{$ranked['program']->description}}</p>
                                </div>
                                <div class="infos">
                                    <input type="hidden" id="user-disability" value="{{Auth::user()->userInfo->disability_id}}">
                                    @foreach ($ranked['program']->disability as $disability)
                                    <div class="disability-item" data-disability-id="{{ $disability->id }}">
                                        {{$disability->disability_name}}
                                    </div>
                                    @endforeach

                                    <div class="match-info @if (Auth::user()->userInfo->education->id != $ranked['program']->education->id) notmatch-info @endif">
                                        {{$ranked['program']->education->education_name}}
                                    </div>
                                    <div class="match-info @if (Auth::user()->userInfo->age < $ranked['program']->start_age || Auth::user()->userInfo->age > $ranked['program']->end_age) notmatch-info @endif">
                                        {{$ranked['program']->start_age . ' - ' . $ranked['program']->end_age}}
                                    </div>
                                </div>
                            </div>
                            <div class="fs-3 d-flex flex-column align-items-center justify-content-center">
                                >
                            </div>
                        </a>
                    </div>
                </div>
                @empty
                <div class="sub-text no-result">No results found.</div>
                @endforelse
            </div>
        </div>
        <div class="pagination-container">
            <div class="pagination">
                {{ $paginatedItems->links() }}
            </div>
        </div>

    </div> -->
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
                item.style.display = "none";
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

    // function submitForm() {
    //     document.getElementById('filterForm').submit();
    // }

    function checkAndSubmit() {
        var searchInput = document.getElementById('searchInput');
        if (searchInput.value.trim() === ' ') {
            document.getElementById('searchForm').submit();
        }
    }
</script>
@endsection