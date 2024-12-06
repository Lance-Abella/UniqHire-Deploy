@extends('layout')

@section('page-title', 'Events')

@section('page-content')

<div class="events-container">
    <div class="outer ">
        <div class="event-grid">
            @forelse($events as $event)
            <div class="event-card">
                <div class="d-flex">
                    <div class="prog-img" @if (!empty($event->employer->profile_path)) style=" background-image: url({{ asset($event->employer->profile_path) }}); background-repeat: no-repeat; background-size: cover; " @endif>
                        @if (empty($event->employer->profile_path))
                        <span>{{ strtoupper(substr($event->employer->name, 0, 1)) }}</span>
                        @endif

                    </div>
                    <div class="d-flex justify-content-between prog-head">
                        <div class="header">
                            <h4 class="text-cap">{{$event->title}}</h4>
                            <p class="sub-text text-cap">{{$event->employer->name}}</p>
                            <p class="sub-text text-cap location" id="location-"><i class='bx bx-map sub-text'></i>{{$event->employer->location}}</p>
                            <input type="hidden" id="lat-" value="">
                            <input type="hidden" id="lng-" value="">
                        </div>
                        <div class="text-end date-posted">
                            @php
                            $diff = intval($event->created_at->diffInSeconds(now()));
                            @endphp
                            <p class="text-end">
                                @if ($diff < 60)
                                    {{ $diff }}s
                                    @elseif ($diff < 3600)
                                    {{ intdiv($diff , 60) }}m
                                    @elseif ($diff < 86400)
                                    {{ intdiv($diff , 3600) }}h
                                    @else
                                    {{ $event->created_at->diffForHumans() }}
                                    @endif
                                    </p>
                        </div>
                    </div>
                </div>
                <div class="row prog-desc mb-1">
                    <p>{!! nl2br(e($event->description)) !!}</p>
                </div>
                <div class="d-flex justify-content-between">
                    <div class="infos d-flex align-items-center">
                        <span><strong>Date</strong> &nbsp;</span>
                        <div class="match-info">
                            {{ \Carbon\Carbon::parse(trim($event->schedule))->format('F d, Y') }}
                        </div>
                        <span>&nbsp; | &nbsp;</span>
                        <span><strong>Time</strong> &nbsp;</span>
                        <div class="match-info">
                            {{ \Carbon\Carbon::parse(trim($event->start_time))->format('h:i A') }} - {{ \Carbon\Carbon::parse(trim($event->end_time))->format('h:i A') }}
                        </div>
                        <span>&nbsp; | &nbsp;</span>
                        <div class="">
                            @include('slugs.participants')
                        </div>
                    </div>
                    <div class="px-2">
                        <form id="apply-form-{{ $event->id }}" action="{{ route('event-application') }}" method="POST">
                            @csrf
                            <input type="hidden" name="user_id" value="{{ Auth::user()->id }}">
                            <input type="hidden" name="event_id" value="{{ $event->id }}">
                            <div>
                                <button type="submit" class="submit-btn border-0 @if (!$isCertified)
                                    disabled
                                    @endif
                                    " @if (!$isCertified)
                                    disabled
                                    @endif title="Enroll and get certified first">
                                    Register
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

            </div>
            @empty
            <div class="d-flex justify-content-center">No events for now.</div>
            @endforelse
        </div>
        <div class="pagination-container">
            <div class="pagination">
                {{ $events->links() }}
            </div>
        </div>
    </div>
</div>

<script>

</script>
@endsection