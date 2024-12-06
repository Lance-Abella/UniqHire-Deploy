<form action="{{ route('edit-profile') }}" method="POST" enctype="multipart/form-data">
    @csrf
    @method('PUT')
    <div>
        <button type="button" class="submit-btn border-0" style="width:8rem;" data-bs-toggle="modal" data-bs-target="#staticBackdrop">Edit Profile</button>
        <div class="modal fade" id="staticBackdrop" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-lg">

                <div class="modal-content">
                    <div class="modal-header">
                        <h1 class="modal-title fs-5" id="staticBackdropLabel">Edit Profile</h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="text-danger">
                            @if (!empty($user->userInfo->profile_path))
                            Current file: {{ basename($user->userInfo->profile_path) }}
                            @endif
                        </div>
                        <div class="input-group mb-3">
                            <input type="file" name="profile_picture" class="form-control" id="choose-file" aria-describedby="inputGroupFileAddon04" aria-label="Upload">
                            <button class="btn btn-outline-secondary" type="button" id="inputGroupFileAddon04" onclick="clearFileInput('choose-file')">Remove</button>
                        </div>

                        <div class="form-floating mb-3">
                            <input type="text" class="form-control" id="floatingInput" name="name" placeholder="Name" value="{{ $user->userInfo->name }}">
                            <label for="floatingInput">Name</label>
                            @error('name')
                            <span class="error-msg">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="row">
                            <div class="col">
                                <div class="form-floating mb-3">
                                    <input type="email" class="form-control" id="floatingInput" name="email" placeholder="Email" value="{{ $user->email }}" disabled>
                                    <label for="floatingInput">Email Address</label>
                                    @error('email')
                                    <span class="error-msg">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col">
                                <div class="form-floating mb-3">
                                    <input type="text" class="form-control" id="floatingInput" name="contactnumber" placeholder="Contact Number" value="{{ $user->userInfo->contactnumber }}">
                                    <label for="floatingInput">Contact Number</label>
                                    @error('contactnumber')
                                    <span class="error-msg">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            @if($user->hasRole('PWD'))
                            <div class="col">
                                <div class="form-floating mb-3">
                                    <input type="number" class="form-control" id="floatingInput" name="age" placeholder="Age" value="{{ $user->userInfo->age}}">
                                    <label for="floatingInput">Age</label>
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
                                        <option value="{{ $level->id }}" @if ($user->userInfo->educational_id == $level->id ) selected @endif>{{ $level->education_name }}</option>
                                        @endif
                                        @endforeach
                                    </select>
                                    <label for="education-level">Education Level</label>
                                    @error('education')
                                    <span class="error-msg">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            @elseif ($user->hasRole('Training Agency') || $user->hasRole('Sponsor') || $user->hasRole('Employer'))
                            <div class="col">
                                <div class="form-floating mb-3">
                                    <input type="text" class="form-control" id="floatingInput" name="founder" value="{{ $user->userInfo->founder }}" placeholder="Founder">
                                    <label for="floatingInput">Founder</label>
                                    @error('founder')
                                    <span class="error-msg">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col">
                                <div class="form-floating mb-3">
                                    <input type="number" class="form-control" id="year-established" name="year_established" value="{{ $user->userInfo->year_established }}" min="1000" max="">
                                    <label for="year-established">Year Established</label>
                                    @error('year-established')
                                    <span class="error-msg">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col">
                                <div class="form-floating mb-3">
                                    <input type="text" class="form-control" id="paypal" name="paypal" value="{{ $user->userInfo->paypal_account }}">
                                    <label for="paypal">PayPal Account</label>
                                    @error('paypal')
                                    <span class=" error-msg">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            @endif
                        </div>
                        <div class="col mb-3">
                            <input type="hidden" id="lat" name="lat" value="{{ $latitude }}">
                            <input type="hidden" id="long" name="long" value="{{ $longitude }}">
                            <input type="hidden" id="loc" name="loc" required>
                            <input id="pac-input" class="controls" type="text" placeholder="Search Box">
                            <div id="map" class="map"></div>
                        </div>
                        @if($user->hasRole('PWD'))
                        <div class="form-floating mb-3">
                            <select class="form-select" id="floatingSelect" name="disability" aria-label="Floating label select example">
                                @foreach ($disabilities as $disability)
                                @if ($disability->disability_name != 'Not Applicable')
                                <option value="{{ $disability->id }}" @if ($user->userInfo->disability_id == $disability->id ) selected @endif >{{ $disability->disability_name }}</option>
                                @endif
                                @endforeach
                            </select>
                            <label for="floatingSelect">Disability</label>
                            @error('disability')
                            <span class="error-msg">{{ $message }}</span>
                            @enderror
                        </div>
                        @endif
                        <hr>
                        <div class="form-floating mb-3">
                            <textarea class="form-control" placeholder="About" id="floatingTextarea2" name="about" style="height: 200px">{{ $user->userInfo->about }}</textarea>
                            <label for="floatingTextarea2">About</label>
                            @error('about')
                            <span class="error-msg">{{ $message }}</span>
                            @enderror
                        </div>
                        @if($user->hasRole('Training Agency'))
                        <hr>
                        <div class="form-floating mb-3">
                            <textarea class="form-control" placeholder="About" id="floatingTextarea2" name="awards" style="height: 100px">{{ $user->userInfo->awards }}</textarea>
                            <label for="floatingTextarea2">Awards</label>
                            @error('awards')
                            <span class="error-msg">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="form-floating mb-3">
                            <textarea class="form-control" placeholder="About" id="floatingTextarea2" name="affiliations" style="height: 100px">{{ $user->userInfo->awards }}</textarea>
                            <label for="floatingTextarea2">Affiliations</label>
                            @error('affiliations')
                            <span class="error-msg">{{ $message }}</span>
                            @enderror
                        </div>
                        @endif
                        <hr>
                        <div class="form-floating mb-3">
                            <div id="socialListContainer">
                                <label for="socialList">Socials</label>
                                <div id="socialList">
                                    @foreach ($userSocials as $userSocial)
                                    <div class="input-group mb-3 social-item">
                                        <select class="form-select" name="socials[]">
                                            <option value="">Select Social</option>
                                            @foreach ($socials as $social)
                                            <option value="{{ $social->id }}"
                                                {{ $userSocial->social_id == $social->id ? 'selected' : '' }}>
                                                {{ $social->name }}
                                            </option>
                                            @endforeach
                                        </select>
                                        <input type="text" class="form-control" name="social_links[]" value="{{ $userSocial->link }}" placeholder="Social Link" style="width:13rem;">
                                        <button type=" button" class="btn btn-outline-danger remove-social" data-index="{{ $loop->index }}">Remove</button>
                                    </div>
                                    @endforeach
                                </div>
                                <button type="button" id="addSocialBtn" class="submit-btn border-0 add-social">
                                    <i class="bx bx-plus"></i> Add Social
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="reset" class="deny-btn border-0">Clear</button>
                        <button type="submit" class="border-0 submit-btn">Save Changes</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>

