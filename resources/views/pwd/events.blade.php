@extends('layout')

@section('page-title', 'Events')

@section('page-content')

<div class="events-container">
    <div class="outer ">
        <div class="event-grid">
            @forelse($events as $event)
            <div class="event-card">
                <div class="d-flex">
                    <div class="prog-img">


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

                            <p class="text-end">
                                date posted
                        </div>
                    </div>
                </div>
                <div class="row prog-desc mb-1">
                    <p>{{$event->description}}</p>
                </div>
                <div class="infos d-flex align-items-center">
                    <span><strong>Date</strong> &nbsp;</span>
                    <div class="match-info">
                        {{ \Carbon\Carbon::parse(trim($event->schedule))->format('F d, Y') }}
                    </div>
                    <span>&nbsp; | &nbsp;</span>
                    <span><strong>Time</strong> &nbsp;</span>
                    <div class="match-info">
                        <td>{{ \Carbon\Carbon::parse(trim($event->start_time))->format('h:i A') }} - {{ \Carbon\Carbon::parse(trim($event->end_time))->format('h:i A') }}</td>
                    </div>
                    <form id="apply-form-{{ $event->id }}" action="{{ route('event-application') }}" method="POST">
                        @csrf
                        <input type="hidden" name="user_id" value="{{ Auth::user()->id }}">
                        <input type="hidden" name="event_id" value="{{ $event->id }}">
                        <div>
                            <button type="submit" class="submit-btn border-0">
                                    Register
                            </button>
                        </div>
                    </form>
                   
                </div>
            </div>
            @empty
            <p>No events for now.</p>
            @endforelse
        </div>
    </div>
</div>

<script>

</script>
@endsection