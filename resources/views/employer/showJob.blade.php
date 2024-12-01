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
                    {{ $listing->description }}
                </div>
            </div>
            <ul class="nav nav-underline" role="tablist">
                <li class="nav-item">
                    <a class="nav-link active" data-bs-toggle="tab" href="#requirements" role="tab">Requirements</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-bs-toggle="tab" href="#enrollees" role="tab">Hired PWDs</a>
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
                        <div class="d-flex justify-content-start">
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
                <div class="tab-pane enrollees" id="enrollees" role="tabpanel">
                   <table class="table table-striped table-hover">
                    <tbody>
                        @forelse ($employees as $employee)
                        <tr>
                            <td class="name">
                                <a href="{{ route('show-profile', $employee->application->user->id) }}">
                                    {{ $employee->application->user->userInfo->name }}
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="3" class="text-center">No Employees yet.</td>
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
                <p>Pending Applicants</p>
            </div>                
            <div class="counts">
                <h3>{{$applicantCount}}</h3>
                <p>Total Hired PWDs</p>
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

