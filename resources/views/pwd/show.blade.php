@extends('layout')

@section('page-title', 'Program Details')

@section('page-content')
<div class="agency-show-prog">
    <div class="back-btn">
        @if (Route::currentRouteName() == 'training-details')
        <a href="{{ route('pwd-list-program') }}" class="m-1 back-link"><i class='bx bx-left-arrow-alt'></i></a>
        @elseif(Route::currentRouteName() == 'show-details' )
        <a href="{{ route('trainings') }}" class="m-1 back-link"><i class='bx bx-left-arrow-alt'></i></a>
        @endif
    </div>
    <div class="prog-details">
        <div class="header d-flex">
            <div class="mb-3 titles">
                <h3 class="text-cap">{{ $program->title }}</h3>
                <p class="sub-text text-cap">{{ $program->agency->userInfo->name }}</p>
                <p class="sub-text prog-loc text-cap" id="location"><i class='bx bx-map sub-text'></i>{{ $program->location }}</p>
                <input type="hidden" id="lat" value="{{ $program->latitude }}">
                <input type="hidden" id="lng" value="{{ $program->longitude }}">
            </div>
            <div class="prog-btn">
                <form id="apply-form-{{ $program->id }}" action="{{ route('pwd-application') }}" method="POST">
                    @csrf
                    <input type="hidden" name="user_id" value="{{ Auth::user()->id }}">
                    <input type="hidden" name="training_program_id" value="{{ $program->id }}">

                    @php
                    // Determine the application status
                    $applicationStatus = null;
                    foreach ($application as $app) {
                    if ($app->training_program_id == $program->id) {
                    $applicationStatus = $app->application_status;
                    break;
                    }
                    }
                    @endphp

                    @if ($applicationStatus == 'Pending')
                    <button type="submit" class="submit-btn pending border-0" disabled title="Your application is still in pending">
                        Pending
                    </button>
                    @elseif($applicationStatus == 'Approved')
                    <button type="submit" class="submit-btn approved border-0" disabled title="You are officially enrolled to this program">
                        <i class='bx bx-check'></i>
                    </button>
                    @else
                    <div class="d-flex flex-column align-items-end apply-btn-container">
                        <button type="submit" class="submit-btn border-0 {{ ((!$isCompletedProgram && !in_array($program->id, $nonConflictingPrograms)) || $slots <= 0) ? 'disabled' : '' }}" onclick="confirmApplication(event, 'apply-form-{{ $program->id }}')" @if((!$isCompletedProgram && !in_array($program->id, $nonConflictingPrograms)) || $slots <= 0) disabled @endif title="Apply for enrollment">
                                Apply
                        </button>
                        @if (!in_array($program->id, $nonConflictingPrograms))
                        <div class="text-center error">
                            <div class="text-danger d-flex justify-content-center">
                                Conflict to you schedule!
                            </div>
                        </div>
                        @elseif ($slots <= 0)
                            <div class="text-center error">
                            <div class="text-danger d-flex justify-content-center">
                                No slots available!
                            </div>
                            @endif
                    </div>
                    @endif
                </form>
            </div>
        </div>
        <div class="mb-5">
            <div class="col">
                {{ $program->description }}
            </div>
        </div>
        <ul class="nav nav-underline" role="tablist">
            <li class="nav-item">
                <a class="nav-link active" data-bs-toggle="tab" href="#requirements" role="tab">Requirements</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" data-bs-toggle="tab" href="#competencies" role="tab">Compentencies</a>
            </li>

            <li class="nav-item">
                <a class="nav-link" data-bs-toggle="tab" href="#enrollees" role="tab">Enrollees</a>
            </li>
            @if ($program->crowdfund)
            <li class="nav-item">
                <a class="nav-link" data-bs-toggle="tab" href="#sponsors" role="tab">Sponsors</a>
            </li>
            @endif
            <li class="nav-item">
                <a class="nav-link" data-bs-toggle="tab" href="#reviews" role="tab">Reviews</a>
            </li>
        </ul>
        <div class="tab-content">
            <div class="tab-pane active" id="requirements" role="tabpanel">
                <div class="requirements">
                    <div class="d-flex justify-content-start mb-5">
                        <div class="more-info">
                            <h5>Schedule</h5>
                            <p>
                                @foreach(explode(',', $program->schedule) as $date)
                                {{ \Carbon\Carbon::parse(trim($date))->format('F d, Y') }}
                                @if(!$loop->last)
                            <p></p>
                            @endif
                            @endforeach

                        </div>
                        <div class="more-info">
                            <h5>Time</h5>
                            <p>{{ \Carbon\Carbon::parse($program->start_time)->format('h:i A') }} - {{ \Carbon\Carbon::parse($program->end_time)->format('h:i A') }}
                            </p>
                            </p>
                        </div>
                        <div class="more-info">
                            <h5>Participants</h5>
                            <p>{{ number_format($program->participants) . ' Persons' }}&nbsp;&nbsp; <span class="sub-text">({{$slots}} remaining)</span></p>
                        </div>
                    </div>

                    <div class="d-flex justify-content-start mb-5">
                        <div class="more-info">
                            <h5>Age</h5>
                            <p class="match-info">{{ $program->start_age . ' - ' . $program->end_age . ' Years Old' }}</p>
                        </div>
                        <div class="more-info">
                            <h5>Skills Offered</h5>
                            <ul>
                                @foreach ($program->skill as $skill)
                                <li class="match-info mb-2">{{ $skill->title }}</li>
                                @endforeach
                            </ul>
                        </div>
                        <div class="more-info">
                            <h5>We Accept</h5>
                            <ul>
                                @foreach ($program->disability as $disability)
                                <li class="match-info mb-2">{{ $disability->disability_name }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                    <div class="d-flex justify-content-start more-info">

                        <div class="more-info">
                            <h5>Education Level (at least)</h5>
                            <span class="match-info">{{ $program->education->education_name }}</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="tab-pane" id="competencies" role="tabpanel">
                <div>
                    <h5>Competencies</h5>
                    <ul>
                        @forelse ($program->competencies as $competency)
                        <li>{{ $competency->name }}</li>
                        @empty
                        <div>No competencies yet.</div>
                        @endforelse
                    </ul>
                </div>
            </div>

            <div class="tab-pane enrollees" id="enrollees" role="tabpanel">
                <h5>Enrollees</h5>
                @forelse ($enrollees as $enrollee)
                <div class="user-card d-flex justify-content-between align-items-center py-3 px-3">
                    <div class="name">
                        <a href="{{ route('show-profile', $enrollee->application->user->id) }}">
                            {{ $enrollee->application->user->userInfo->name }}
                        </a>
                    </div>
                    <div class="status">
                        {{$enrollee->completion_status}}
                    </div>
                </div>
                @empty
                <div class="user-card text-center py-3 px-3">
                    No enrollees yet.
                </div>
                @endforelse
            </div>
            @if ($program->crowdfund)
            <div class="tab-pane" id="sponsors" role="tabpanel">
                <div class="crowdfund-progress mb-3">
                    <!-- <p class="sub-text">
                        Goal Amount: &nbsp;&nbsp;<span>{{number_format($program->crowdfund->goal, 0, '.', ',') . ' PHP'}}</span>
                    </p> -->
                    <p class="sub-text">
                        Current Funding: &nbsp;&nbsp;<span>{{number_format($program->crowdfund->raised_amount, 0, '.', ',') . ' PHP' . ' of ' . number_format($program->crowdfund->goal, 0, '.', ',') . ' PHP'}}</span>
                    </p>
                    <p class="sub-text">
                        Crowdfunding Progress:
                    </p>
                    <div class="progress">
                        <div class="progress-bar progress-bar-striped progress-bar-animated text-end" role="progressbar" aria-valuenow="{{ $program->crowdfund->progress }}" aria-valuemin="0" aria-valuemax="100"
                            @if ($program->crowdfund->progress !== null && $program->crowdfund->progress >= 20)
                            style="width: {{ $program->crowdfund->progress }}%;"
                            @else
                            style="width: 4%;"
                            @endif>{{ $program->crowdfund->progress }}%</div>
                    </div>
                </div>

                <h5>Sponsors</h5>
                @forelse ($sponsors as $sponsor)
                <p class="sub-text">{{$sponsor->name}} &nbsp;&nbsp;<em>({{number_format($sponsor->amount, 0, '.', ',') . ' PHP'}}) </em></p>
                @empty
                <div>No sponsors yet</div>
                @endforelse
            </div>
            @endif
            <div class="tab-pane" id="reviews" role="tabpanel">
                <div class="border reviews">
                    <div class="header border-bottom d-flex justify-content-between align-items-center">
                        <h3>Reviews</h3>
                        @if ($isCompletedProgram && !$userHasReviewed)
                        @include('slugs.feedback')
                        @endif
                    </div>
                    <div class="outer">
                        <div class="review-grid">
                            @forelse($reviews as $review)
                            <div class="body-review border">
                                <div class="owner border-bottom d-flex justify-content-between">
                                    <div class="owner-name">
                                        {{$review->pwd->userInfo->name}}
                                    </div>
                                    @if ($userHasReviewed && $review->pwd_id == Auth::user()->id)
                                    @include('slugs.editFeedback')
                                    @endif
                                </div>
                                <div class="content border-bottom">
                                    <div>
                                        @for ($i = 1; $i <= 5; $i++) @if ($i <=$review->rating)
                                            <i class='bx bxs-star'></i>
                                            @else
                                            <i class='bx bx-star'></i>
                                            @endif
                                            @endfor
                                    </div>
                                    {{$review->content ?? ''}}
                                </div>
                                <div class="time text-end">
                                    {{$review->pwd->created_at->format('d M Y H:i:s')}}
                                </div>
                            </div>
                            @empty
                            <div>No reviews available</div>
                            @endforelse
                        </div>
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