@push('map-scripts')
<script src="{{ asset('js/editInitMap.js') }}"></script>
<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyA4IdhyGOY2rDNFymY1kGR3qaS6K4RlWEY&libraries=places&loading=async&callback=editInitMap" asynch defer></script>
@endpush

<script>
    function clearFileInput(id) {
        var input = document.getElementById(id);
        input.value = '';
    }

    let socialCount = document.querySelectorAll('.social-item').length; // Existing socials count
    const maxSocials = 4; // Max number of socials a user can add

    const socialList = document.getElementById('socialList');
    const addSocialBtn = document.getElementById('addSocialBtn');

    // Add social event listener
    addSocialBtn.addEventListener('click', function() {
        if (socialCount < maxSocials) {
            socialCount++;
            const socialItem = document.createElement('div');
            socialItem.className = 'input-group mb-3 social-item';
            socialItem.innerHTML = `
            <select class="form-select" name="socials[]" required>
                <option value="" disabled selected>Select a social</option>
                @foreach ($socials as $social)
                <option value="{{ $social->id }}">{{ $social->name }}</option>
                @endforeach
            </select>
            <input type="url" class="form-control" name="social_links[]" placeholder="Enter profile link" required style="width:13rem;">
            <button type="button" class="btn btn-outline-danger remove-social">Remove</button>
        `;
            socialList.appendChild(socialItem);
            toggleAddButton();
        }
    });

    // Remove social using event delegation
    socialList.addEventListener('click', function(e) {
        if (e.target.classList.contains('remove-social')) {
            const socialItem = e.target.closest('.social-item');
            if (socialItem) {
                socialList.removeChild(socialItem);
                socialCount--;
                toggleAddButton();
            }
        }
    });

    // Toggle add button
    function toggleAddButton() {
        addSocialBtn.disabled = socialCount >= maxSocials;
    }
</script>