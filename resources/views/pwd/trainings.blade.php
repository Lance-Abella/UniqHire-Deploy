@extends('layout')

@section('page-title', 'Trainings')

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
                    Applications.
                </div>
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <td class="table-head">Title</td>
                            <td class="table-head">Agency</td>
                            <td class="table-head">Schedule</td>
                            <td class="table-head">Time</td>
                            <td class="table-head">Status</td>
                            <td class="table-head"></td>
                        </tr>
                    </thead>
                    <tbody class="table-group-divider text-center">
                        @forelse ($applications as $application)
                        <tr>
                            <td class="text-cap text-start title">{{$application->program->title}}</td>
                            <td class="text-cap">{{$application->program->agency->userInfo->name}}</td>
                            <td>
                                <p>
                                    @foreach(explode(',', $application->program->schedule) as $date)
                                    {{ \Carbon\Carbon::parse(trim($date))->format('F d, Y') }}
                                    @if(!$loop->last)
                                <p></p>
                                @endif
                                @endforeach                                
                                </p>
                            </td>
                            <td>
                                <p>{{ \Carbon\Carbon::parse($application->program->start_time)->format('h:i A') }} - {{ \Carbon\Carbon::parse($application->program->end_time)->format('h:i A') }}
                                </p>
                            </td>
                            <td class="status-cell">
                                <p class="match-info 
                                @if ($application->application_status == 'Pending')
                                pending
                                @endif
                                ">{{$application->application_status}}</p>
                            </td>
                            <td class="text-cap"><a href="{{ route('show-details', $application->program->id) }}">Show Details</a></td>
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
                    Trainings.
                </div>
                <div class="mb-3">
                    <form action="{{ route('trainings') }}" method="GET" id="filterForm">
                        <div class="d-flex align-items-center">
                            <label class="">Filter by:</label>
                            <select class="form-select" name="status" id="status" onchange="submitForm()">
                                <option value="all" {{ request('status') == 'all' ? 'selected' : '' }}>Show All</option>
                                <option value="ongoing" {{ request('status') == 'ongoing' ? 'selected' : '' }}>Ongoing</option>
                                <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                            </select>
                        </div>
                    </form>
                </div>
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <td class="table-head">Title</td>
                            <td class="table-head">Agency</td>
                            <td class="table-head">Schedule</td>
                            <td class="table-head">Time</td>
                            <td class="table-head">Status</td>
                            <td class="table-head"></td>
                        </tr>
                    </thead>
                    <tbody class="table-group-divider text-center">
                        @forelse ($trainings as $training)
                        <tr>
                            <td class="text-cap text-start title">{{$training->program->title}}</td>
                            <td class="text-cap">{{$training->program->agency->userInfo->name}}</td>
                            <td>
                                <p>
                                    @foreach(explode(',', $training->program->schedule) as $date)
                                    {{ \Carbon\Carbon::parse(trim($date))->format('F d, Y') }}
                                    @if(!$loop->last)
                                <p></p>
                                @endif
                                @endforeach
                                
                                </p>
                            </td>
                           <td><p>{{ \Carbon\Carbon::parse($training->program->start_time)->format('h:i A') }} - {{ \Carbon\Carbon::parse($training->program->end_time)->format('h:i A') }}
                                </p></td>
                            <td class="status-cell">
                                <p class="match-info @if ($training->completion_status == 'Ongoing')
                                pending
                                @endif
                                ">{{$training->completion_status}}</p>
                            </td>
                            <td class="text-cap"><a href="{{ route('show-details', $training->program->id) }}">Show Details</a></td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center">No trainings</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
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
            <h3>{{$trainingsCount}}</h3>
            <p>Total Trainings</p>
        </div>
    </div>
    <!-- <div class="mb-3">
        <div class="text-start header-texts fs-2 back-link-container border-bottom">
            Tracker.
        </div>
    </div>
    <div class="outer">
        <div class="table-container">
            <div class="applications">
                <div class="text-start header-texts fs-4 back-link-container border-bottom mb-3">
                    Applications.
                </div>
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <td class="table-head">Title</td>
                            <td class="table-head">Agency</td>
                            <td class="table-head">Schedule</td>
                            <td class="table-head">Status</td>
                            <td class="table-head"></td>
                        </tr>
                    </thead>
                    <tbody class="table-group-divider text-center">
                        @forelse ($applications as $application)
                        <tr>
                            <td class="text-cap text-start title">{{$application->program->title}}</td>
                            <td class="text-cap">{{$application->program->agency->userInfo->name}}</td>
                            <td>
                                <p>
                                    @foreach(explode(',', $application->program->schedule) as $date)
                                    {{ \Carbon\Carbon::parse(trim($date))->format('F d, Y') }}
                                    @if(!$loop->last)
                                <p></p>
                                @endif
                                @endforeach
                                </p>
                            </td>
                            <td class="status-cell">
                                <p class="match-info 
                                @if ($application->application_status == 'Pending')
                                pending
                                @endif
                                ">{{$application->application_status}}</p>
                            </td>
                            <td class="text-cap"><a href="{{ route('show-details', $application->program->id) }}">Show Details</a></td>
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
                    Trainings.
                </div>
                <div class="mb-3">
                    <form action="{{ route('trainings') }}" method="GET" id="filterForm">
                        <div class="d-flex align-items-center">
                            <label class="">Filter by:</label>
                            <select class="form-select" name="status" id="status" onchange="submitForm()">
                                <option value="all" {{ request('status') == 'all' ? 'selected' : '' }}>Show All</option>
                                <option value="ongoing" {{ request('status') == 'ongoing' ? 'selected' : '' }}>Ongoing</option>
                                <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                            </select>
                        </div>
                    </form>
                </div>
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <td class="table-head">Title</td>
                            <td class="table-head">Agency</td>
                            <td class="table-head">Start</td>
                            <td class="table-head">End</td>
                            <td class="table-head">Status</td>
                            <td class="table-head"></td>
                        </tr>
                    </thead>
                    <tbody class="table-group-divider text-center">
                        @forelse ($trainings as $training)
                        <tr>
                            <td class="text-cap text-start title">{{$training->program->title}}</td>
                            <td class="text-cap">{{$training->program->agency->userInfo->name}}</td>
                            <td>
                                <p>
                                    @foreach(explode(',', $training->program->schedule) as $date)
                                    {{ \Carbon\Carbon::parse(trim($date))->format('F d, Y') }}
                                    @if(!$loop->last)
                                <p></p>
                                @endif
                                @endforeach
                                </p>
                            </td>
                            <td class="status-cell">
                                <p class="match-info @if ($training->completion_status == 'Ongoing')
                                pending
                                @endif
                                ">{{$training->completion_status}}</p>
                            </td>
                            <td class="text-cap"><a href="{{ route('show-details', $training->program->id) }}">Show Details</a></td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center">No trainings</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
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
                <h3>{{$trainingsCount}}</h3>
                <p>Total Trainings</p>
            </div>
        </div>
    </div> -->
</div>


@endsection

<script>
    function submitForm(status) {
        document.getElementById('filterForm').submit();
    }
</script>