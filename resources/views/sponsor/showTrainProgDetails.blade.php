@extends('layout')

@section('page-title', 'Program Details')

@section('page-content')
<div class="agency-show-prog">
    <div class="back-btn">
        @if (Route::currentRouteName() == 'training-details')
        <a href="{{ route('pwd-list-program') }}" class="m-1 back-link"><i class='bx bx-left-arrow-alt'></i></a>
        @elseif(Route::currentRouteName() == 'show-details' )
        <a href="{{ route('trainings') }}" class="m-1 back-link"><i class='bx bx-left-arrow-alt'></i></a>
        @endif
    </div>
    <div class="prog-details">
        <div class="header d-flex">
            <div class="mb-3 titles">
                <h3 class="text-cap">{{ $program->title }}</h3>
                <p class="sub-text text-cap">{{ $program->agency->userInfo->name }}</p>
                <p class="sub-text prog-loc text-cap" id="location"><i class='bx bx-map sub-text'></i>Loading address...</p>
                <input type="hidden" id="lat" value="{{ $program->latitude }}">
                <input type="hidden" id="lng" value="{{ $program->longitude }}">
            </div>
        </div>
        <div class="mb-5">
            <div class="col">
                {{ $program->description }}
            </div>
        </div>
        <ul class="nav nav-underline" role="tablist">
            <li class="nav-item">
                <a class="nav-link active" data-bs-toggle="tab" href="#requirements" role="tab">Requirements</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" data-bs-toggle="tab" href="#competencies" role="tab">Competencies</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" data-bs-toggle="tab" href="#enrollees" role="tab">Enrollees</a>
            </li>
            @if ($program->crowdfund)
            <li class="nav-item">
                <a class="nav-link" data-bs-toggle="tab" href="#sponsors" role="tab">Sponsors</a>
            </li>
            @endif
            <li class="nav-item">
                <a class="nav-link" data-bs-toggle="tab" href="#reviews" role="tab">Reviews</a>
            </li>
        </ul>
        <div class="tab-content">
            <div class="tab-pane active" id="requirements" role="tabpanel">
                <div class="requirements">
                    <div class="d-flex justify-content-start mb-5">
                        <div class="more-info">
                            <h5>Schedule</h5>
                            <p>
                                @foreach(explode(',', $program->schedule) as $date)
                                {{ \Carbon\Carbon::parse(trim($date))->format('F d, Y') }}
                                @if(!$loop->last)
                            <p></p>
                            @endif
                            @endforeach
                            </p>
                        </div>
                        <div class="more-info">
                            <h5>Participants</h5>
                            <p>{{ number_format($program->participants) . ' Persons' }}&nbsp;&nbsp; <span class="sub-text">({{$slots}} remaining)</span></p>
                        </div>
                    </div>

                    <div class="d-flex justify-content-start mb-5">
                        <div class="more-info">
                            <h5>Age</h5>
                            <p class="match-info">{{ $program->start_age . ' - ' . $program->end_age . ' Years Old' }}</p>
                        </div>
                        <div class="more-info">
                            <h5>Skills Acquired</h5>
                            <ul>
                                @foreach ($program->skill as $skill)
                                <li class="match-info mb-2">{{ $skill->title }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                    <div class="d-flex justify-content-start more-info">
                        <div class="more-info">
                            <h5>We Accept</h5>
                            <ul>
                                @foreach ($program->disability as $disability)
                                <li class="match-info mb-2">{{ $disability->disability_name }}</li>
                                @endforeach
                            </ul>
                        </div>
                        <div class="more-info">
                            <h5>Education Level (at least)</h5>
                            <span class="match-info">{{ $program->education->education_name }}</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="tab-pane" id="competencies" role="tabpanel">
                <div>
                    <h5>Competencies</h5>
                    <ul>
                        @forelse ($program->competencies as $competency)
                        <li>{{ $competency->name }}</li>
                        @empty
                        <div>No competencies yet.</div>
                        @endforelse
                    </ul>
                </div>
            </div>

            <div class="tab-pane enrollees" id="enrollees" role="tabpanel">
                <table class="table table-striped table-hover">
                    <tbody>
                        @forelse ($enrollees as $enrollee)
                        <tr>
                            <td class="name">
                                <a href="{{ route('show-profile', $enrollee->application->user->id) }}">
                                    {{ $enrollee->application->user->userInfo->name }}
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="3" class="text-center">No enrollees yet.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if ($program->crowdfund)
            <div class="tab-pane" id="sponsors" role="tabpanel">
                <div class="crowdfund-progress mb-3">
                    <p class="sub-text">
                        Goal Amount: &nbsp;&nbsp;<span>{{number_format($program->crowdfund->goal, 0, '.', ',') . ' PHP'}}</span>
                    </p>
                    <p class="sub-text">
                        Crowdfunding Progress:
                    </p>
                    <div class="progress">
                        <div class="progress-bar" role="progressbar" aria-valuenow="{{ $program->crowdfund->progress }}" aria-valuemin="0" aria-valuemax="100">{{ $program->crowdfund->progress }}%</div>
                    </div>
                </div>

                <h5>Sponsors</h5>
                <span class=""></span>
            </div>
            @endif
            <div class="tab-pane" id="reviews" role="tabpanel">
                <div class="border reviews">
                    <div class="reviews-title d-flex justify-content-between">
                        <h5>Reviews</h5>
                        <span class="text-muted">({{ $program->reviews ? $program->reviews->count() : 0 }})</span>
                    </div>
                    @forelse($program->reviews ?? [] as $review) <!-- Use null coalescing here -->
                    <div class="review-card">
                        <h6>{{ $review->user->userInfo->name }}</h6>
                        <p class="sub-text text-cap mb-0">Rating: <span class="text-warning">({{ $review->rating }})</span></p>
                        <p>{{ $review->comment }}</p>
                    </div>
                    @empty
                    <div>No reviews yet.</div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
@endsection