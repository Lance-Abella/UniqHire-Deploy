@extends('layout')
@section('page-title', 'Add Training Program')
@section('page-content')
<form action="{{ route('programs-add') }}" method="POST" class="container mb-5 add-form">
    @csrf
    <div class="row mt-2 mb-2 border-bottom">
        <div class="text-start header-texts back-link-container">
            <a href="{{ route('programs-manage') }}" class="m-1 back-link"><i class='bx bx-left-arrow-alt'></i></a>
            Add Training Program.
        </div>
    </div>
    <div class="row">
        <div class="col">
            <div class="form-floating mb-3">
                <input type="text" class="form-control" id="floatingInput" name="title" value="{{old('title')}}" required placeholder="First Name">
                <label for="floatingInput">Title</label>
                @error('title')
                <span class="error-msg">{{ $message }}</span>
                @enderror
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col">
            <div class="form-floating mb-3">
                <input type="hidden" id="lat" name="lat" required>
                <input type="hidden" id="long" name="long" required>
                <input type="hidden" id="loc" name="loc" required>
                <input id="pac-input" class="controls" type="text" placeholder="Search Box">
                <div id="map" class="map"></div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col">
            <div class="form-floating mb-3">
                <textarea class="form-control" placeholder="Description" id="floatingTextarea2" name="description" style="height: 200px">{{old('description')}}</textarea>
                <label for="floatingTextarea2">Description</label>
                @error('description')
                <span class="error-msg">{{ $message }}</span>
                @enderror
            </div>
        </div>
    </div>
    <div class="row mb-3">
        <div class="col">
            <div class="row">
                <div class="col">
                    <div class="form-floating mb-3">
                        <input type="number" class="form-control" id="startAge" name="start_age" value="{{old('start_age')}}" required placeholder="Input Age">
                        <label for="floatingInput">Age Range (from)</label>
                        @error('start_age')
                        <span class="error-msg">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
                <div class="col">
                    <div class="form-floating mb-3">
                        <input type="number" class="form-control" id="endAge" name="end_age" value="{{old('end_age')}}" required placeholder="Input Age">
                        <label for="floatingInput">Age Range (to)</label>
                        @error('end_age')
                        <span class="error-msg">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="form-floating mb-3">
                <input type="text" class="form-control date" name="schedule" required placeholder="Choose Date" value="{{old('schedule')}}">
                <label for="floatingInput">Choose Date (Max. of 16 days only)</label>
                @error('schedule')
                <span class="error-msg">{{ $message }}</span>
                @enderror
            </div>
        </div>
    </div>
    <div class="row mb-3">
        <div class="col">
            <div class="row">
                <div class="col">
                    <div class="form-floating mb-3">
                        <input type="time" class="form-control" id="startTime" name="start_time" value="{{old('start_time')}}" required placeholder="Input Start Time">
                        <label for="floatingInput">Time Start</label>
                        @error('start_time')
                        <span class="error-msg">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
                <div class="col">
                    <div class="form-floating mb-3">
                        <input type="time" class="form-control" id="endTime" name="end_time" value="{{old('end_time')}}" required placeholder="Input Start Time">
                        <label for="floatingInput">Time End</label>
                        @error('end_time')
                        <span class="error-msg">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row mb-3">
        <div class="col">
            <h5>Select Disabilities</h5>
            <div class="req-container">
                @foreach ($disabilities as $disability)
                @if ($disability->disability_name != 'Not Applicable')
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" value="{{$disability->id}}" id="disability{{$loop->index}}" name="disabilities[]" @if (old('disabilities') && in_array($disability->id, old('disabilities')))
                    checked @endif>
                    <label class="form-check-label" for="disability{{$loop->index}}">
                        {{$disability->disability_name}}
                    </label>
                </div>
                @endif
                @endforeach
            </div>
        </div>
        <div class="col">
            <h5>Select Skills</h5>
            <div class="req-container">
                @foreach ($skills as $skill)
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" value="{{$skill->id}}" id="skill{{$loop->index}}" name="skills[]" @if (old('skills') && in_array($skill->id, old('skills')))
                    checked @endif>
                    <label class="form-check-label" for="skill{{$loop->index}}">
                        {{$skill->title}}
                    </label>
                </div>
                @endforeach
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col">
            <div class="form-floating mb-3">
                <input type="text" class="form-control" id="participants" name="participants" value="{{old('participants')}}" required placeholder="Input Participants" oninput="formatNumber(this)">
                <label for="floatingInput">Number of Participants</label>
                @error('participants')
                <span class="error-msg">{{ $message }}</span>
                @enderror
            </div>
        </div>
        <div class="col">
            <div class="form-floating mb-3">
                <select class="form-select" id="floatingSelect" name="education" aria-label="Floating label select example">
                    @foreach ($levels as $level)
                    @if ($level->education_name != 'Not Applicable')
                    <option value="{{ $level->id }}">{{ $level->education_name }}</option>
                    @endif
                    @endforeach

                </select>
                <label for="floatingSelect">Education Level (at least)</label>
            </div>
        </div>
    </div>
    <hr>
    @if (Auth::user()->userInfo->paypal_account == '' || Auth::user()->userInfo->paypal_account == null)
    <div class="mb-2"><span class="error-msg">** Need to have paypal email in profile</span></div>
    @endif

    <div>
        <div class="form-check">
            <input class="form-check-input" type="checkbox" value="" id="host-crowdfund" onchange="toggleCrowdfund()" @if (Auth::user()->userInfo->paypal_account == '' || Auth::user()->userInfo->paypal_account == null)
            disabled
            @endif>
            <label class="form-check-label" for="flexCheckDefault">
                Host a crowdfunding for this?
            </label>
        </div>
    </div>
    <div class="row" id="crowdfund-section">
        <div class="col">
            <div class="form-floating mb-3">
                <input type="text" class="form-control" id="amount-needed" name="goal" required placeholder="Amount Needed" disabled oninput="formatNumber(this)">
                <label for="floatingInput">Amount Needed</label>
                @error('goal')
                <span class="error-msg">{{ $message }}</span>
                @enderror
            </div>
        </div>
    </div>
    <!-- COMPETENCY -->
    <div class="row border-bottom">
        <div class="col">
            <div class="form-floating mb-3">
                <div id="competencyListContainer">
                    <label for="competencyList">Competencies:</label>
                    <div id="competencyList"></div>
                    <button type="button" id="addCompetencyBtn" class="submit-btn border-0 add-comp"><i class="bx bx-plus"></i> Add Competency</button>
                </div>
            </div>
        </div>
    </div>
    <div class="d-flex justify-content-evenly mt-3 prog-btn">
        <button type="reset" class="deny-btn border-0">Clear</button>
        <button type="submit" class="submit-btn border-0">Add</button>
    </div>
