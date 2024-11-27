@extends ('layout')

@section('page-title', 'Find Work')

@section('page-content')

<div class="pwd-browse-prog pwd-browse-job mb-3">
    <div class="filter-container">
        <form action="{{ route('pwd-list-job') }}" method="GET" id="filterForm">
            <div class="d-flex justify-content-between mb-3">
                <h3>Filter</h3>
                <i class='bx bx-filter-alt fs-3 sub-text text-end'></i>
            </div>
            <div class="mb-3">
                <span>
                    <p>Work Setup</p>
                </span>
                @foreach($setups as $setup)
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" value="{{$setup->name}}" id="flexCheckChecked{{$loop->index}}" name="setup[]" onchange="submitForm()" {{ in_array($setup->name, request()->input('setup', [])) ? 'checked' : '' }}>
                    <label class="form-check-label" for="flexCheckChecked{{$loop->index}}">
                        {{$setup->name}} &nbsp;<span class="count sub-text">({{ $setupCounts[$setup->id]->job_count }})</span>
                    </label>
                </div>
                @endforeach
            </div>
            <div class="mb-3">
                <span>
                    <p>Work Type</p>
                </span>
                @foreach($types as $type)
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" value="{{$type->name}}" id="flexCheckChecked{{$loop->index}}" name="type[]" onchange="submitForm()" {{ in_array($type->name, request()->input('type', [])) ? 'checked' : '' }}>
                    <label class="form-check-label" for="flexCheckChecked{{$loop->index}}">
                        {{$type->name}} &nbsp;<span class="count sub-text">({{ $typeCounts[$type->id]->job_count }})</span>
                    </label>
                </div>
                @endforeach
            </div>
            <div class="mb-5">
                <span>
                    <p>Salary Range <span class="sub-text">(₱)</span></p>

                </span>
                <div class="input-group input-group-sm mb-3">
                    <span class="input-group-text" id="inputGroup-sizing-sm">Min</span>
                    <input type="number" class="form-control" name="minSalary" id="minSalaryInput" value="{{request()->minSalary ?? 0}}" oninput="updateRangeFromInput()" readonly>
                </div>
                <div class="input-group input-group-sm mb-3">
                    <span class="input-group-text" id="inputGroup-sizing-sm">Max</span>
                    <input type="number" class="form-control" name="maxSalary" id="maxSalaryInput" value="{{request()->maxSalary ?? 100000}}" oninput="updateRangeFromInput()" readonly>
                </div>
                <div class="slider">
                    <div class="progress">
                    </div>
                </div>
                <div class="range-input">
                    <input type="range" name="" class="min-range" min="0" max="100000" value="{{request()->minSalary ?? 0}}" step="500" id="minRange" oninput="rangeInput()">
                    <input type="range" name="" class="max-range" min="0" max="100000" value="{{request()->maxSalary ?? 100000}}" step="500" id="maxRange" oninput="rangeInput()">
                </div>
            </div>
            <div class="d-flex justify-content-center align-items-center">
                <button type="submit" class="submit-btn border-0">Apply Filters</button>
            </div>
        </form>
    </div>
    <div class="list">
        <div class="mb-4 searchbar-container">
            <div class="d-flex justify-content-center">
                <form role="search" action="{{ route('pwd-list-job') }}" method="GET" id="searchForm">
                    <div class="d-flex searchbar">
                        <input class="form-control" type="search" placeholder="Search Jobs" aria-label="Search" id="searchInput" onchange="checkAndSubmit()" name="search" value="{{ request('search') }}">
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

                    <div class="job-card mb-2" data-program-id="{{ $ranked['job']->id }}" data-lat="{{ $ranked['job']->latitude }}" data-lng="{{ $ranked['job']->longitude }}">
                        <input type="hidden" name="" value="{{$ranked['similarity']}}" id="">

                        <a href="{{ route('job-details', $ranked['job']->id) }}" class="d-flex prog-texts">
                            <div class="prog-texts-container">
                                <div class="d-flex mb-2">
                                    <div class="prog-img" @if (!empty($ranked['job']->employer->userInfo->profile_path)) style=" background-image: url({{ asset($ranked['job']->employer->userInfo->profile_path) }}); background-repeat: no-repeat; background-size: cover; " @endif>
                                        @if (empty($ranked['job']->employer->userInfo->profile_path))
                                        <span>{{ strtoupper(substr($ranked['job']->employer->userInfo->name, 0, 1)) }}</span>
                                        @endif
                                    </div>

                                    <div class="prog-head d-flex justify-content-between" style="width:16.5rem">
                                        <div class=" header" style="width:12rem">
                                            <h4 class="text-cap">{{$ranked['job']->position}}</h4>
                                            <p class="sub-text text-cap">{{$ranked['job']->employer->userInfo->name}}</p>
                                            <p class="sub-text text-cap location">
                                                <i class='bx bx-map sub-text'></i>{{$ranked['job']->location}}
                                            </p>
                                        </div>
                                        <div class="text-end date-posted">
                                            @php
                                            $diff = $ranked['job']->created_at->diffInSeconds(now());
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
                                <div class="row prog-desc mb-1">
                                    <p>{{$ranked['job']->description}}</p>
                                </div>
                                <div class="infos" style="width:23.2rem">
                                    <input type="hidden" id="user-disability" value="{{Auth::user()->userInfo->disability_id}}">
                                    @foreach ($ranked['job']->disability as $disability)
                                    <div class="disability-item" data-disability-id="{{ $disability->id }}">
                                        {{$disability->disability_name}}
                                    </div>
                                    @endforeach
                                    <div class="salary-info" data-salary="{{ $ranked['job']->salary }}" id="salary-value">
                                        {{number_format($ranked['job']->salary, 0, '.', ',') . ' PHP'}}
                                    </div>
                                    <div class="worksetup-item" data-worksetup="{{ $ranked['job']->setup->id }}" id="worksetup-id">
                                        {{ $ranked['job']->setup->name }}
                                    </div>
                                    <div class="worktype-item" data-worktype="{{ $ranked['job']->type->id }}" id="worktype-id">
                                        {{ $ranked['job']->type->name }}
                                    </div>
                                </div>
                            </div>
                        </a>
                    </div>
                    @endforeach
                </div>
                @endif
            </div>
            <div class=" pagination-container">

            </div>

        </div>
    </div>
