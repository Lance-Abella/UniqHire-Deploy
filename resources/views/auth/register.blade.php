@extends('layout')

@section('page-title', 'Create Account')

@section('auth-content')
<div class="container register-container vh-100 mb-4 pb-4">

    <form method="POST" action="{{ route('register-form') }}" enctype="multipart/form-data">
        <div class="row" style="padding-top:3rem;">
            <div class="col">
                <div class="text-start header-texts back-link-container">
                    <a href="{{ route('login-page') }}" class="m-1 back-link"><i class='bx bx-left-arrow-alt'></i></a>
                    Create an Account.
                </div>
            </div>
            <!-- <div class="col-2">
            <div class="text-center">
                <img src="../images/logo.png" alt="UniqHire Logo" style="height: 3.7rem;">
            </div>
        </div> -->
            <div class="col ">
                <div class="row">
                    <div class="col d-flex align-items-center justify-content-end">
                        <label for="registerAs">Register As:</label>
                    </div>
                    <div class="col">
                        <select class="form-select" name="role[]" id="role" aria-label="Small select example" onchange="togglePWDSection()">
                            @foreach ($roles as $role)
                            @if ($role->role_name !== 'Admin')
                            <option value="{{ $role->id }}">{{ $role->role_name }}</option>
                            @endif
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
            <hr class="mb-4">
            <div>
                @csrf
                <div class="row">
                    <div class="col">
                        <div class="form-floating mb-3">
                            <input type="text" class="form-control" id="name" name="name" value="{{ old('name') }}" required placeholder="Name">
                            <label for="name">Name</label>
                            @error('name')
                            <span class="error-msg">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col">
                        <div class="form-floating mb-3">
                            <input type="email" class="form-control" id="email" name="email" placeholder="Email Address">
                            <label for="email">Email Address</label>
                            @error('email')
                            <span class="error-msg">{{ $message }}</span>
                            @enderror
                        </div>
                        <!-- <div class="col">
                            <input type="checkbox" name="generate_email" id="generate_email" class="form-check-input border border-dark">
                            <label for="generate_email" class="form-check-label">Generate Email Address</label>
                        </div> -->
                    </div>
                    <div class="col">
                        <div class="form-floating mb-3">
                            <input type="text" class="form-control" id="contactnumber" name="contactnumber" value="{{ old('contactnumber') }}" required placeholder="Contact Number" pattern="\d{11}" minlength="11" maxlength="11">
                            <label for="contactnumber">Contact Number</label>
                            @error('contactnumber')
                            <span class="error-msg">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col">
                        <div class="form-floating mb-3">
                            <input type="password" class="form-control" id="floatingPassword" name="password" required placeholder="Password">
                            <label for="floatingPassword">Password</label>
                            @error('password')
                            <span class="error-msg">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                    <div class="col">
                        <div class="form-floating mb-3">
                            <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" required placeholder="Confirm Password">
                            <label for="password_confirmation">Confirm Password</label>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col">
                        <input type="hidden" id="lat" name="lat" required>
                        <input type="hidden" id="long" name="long" required>
                        <input type="hidden" id="loc" name="loc" required>
                        <input id="pac-input" class="controls" type="text" placeholder="Search Box">                        
                        <div id="map" class="map"></div>
                    </div>
                </div>                
                <hr>
                <div id="pwd-section">
                    <div class="row">
                        <div class="col">
                            <div class="form-floating mb-3">
                                <input type="number" class="form-control" id="age" name="age" placeholder="Age">
                                <label for="age">Age</label>
                                @error('age')
                                <span class="error-msg">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="col">
                            <div class="form-floating mb-3">
                                <select class="form-select" id="education-level" name="education" aria-label="Floating label select example">
                                    @foreach ($levels as $level)
                                    @if ($level->id != '1')
                                    <option value="{{ $level->id }}">{{ $level->education_name }}</option>
                                    @endif
                                    @endforeach
                                </select>
                                <label for="education-level">Education Level</label>
                            </div>
                        </div>
                        <div class="col">
                            <div class="form-floating mb-3">
                                <select class="form-select" id="disabilities" name="disability" aria-label="Floating label select example">
                                    @foreach ($disabilities as $disability)
                                    @if ($disability->id != '1')
                                    <option value="{{ $disability->id }}">{{ $disability->disability_name }}</option>
                                    @endif
                                    @endforeach

                                </select>
                                <label for="disabilities">Disability</label>
                            </div>
                        </div>
                    </div>
                    <!-- <div class="row mb-4">
                        <div class="col">
                            PWD Card
                            <div class="input-group mb-3">
                                <input type="file" name="pwd_card" class="form-control" id="choose-file" aria-describedby="inputGroupFileAddon04" aria-label="Upload">
                                <button class="btn btn-outline-secondary" type="button" id="inputGroupFileAddon04" onclick="clearFileInput('choose-file')">Remove</button>
                            </div>
                        </div>
                    </div> -->
                    <div class="row">
                    <div class="col">
                        <div class="form-floating mb-3">
                            <input type="text" class="form-control" id="pwd_id" name="pwd_id" value="{{ old('pwd_id') }}" required placeholder="PWD ID Number">
                            <label for="name">PWD ID Number</label>
                            @error('pwd_id')
                            <span class="error-msg">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                </div>
                </div>
                <div id="agency-section" style="display:none;">
                    <div class="row">
                        <div class="col">
                            <div class="form-floating mb-3">
                                <input type="text" class="form-control" id="founder" name="founder" value="{{ old('name') }}" placeholder="Founder">
                                <label for="founder">Founder</label>
                                @error('founder')
                                <span class="error-msg">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="col">
                            <div class="form-floating mb-3">
                                <input type="number" class="form-control" id="year_established" name="year_established" placeholder="Year Established">
                                <label for="year_established">Year Established</label>
                                @error('year_established')
                                <span class="error-msg">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>
                <div class="d-flex justify-content-center align-items-center mb-3">
                    <button type="reset" class="m-2 border-0 bg-transparent deny-btn">
                        Clear
                    </button>
                    <button type="submit" class="m-2 border-0 bg-text submit-btn">
                        Register
                    </button>
                </div>
                <div class="text-center mb-3 pb-4">
                    <hr class="mb-4" style="width: 30rem; margin:0 auto;">
                    <span>
                        Already have an account? <a href="{{ route('login-page') }}" class="link-underline link-underline-opacity-0 highlight-link">Login.</a>
                    </span>
                </div>
            </div>

        </div>
    </form>
