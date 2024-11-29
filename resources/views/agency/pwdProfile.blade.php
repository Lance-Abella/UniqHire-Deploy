@extends('layout')

@section('page-title', 'User Profile')
@section('page-content')
<div class="profile-container container">
    <div class="back-btn">
        <a href="{{ url()->previous() }}" class="m-1 back-link"><i class='bx bx-left-arrow-alt'></i></a>
    </div>
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
                    <p class="text-cap" id="location"><i class='bx bx-map sub-text'></i>{{ $user->userInfo->location }}</p>
                    <input type="hidden" id="lat" value="{{ $user->userInfo->latitude }}">
                    <input type="hidden" id="lng" value="{{ $user->userInfo->longitude }}">
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
            <div class="bio-item exp">
                <div>
                    <div class="mb-3 d-flex justify-content-between exp-header">
                        <h4 class="">Skills&nbsp;&nbsp;</h4>
                    </div>
                    <ul class="experiences">
                        @forelse($skilluser as $skill)
                        <li class="mb-1">
                            <div class="d-flex">
                                <div class="d-flex">
                                    <div class="exp-container">
                                        <p class="skill-title">{{ $skill->skill->title }}</p>
                                    </div>
                                    <form action="{{ route('delete-skill', $skill->id) }}" method="POST" class="d-flex justify-content-end">
                                        @csrf
                                        @method('DELETE')
                                        <button class="border-0 match-info skill-delete-btn delete-btn" style="display: none;"><i class='bx bx-x'></i></button>
                                    </form>
                                </div>
                            </div>
                        </li>
                        @empty
                        <div class="about sub-text">No Skills. Add one.</div>
                        @endforelse
                    </ul>
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
                    <div class="mb-3 d-flex justify-content-between exp-header">
                        <h4 class="">Work Experience&nbsp;&nbsp;</h4>
                    </div>
                    <ul class="experiences">
                        @forelse($experiences as $experience)
                        <li class="mb-1">
                            <div class="d-flex">
                                <div class="exp-container">
                                    <p class="exp-title">{{ $experience->title }}</p>
                                    <p class="exp-date">{{ \Carbon\Carbon::parse($experience->date)->format('M d, Y') }}</p>
                                </div>
                                <form action="{{ route('delete-experience', $experience->id) }}" method="POST" class="d-flex justify-content-end">
                                    @csrf
                                    @method('DELETE')
                                    <button class="border-0 match-info exp-delete-btn delete-btn" style="display: none;"><i class='bx bx-x'></i></button>
                                </form>
                            </div>
                        </li>
                        @empty
                        <div class="about sub-text">No experiences. Add one.</div>
                        @endforelse
                    </ul>
                </div>
            </div>
        </div>

    </div>
</div>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        var geocoder = new google.maps.Geocoder();

        // Fetch latitude and longitude values
        var lat = parseFloat(document.getElementById('lat').value);
        var lng = parseFloat(document.getElementById('lng').value);
        var locationElement = document.getElementById('location');

        if (!isNaN(lat) && !isNaN(lng)) {
            var latlng = {
                lat: lat,
                lng: lng
            };

            // Perform the geocode request
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
</script>

@endsection