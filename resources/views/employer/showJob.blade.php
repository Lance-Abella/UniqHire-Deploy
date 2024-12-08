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
                    @include('slugs.applicantRequests')
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
                    {!! nl2br(e($listing->description)) !!}
                </div>
            </div>
            <ul class="nav nav-underline" role="tablist">
                <li class="nav-item">
                    <a class="nav-link active" data-bs-toggle="tab" href="#requirements" role="tab">Requirements</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-bs-toggle="tab" href="#employees" role="tab">Interviewees</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-bs-toggle="tab" href="#hired" role="tab">Employees</a>
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
                            </div>
                            <div class="more-info">
                                <h5>Salary</h5>
                                <p>{{ number_format($listing->salary, 0, '.', ',') . ' PHP' }}</p>
                            </div>
                            <div class="more-info">
                                <h5>Work Type</h5>
                                <p class="match-info">{{ $listing->type->name }}</p>
                            </div>
                        </div>
                        <div class="d-flex justify-content-start more-info mb-5">
                            <div class="more-info">
                                <h5>Work Setup</h5>
                                <p class="match-info">{{ $listing->setup->name }}</p>
                            </div>
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
                    <h5>Interviewees</h5>
                    @forelse ($interviewees as $interviewee)
                    <div class="user-card d-flex justify-content-between align-items-center py-3 px-3">
                        <div class="name">
                            <a href="{{ route('show-profile', $interviewee->application->user->id) }}">
                                {{ $interviewee->application->user->userInfo->name }}
                            </a>
                        </div>
                        <div class="status">
                            @if($interviewee->schedule != null)
                            {{ \Carbon\Carbon::parse(trim($interviewee->schedule))->format('F d, Y') }}
                            @else
                            No date scheduled
                            @endif
                        </div>
                        <div class="status">
                            @if($interviewee->schedule != null)
                            {{ \Carbon\Carbon::parse(trim($interviewee->start_time))->format('h:i A') . ' until ' . \Carbon\Carbon::parse(trim($interviewee->end_time))->format('h:i A') }}
                            @else
                            No time scheduled
                            @endif
                        </div>
                        <div class="set-sched">
                            <form action="{{route('set-schedule', $interviewee->id)}}" method="GET">
                                @csrf
                                <button class="submit-btn border-0">Set schedule</button>
                            </form>
                        </div>
                        <div class="btn-container">
                            <form action="{{ route('mark-hired') }}" method="POST">
                                @csrf
                                <input type="hidden" value="{{$interviewee->id}}" name="employeeId">
                                <input type="hidden" value="{{$interviewee->pwd_id}}" name="userId">
                                <input type="hidden" value="{{$interviewee->id}}" name="jobId">
                                @if ($interviewee->hiring_status == 'Pending')
                                <button class="submit-btn border-0">Hire</button>
                                @else
                                <button class="submit-btn completed border-0" disabled><i class='bx bx-check'></i></button>
                                @endif
                            </form>
                        </div>
                    </div>
                    @empty
                    <div class="user-card text-center py-3 px-3">
                        No Interviewees yet.
                    </div>
                    @endforelse
                </div>
                <div class="tab-pane enrollees" id="hired" role="tabpanel">
                    <h5>Employees</h5>
                    @forelse ($hiredPWDs as $employee)
                    <div class="user-card d-flex justify-content-between align-items-center py-3 px-3">
                        <div class="name">
                            <a href="{{ route('show-profile', $employee->application->user->id) }}">
                                {{ $employee->application->user->userInfo->name }}
                            </a>
                        </div>
                    </div>
                    @empty
                    <div class="user-card text-center py-3 px-3">
                        No Employees yet.
                    </div>
                    @endforelse
                </div>
            </div>
        </div>
        <div class="counts-container">
            <div class="counts">
                <h3>{{$pendingsCount}}</h3>
                <p>Pending Applicants</p>
            </div>
            <div class="counts">
                <h3>{{$intervieweeCount}}</h3>
                <p>Interviewees</p>
            </div>
            <div class="counts">
                <h3>{{$totalHired}}</h3>
                <p>Total Employees</p>
            </div>
        </div>
    </div>
</div>
@endsection

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
</script>