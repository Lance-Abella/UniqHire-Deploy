@extends('layout')

@section('page-title', 'Profile')
@section('page-content')
<div class="profile-container container">
    <div class="profile-info mb-4">
        <div class="profile-pic" @if (!empty($user->userInfo->profile_path)) style=" background-image: url({{ asset($user->userInfo->profile_path) }}); background-repeat: no-repeat; background-size: cover; " @endif>
            @if (!empty($user->userInfo->profile_path))
            <form id="remove-pic" action="{{route('remove-pic')}}" method="POST" class="d-flex justify-content-center">
                @csrf
                <button type="submit" class="deny-btn border-0" onclick="confirmRemoveProfile(event, 'remove-pic')"><i class='bx bx-trash'></i></button>
            </form>
            @else
            <span>{{ strtoupper(substr($user->userInfo->name, 0, 1)) }}</span>
            @endif

        </div>
        <div class="d-flex justify-content-between header">
            <div class="details ">
                <div class="">
                    <p class="text-cap profile-name">{{ $user->userInfo->name }}</p>
                    <p class="text-cap"><i class='bx bx-map sub-text'></i>{{$user->userInfo->location}}</p>
                    <input type="hidden" id="lat" value="{{ $latitude }}">
                    <input type="hidden" id="lng" value="{{ $longitude }}">
                </div>
                @if($user->hasRole('PWD'))
                <div class="">
                    <p class="text-cap age"><strong>Age:</strong>
                        @if ($user->userInfo->age != 0)
                        {{ $user->userInfo->age }} years old
                        @else
                        <span class="about sub-text">No data yet</span>
                        @endif

                    </p>
                    <p class="text-cap"> <strong>Disability:</strong>&nbsp;&nbsp;&nbsp;<span class="match-info">{{ $user->userInfo->disability->disability_name }}</span></p>
                </div>
                @elseif($user->hasRole('Training Agency') || $user->hasRole('Sponsor') || $user->hasRole('Employer'))
                <div class="">
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
            @include('slugs.editProfile')
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
                <div class="contact-item ">
                    <span class="d-flex align-items-center sub-text"><i class='bx bxl-paypal side-icon'></i> Paypal</span>
                    @if ($user->userInfo->paypal_account != '' || $user->userInfo->paypal_account != null)
                    <p>{{ $user->userInfo->paypal_account }}</p>
                    @else
                    <p class="sub-text">No paypal account</p>
                    @endif
                </div>
                <div class="contact-item">
                    <span class="d-flex align-items-center sub-text"><i class='bx bx-envelope side-icon'></i> Contact no</span>
                    <p><a href="">{{ $user->userInfo->contactnumber }}</a></p>
                </div>

                @foreach ($userSocials as $userSocial)
                @php
                $socialName = strtolower($userSocial->social->name);
                $iconClass = ($socialName == 'website') ? 'bx bx-globe' : "bx bxl-$socialName";
                @endphp
                <div class="contact-item">
                    <span class="d-flex align-items-center sub-text">
                        <i class="{{ $iconClass }} side-icon"></i>
                        {{ $userSocial->social->name }}
                    </span>
                    <p><a href="{{ $userSocial->link }}" target="_blank">{{ $userSocial->link }}</a></p>
                </div>
                @endforeach

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
            @if ($user->hasRole('PWD'))
            <div class="bio-item exp">
                <div>
                    @include('slugs.editSkills')
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
                    @include('slugs.editExperiences')
                </div>
            </div>
            @elseif($user->hasRole('Training Agency'))
            <div class="bio-item exp">
                <div>
                    <h4 class="mb-3">Awards & Recognitions</h4>
                    @if ($user->userInfo->awards != null)
                    <p>{!! nl2br(e($user->userInfo->awards)) !!}</p>
                    @else
                    <p class="about sub-text">No data yet</p>
                    @endif
                </div>
                <div>
                    <h4 class="mb-3">Affiliations</h4>
                    @if ($user->userInfo->affiliations != null)
                    <p>{!! nl2br(e($user->userInfo->affiliations)) !!}</p>
                    @else
                    <p class="about sub-text">No data yet</p>
                    @endif
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Set max year for the year established input
        var yearEstablishedInput = document.getElementById('year-established');
        var currentYear = new Date().getFullYear();
        yearEstablishedInput.max = currentYear;
    });

    function confirmRemoveProfile(event, formId) {
        event.preventDefault();
        Swal.fire({
            title: "Confirmation",
            text: "Do you really want to remove your profile picture?",
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
</script>