</div>
<script>
    function rangeInput() {
        const minRange = document.getElementById("minRange");
        const maxRange = document.getElementById("maxRange");
        const minSalaryInput = document.getElementById("minSalaryInput");
        const maxSalaryInput = document.getElementById("maxSalaryInput");
        const progress = document.querySelector(".slider .progress");

        // Synchronize ranges and inputs
        if (parseInt(minRange.value) > parseInt(maxRange.value)) {
            minRange.value = maxRange.value;
        }
        if (parseInt(maxRange.value) < parseInt(minRange.value)) {
            maxRange.value = minRange.value;
        }

        minSalaryInput.value = minRange.value;
        maxSalaryInput.value = maxRange.value;

        // Update the progress bar
        const minPercent = (minRange.value / minRange.max) * 100;
        const maxPercent = (maxRange.value / maxRange.max) * 100;

        progress.style.left = `${minPercent}%`;
        progress.style.right = `${100 - maxPercent}%`;

        updateMatchInfo();
        // submitForm();
    }

    function updateRangeFromInput() {
        const minRange = document.getElementById("minRange");
        const maxRange = document.getElementById("maxRange");
        const minSalaryInput = document.getElementById("minSalaryInput");
        const maxSalaryInput = document.getElementById("maxSalaryInput");
        const progress = document.querySelector(".slider .progress");

        let minVal = parseInt(minSalaryInput.value) || 0;
        let maxVal = parseInt(maxSalaryInput.value) || 0;

        // Ensure inputs are within valid range
        minVal = Math.max(0, minVal);
        maxVal = Math.min(100000, maxVal);

        if (minVal > maxVal) minVal = maxVal;
        if (maxVal < minVal) maxVal = minVal;

        minRange.value = minVal;
        maxRange.value = maxVal;

        const minPercent = (minVal / minRange.max) * 100;
        const maxPercent = (maxVal / maxRange.max) * 100;

        progress.style.left = `${minPercent}%`;
        progress.style.right = `${100 - maxPercent}%`;

        updateMatchInfo();
    }

    // function submitForm() {
    //     document.getElementById("filterForm").submit();
    // }

    function updateMatchClass(elements, conditionFunc, matchClass, notMatchClass) {
        elements.forEach(element => {
            if (conditionFunc(element)) {
                element.classList.add(matchClass);
                element.classList.remove(notMatchClass);
            } else {
                element.classList.remove(matchClass);
                element.classList.add(notMatchClass);
            }
        });
    }

    // Function to update salary info based on the min and max salary
    function updateSalaryMatchInfo(minSalary, maxSalary) {
        const salaryElements = document.querySelectorAll(".salary-info");
        updateMatchClass(salaryElements, (element) => {
            const salary = parseInt(element.getAttribute("data-salary")) || 0;
            return salary >= minSalary && salary <= maxSalary;
        }, "match-info", "notmatch-info");
    }


    // Function to update disability items match-info
    function updateDisabilityMatchInfo(userDisabilityId) {
        const disabilityItems = document.querySelectorAll(".disability-item");
        updateMatchClass(disabilityItems, (item) => {
            const programDisabilityId = parseInt(item.getAttribute("data-disability-id"), 10);
            return programDisabilityId === userDisabilityId;
        }, "match-info", "notmatch-info");
    }

    // Function to update work setup match-info
    function updateWorkSetupMatchInfo(selectedSetups) {
        const workSetupItems = document.querySelectorAll(".worksetup-item");
        updateMatchClass(workSetupItems, (item) => {
            const jobSetup = item.textContent.trim();
            return selectedSetups.includes(jobSetup);
        }, "match-info", "notmatch-info");
    }

    // Function to update work type match-info
    function updateWorkTypeMatchInfo(selectedTypes) {
        const workTypeItems = document.querySelectorAll(".worktype-item");
        updateMatchClass(workTypeItems, (item) => {
            const jobType = item.textContent.trim();
            return selectedTypes.includes(jobType);
        }, "match-info", "notmatch-info");
    }

    // Main update function
    function updateMatchInfo() {
        const minSalary = parseInt(document.getElementById("minSalaryInput").value) || 0;
        const maxSalary = parseInt(document.getElementById("maxSalaryInput").value) || Infinity;

        updateSalaryMatchInfo(minSalary, maxSalary);

        const userDisabilityId = parseInt(document.getElementById("user-disability").value, 10);
        updateDisabilityMatchInfo(userDisabilityId);

        const selectedSetups = Array.from(document.querySelectorAll("input[name='setup[]']:checked")).map(input => input.value);
        updateWorkSetupMatchInfo(selectedSetups);

        const selectedTypes = Array.from(document.querySelectorAll("input[name='type[]']:checked")).map(input => input.value);
        updateWorkTypeMatchInfo(selectedTypes);
    }


    document.addEventListener("DOMContentLoaded", function() {
        // Initialize match-info on load
        rangeInput();
        updateMatchInfo();

        // Event listeners for salary inputs
        document.getElementById("minSalaryInput").addEventListener("input", updateRangeFromInput);
        document.getElementById("maxSalaryInput").addEventListener("input", updateRangeFromInput);
        document.getElementById("minRange").addEventListener("input", rangeInput);
        document.getElementById("maxRange").addEventListener("input", rangeInput);

        // Event listeners for filters
        addChangeListeners("input[name='setup[]']", updateMatchInfo);
        addChangeListeners("input[name='type[]']", updateMatchInfo);
    });
</script>
@endsection