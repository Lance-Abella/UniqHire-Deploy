@extends('layout')

@section('page-title', 'Job Details')

@section('page-content')
<div class="agency-show-prog">
    <div class="back-btn">
        @if (Route::currentRouteName() == 'job-details')
        <a href="{{ route('pwd-list-job') }}" class="m-1 back-link"><i class='bx bx-left-arrow-alt'></i></a>
        @elseif(Route::currentRouteName() == 'show-details' )
        <a href="{{ route('trainings') }}" class="m-1 back-link"><i class='bx bx-left-arrow-alt'></i></a>
        @endif
    </div>
    <div class="prog-details">
        <div class="header d-flex">
            <div class="mb-3 titles">
                <h3 class="text-cap">{{ $listing->position }}</h3>
                <p class="sub-text text-cap">{{ $listing->employer->userInfo->name }}</p>
                <p class="sub-text prog-loc text-cap" id="location"><i class='bx bx-map sub-text'></i>{{ $listing->location }}</p>
                <input type="hidden" id="lat" value="{{ $listing->latitude }}">
                <input type="hidden" id="lng" value="{{ $listing->longitude }}">
            </div>
            <div class="prog-btn">
                <form id="apply-form-{{ $listing->id }}" action="{{ route('pwd-jobApplication') }}" method="POST">
                    @csrf
                    <input type="hidden" name="user_id" value="{{ Auth::user()->id }}">
                    <input type="hidden" name="job_id" value="{{ $listing->id }}">

                    @php
                    // Determine the application status
                    $applicationStatus = null;
                    foreach ($applications as $app) {
                    if ($app->job_id == $listing->id) {
                    $applicationStatus = $app->application_status;
                    break;
                    }
                    }
                    @endphp

                    @if ($applicationStatus == 'Pending')
                    <button type="submit" class="submit-btn pending border-0" disabled>
                        Pending
                    </button>
                    @elseif($applicationStatus == 'Approved')
                    <button type="submit" class="submit-btn approved border-0" disabled>
                        <i class='bx bx-check'></i>
                    </button>
                    @else
                    <div class="d-flex flex-column align-items-end apply-btn-container">
                        <button type="submit" class="submit-btn border-0" onclick="confirmApplication(event, 'apply-form-{{ $listing->id }}')">
                                Apply
                        </button>                       
                    </div>
                    @endif
                </form>
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
                <a class="nav-link" data-bs-toggle="tab" href="#employees" role="tab">Hired PWDs</a>
            </li>       
        </ul>
        <div class="tab-content">
            <div class="tab-pane active" id="requirements" role="tabpanel">
                <div class="requirements">
                    <div class="d-flex justify-content-start mb-5">
                        <div class="more-info">
                            <h5>Hiring until</h5>
                            <p>
                                @foreach(explode(',', $listing->end_date) as $date)
                                {{ \Carbon\Carbon::parse(trim($date))->format('F d, Y') }}
                                @if(!$loop->last)
                            <p></p>
                            @endif
                            @endforeach
                            </p>
                        </div>
                         <div class="more-info">
                                <h5>Salary</h5>
                                <p>{{ $listing->salary . ' Pesos' }}</p>
                            </div> 
                    </div>
                    <div class="d-flex justify-content-start more-info mb-5">
                        <div class="more-info">
                            <h5>Work Type</h5>
                            <p>{{ $listing->type->name }}</p>
                        </div> 
                        <div class="more-info">
                            <h5>Work Setup</h5>
                            <p>{{ $listing->setup->name }}</p>
                        </div> 
                    </div>
                    <div class="d-flex justify-content-start mb-5">                       
                        <div class="more-info">
                            <h5>Skills Required</h5>
                            <ul>
                                @foreach ($listing->skill as $skill)
                                <li class="match-info mb-2">{{ $skill->title }}</li>
                                @endforeach
                            </ul>
                        </div>
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
            <div class="tab-pane enrollees" id="employees" role="tabpanel">
                    <table class="table table-striped table-hover">
                         <tbody>
                            @forelse ($hiredPWDs as $hired)
                                <tr>                                    
                                    <td class="name">
                                        <a href="{{ route('show-profile', $hired->application->user->id) }}">
                                            {{ $hired->application->user->userInfo->name }}
                                        </a>
                                    </td>                                    
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="text-center">No hired PWDs yet.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
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