</div>

@endsection

@push('map-scripts')
<script async defer src="https://maps.googleapis.com/maps/api/js?key=AIzaSyA4IdhyGOY2rDNFymY1kGR3qaS6K4RlWEY&libraries=places&loading=async&callback=initMap"></script>
<script src="{{ asset('js/initMap.js') }}"></script>
@endpush

<script>
    function clearFileInput(id) {
        var input = document.getElementById(id);
        input.value = '';
    }

    function togglePWDSection() {
        var roleSelect = document.getElementById('role');
        var pwdSection = document.getElementById('pwd-section');
        var disabilitySelect = document.getElementById('disabilities');
        var agencySection = document.getElementById('agency-section');
        var educationSelect = document.getElementById('education-level');

        if (roleSelect.value === '2') {
            pwdSection.style.display = 'block';
            agencySection.style.display = 'none';

            removeOption(disabilitySelect, '1');
            removeOption(educationSelect, '1');
        } else {
            pwdSection.style.display = 'none';
            addNotApplicableOption(disabilitySelect);
            addNotApplicableOption(educationSelect);
            disabilitySelect.value = '1';
            educationSelect.value = '1';
        }

        if (roleSelect.value === '3' || roleSelect.value === '4') {
            agencySection.style.display = 'block';
        } else {
            agencySection.style.display = 'none';
        }
    }

    function removeOption(selectElement, value) {
        for (var i = 0; i < selectElement.options.length; i++) {
            if (selectElement.options[i].value === value) {
                selectElement.remove(i);
                break;
            }
        }
    }

    function addNotApplicableOption(selectElement) {
        var optionExists = false;
        for (var i = 0; i < selectElement.options.length; i++) {
            if (selectElement.options[i].value === '1') {
                optionExists = true;
                break;
            }
        }

        if (!optionExists) {
            var notApplicableOption = document.createElement('option');
            notApplicableOption.value = '1';
            notApplicableOption.text = 'Not Applicable';
            selectElement.add(notApplicableOption);
        }
    }
</script>