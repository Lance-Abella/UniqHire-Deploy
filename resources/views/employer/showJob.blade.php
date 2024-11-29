@extends('layout')

@section('page-title', 'Job Details')

@section('page-content')
<div class="mb-5 agency-show-prog">
    <div class="back-btn">
        @if (Route::currentRouteName() == 'jobs-show')
        <a href="{{ route('manage-jobs') }}" class="m-1 back-link"><i class='bx bx-left-arrow-alt'></i></a>
        @endif
    </div>
    <div class="detailed-prog">
        <div class="prog-details">
            <div class="d-flex header">
                <div class="mb-3 titles">
                    <h3 class="text-cap">{{ $listing->position }}</h3>
                    <p class="sub-text text-cap">{{ $listing->employer->userInfo->name }}</p>
                    <p class="sub-text prog-loc text-cap" id="location"><i class='bx bx-map sub-text'></i>{{ $listing->location }}</p>
                    <input type="hidden" id="lat" value="{{ $listing->latitude }}">
                    <input type="hidden" id="lng" value="{{ $listing->longitude }}">
                </div>
                <div class="prog-btn">
                    @include('slugs.enrolleeRequests')
                    <div class="edit-delete">
                        <div class="">
                            <form action="{{ route('jobs-edit', $listing->id) }}" method="GET">
                                <button class="submit-btn border-0 edit-btn">Edit</button>
                            </form>
                        </div>
                        <div class="">
                            <form id="delete-form-{{ $listing->id }}" action="{{ route('jobs-delete', $listing->id) }}" method="POST">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="deny-btn border-0" onclick="confirmDeleteJob(event, 'delete-form-{{ $listing->id }}')">Delete</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <div class="mb-5">
                <div class="col">
                    {{ $listing->description }}
                </div>
            </div>
            <ul class="nav nav-underline" role="tablist">
                <li class="nav-item">
                    <a class="nav-link active" data-bs-toggle="tab" href="#requirements" role="tab">Requirements</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-bs-toggle="tab" href="#enrollees" role="tab">Enrollees</a>
                </li>
                @if ($listing->crowdfund)
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
                                <!-- <div style="height:5rem;overflow-y:scroll"> -->
                                <p>
                                    @foreach(explode(',', $listing->schedule) as $date)
                                    {{ \Carbon\Carbon::parse(trim($date))->format('F d, Y') }}
                                    @if(!$loop->last)
                                <p></p>
                                @endif
                                @endforeach
                                </p>
                                <!-- </div> -->

                            </div>
                            <div class="more-info">
                                <h5>Participants</h5>
                                <p>{{ number_format($listing->participants) . ' Persons' }}&nbsp;&nbsp; <span class="sub-text">({{$slots}} remaining)</span></p>
                            </div>
                        </div>
                        <!-- AGE -->
                        <div class="d-flex justify-content-start mb-5">
                            <div class="more-info">
                                <h5>Age</h5>
                                <p class="match-info">{{ $listing->start_age . ' - ' . $listing->end_age . ' Years Old' }}</p>
                            </div>
                            <div class="more-info">
                                <h5>Skills Acquired</h5>
                                <ul>
                                    @foreach ($listing->skill as $skill)
                                    <li class="match-info mb-2">{{ $skill->title }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                        <div class="d-flex justify-content-start more-info">
                            <div class="more-info">
                                <h5>We Accept</h5>
                                <ul>
                                    @foreach ($listing->disability as $disability)
                                    <li class="match-info mb-2">{{ $disability->disability_name }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="tab-pane enrollees" id="enrollees" role="tabpanel">
                    <table class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <td class="check"><input class="form-check-input" type="checkbox"></td>
                                <td class="name"></td>
                                <td class="d-flex justify-content-end btn-container"><button class="submit-btn border-0">Mark as Complete</button></td>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($enrollees as $enrollee)
                            <tr>
                                <td class="check"><input class="form-check-input" type="checkbox"></td>
                                <td class="name">
                                    <a href="{{ route('show-profile', $enrollee->application->user->id) }}">
                                        {{ $enrollee->application->user->userInfo->name }}
                                    </a>
                                </td>

                                <td class="d-flex justify-content-end btn-container">
                                    <form action="{{ route('mark-complete') }}" method="POST">
                                        @csrf
                                        <input type="hidden" value="{{$enrollee->id}}" name="enrolleeId">
                                        <input type="hidden" value="{{$enrollee->pwd_id}}" name="userId">
                                        <input type="hidden" value="{{$listing->id}}" name="programId">
                                        @if ($enrollee->completion_status == 'Ongoing')
                                        <button class="submit-btn border-0">Completed?</button>
                                        @else
                                        <button class="submit-btn completed border-0" disabled><i class='bx bx-check'></i></button>
                                        @endif
                                    </form>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="3" class="text-center">No enrollees yet.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if ($listing->crowdfund)
                <div class="tab-pane" id="sponsors" role="tabpanel">
                    <div class="crowdfund-progress mb-3">
                        <p class="sub-text">
                            Goal Amount: &nbsp;&nbsp;<span>{{number_format($listing->crowdfund->goal, 0, '.', ',') . ' PHP'}}</span>
                        </p>
                        <p class="sub-text">
                            Crowdfunding Progress:
                        </p>
                        <div class="progress">
                            <div class="progress-bar" role="progressbar" aria-valuenow="{{ $listing->crowdfund->progress }}" aria-valuemin="0" aria-valuemax="100">{{ $listing->crowdfund->progress }}%</div>
                        </div>
                    </div>

                    <h5>Sponsors</h5>
                    <span class=""></span>
                </div>
                @endif
                <div class="tab-pane" id="reviews" role="tabpanel">
                    <div class="border reviews">
                        <div class="header border-bottom d-flex justify-content-between align-items-center">
                            <h3>Reviews</h3>
                        </div>
                        <div class="outer">
                            <div class="review-grid">
                                @forelse($reviews as $review)
                                <div class="body-review border">
                                    <div class="owner border-bottom">
                                        {{$review->pwd->userInfo->name}}
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
        <div class="counts-container">
            <div class="counts">
                <h3>{{$pendingsCount}}</h3>
                <p>Pendings</p>
            </div>
            <div class="counts">
                <h3>{{$ongoingCount}}</h3>
                <p>Ongoing</p>
            </div>
            <div class="counts">
                <h3>{{$completedCount}}</h3>
                <p>Completed</p>
            </div>
            <div class="counts">
                <h3>{{$enrolleesCount}}</h3>
                <p>Total Enrollees</p>
            </div>
        </div>
    </div>
</div>

<script>
    function confirmDeleteJob(event, formId) {
        event.preventDefault();
        Swal.fire({
            title: "Confirmation",
            text: "Do you really want to delete this job listing?",
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

    // function initMap() {
    //     var lat = parseFloat(document.getElementById('lat').value);
    //     var lng = parseFloat(document.getElementById('lng').value);
    //     var latlng = {
    //         lat: lat,
    //         lng: lng
    //     };
    //     var geocoder = new google.maps.Geocoder();

    //     // Reverse geocode to get the address
    //     geocoder.geocode({
    //         location: latlng
    //     }, function(results, status) {
    //         var locationElement = document.getElementById('location');
    //         if (status === 'OK') {
    //             if (results[0]) {
    //                 var addressParts = results[0].formatted_address.split(',');
    //                 // Extract the city and country (assuming the city is at index 1 and the country at index 3)
    //                 var city = addressParts[1].trim(); // City (e.g., "Cebu City")
    //                 var country = addressParts[addressParts.length - 1].trim();
    //                 locationElement.innerHTML = "<i class='bx bx-map sub-text'></i> " + city + ", " + country;
    //             } else {
    //                 locationElement.innerHTML = "<i class='bx bx-map sub-text'></i> No address found";
    //             }
    //         } else {
    //             locationElement.innerHTML = "<i class='bx bx-map sub-text'></i> Geocoder failed: " + status;
    //         }
    //     });
    // }

    // // Initialize the map and geocoding
    // window.onload = initMap;
</script>

@endsection