</form>


@endsection

@push('map-scripts')
<script async defer src="https://maps.googleapis.com/maps/api/js?key=AIzaSyA4IdhyGOY2rDNFymY1kGR3qaS6K4RlWEY&libraries=places&loading=async&callback=initMap"></script>
<script src="{{ asset('js/initMap.js') }}"></script>
@endpush

<script>
    function formatNumber(input) {
        let value = input.value.replace(/,/g, '');
        if (!isNaN(value) && value !== '') {
            input.value = Number(value).toLocaleString();
        }
    }

    function toggleCrowdfund() {
        var hostCrowdfund = document.getElementById('host-crowdfund');
        var crowdfundSection = document.getElementById('crowdfund-section');

        if (hostCrowdfund.checked) {
            // crowdfundSection.style.display = 'block';
            document.getElementById('amount-needed').disabled = false;
            document.getElementById('amount-needed').required = true;
        } else {
            // crowdfundSection.style.display = 'none';
            document.getElementById('amount-needed').disabled = true;
            document.getElementById('amount-needed').required = false;
        }
    }

    function sortAndFormatDates(dateInput) {
        let dates = dateInput.val().split(',');

        // Parse and sort the dates
        dates = dates.map(date => new Date(date.trim()));
        dates.sort((a, b) => a - b);

        // Limit to 16 dates
        if (dates.length > 16) {
            dates = dates.slice(0, 16);
        }

        // Format the dates back to the desired format (mm/dd/yyyy)
        const sortedDates = dates.map(date =>
            ('0' + (date.getMonth() + 1)).slice(-2) + '/' +
            ('0' + date.getDate()).slice(-2) + '/' +
            date.getFullYear()
        );

        dateInput.val(sortedDates.join(','));
    }

    document.addEventListener('DOMContentLoaded', function() {
        // Get the old input value if it exists
        const oldSchedule = '{{old("schedule")}}';

        // Initialize the datepicker
        $('.date').datepicker({
            multidate: 16,
            multidateSeparator: ',',
            todayHighlight: true,
            format: 'mm/dd/yyyy'
        }).on('changeDate', function(e) {
            sortAndFormatDates($(this));
        });

        if (oldSchedule) {
            // Split the old dates string and set them in the datepicker
            const dates = oldSchedule.split(',').map(date => date.trim());
            $('.date').datepicker('setDates', dates);
        }

        // Trigger sorting when the input field loses focus
        $('.date').on('blur', function() {
            sortAndFormatDates($(this));
        });

        let competencyCount = 0;
        const addCompetencyBtn = document.getElementById('addCompetencyBtn');
        const competencyList = document.getElementById('competencyList');

        function toggleButtons() {
            if (competencyCount >= 4) {
                addCompetencyBtn.classList.add('d-none');
            } else {
                addCompetencyBtn.classList.remove('d-none');
            }
        }

        addCompetencyBtn.addEventListener('click', function() {
            if (competencyCount < 4) {
                competencyCount++;
                const competencyItem = document.createElement('div');
                competencyItem.className = 'input-group mb-3';
                competencyItem.innerHTML = `
                <input type="text" class="form-control" placeholder="Enter competency" name="competencies[]" required>
                <button class="btn btn-outline-secondary remove-btn" type="button">Remove</button>
            `;
                competencyList.appendChild(competencyItem);

                competencyItem.querySelector('.remove-btn').addEventListener('click', function() {
                    competencyList.removeChild(competencyItem);
                    competencyCount--;
                    toggleButtons();
                });

                competencyItem.querySelector('input').addEventListener('input', toggleButtons);

                toggleButtons();
            }
        });

        toggleButtons(); // Initialize the button states
    });
</script>