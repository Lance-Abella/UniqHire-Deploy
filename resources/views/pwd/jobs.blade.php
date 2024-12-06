@extends('layout')

@section('page-title', 'Jobs')

@section('page-content')
<div class="trainings-container">
    <div class="tables">
        <div class="mb-3">
            <div class="text-start header-texts fs-2 back-link-container border-bottom">
                Tracker.
            </div>
        </div>
        <div class="table-container">
            <div class="applications">
                <div class="text-start header-texts fs-4 back-link-container border-bottom mb-3">
                    Job Applications.
                </div>
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <td class="table-head">Employer</td>
                            <td class="table-head">Position</td>
                            <td class="table-head">Salary</td>
                            <td class="table-head">Status</td>
                            <td class="table-head"></td>
                        </tr>
                    </thead>
                    <tbody class="table-group-divider text-center">
                        @forelse ($applications as $application)
                        <tr>
                            <td class="text-cap">{{$application->job->employer->userInfo->name}}</td>
                            <td>
                                {{$application->job->position}}
                            </td>
                            <td>
                                {{ number_format($application->job->salary, 0, '.', ',') . ' PHP' }}
                            </td>
                            <td class="status-cell">
                                <p class="match-info 
                                @if ($application->application_status == 'Pending')
                                pending
                                @endif
                                ">{{$application->application_status}}</p>
                            </td>
                            <td class="text-cap"><a href="{{ route('show-job-details', $application->job->id) }}">Show Details</a></td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center">No pending applications</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div>
                <div class="text-start header-texts fs-4 back-link-container border-bottom mb-3">
                    Jobs Interviews.
                </div>
                <div class="mb-3">
                    <form action="{{ route('jobs') }}" method="GET" id="filterForm">
                        <div class="d-flex align-items-center">
                            <label class="">Filter by:</label>
                            <select class="form-select" name="status" id="status" onchange="submitForm()">
                                <option value="all" {{ request('status') == 'all' ? 'selected' : '' }}>Show All</option>
                                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                                <option value="accepted" {{ request('status') == 'accepted' ? 'selected' : '' }}>Accepted</option>
                            </select>
                        </div>
                    </form>
                </div>
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <td class="table-head">Employer</td>
                            <td class="table-head">Position</td>
                            <td class="table-head">Salary</td>
                            <td class="table-head">Schedule</td>
                            <td class="table-head">Time</td>
                            <td class="table-head">Status</td>
                            <td class="table-head"></td>
                        </tr>
                    </thead>
                    <tbody class="table-group-divider text-center">
                        @forelse ($interviews as $interview)
                        <tr>
                            <td class="text-cap">{{$interview->job->employer->userInfo->name}}</td>
                            <td>
                                {{$interview->job->position}}
                            </td>
                            <td>
                                {{number_format($interview->job->salary, 0, '.', ',') . ' PHP'}}
                            </td>
                            <td>
                                {{ \Carbon\Carbon::parse($interview->schedule)->format('F d, Y') }}
                            </td>
                            <td>
                                {{ \Carbon\Carbon::parse($interview->start_time)->format('h:i A') }} - {{ \Carbon\Carbon::parse($interview->end_time)->format('h:i A') }}

                            </td>
                            <td class="status-cell">
                                <p class="match-info @if ($interview->hiring_status == 'Pending')
                                pending
                                @endif
                                ">{{$interview->hiring_status}}</p>
                            </td>
                            <td class="text-cap"><a href="{{ route('show-job-details', $interview->job->id) }}">Show Details</a></td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center">No job interviews</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="counts-container border-left">
        <div class="counts">
            <h3>{{$pendingsCount}}</h3>
            <p>Pendings</p>
        </div>
        <div class="counts">
            <h3>{{$interviewCount}}</h3>
            <p>Job Interviews</p>
        </div>
    </div>
</div>


@endsection

<script>
    function submitForm(status) {
        document.getElementById('filterForm').submit();
    }
</script>