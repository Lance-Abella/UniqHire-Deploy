@extends('layout')

@section('page-title', 'Events')

@section('page-content')

<div class="events-container" @if ( empty($events))
    style="height:90vh;"
    @endif>
    <div class="outer ">
        <div class="post-form mb-3">
            <form action="{{route('post-events')}}" method="POST">
                @csrf
                <div class="form-floating mb-3">
                    <input type="text" class="form-control" id="title" name="title" value="{{ old('title') }}" required placeholder="Name">
                    <label for="name">Title</label>
                    @error('title')
                    <span class="error-msg">{{ $message }}</span>
                    @enderror
                </div>
                <div class="form-floating mb-3">
                    <textarea class="form-control" placeholder="Description" id="floatingTextarea2" name="description" style="height: 150px">{{old('description')}}</textarea>
                    <label for="floatingTextarea2">Description</label>
                    @error('description')
                    <span class="error-msg">{{ $message }}</span>
                    @enderror
                </div>
                <div class="row">
                    <div class="col">
                        <div class="form-floating mb-3">
                            <input type="date" class="form-control date" name="schedule" required placeholder="Choose Date">
                            <label for="floatingInput">Choose Date</label>
                        </div>
                    </div>
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
                <div class="btn-container">
                    <button type="submit" class="submit-btn border-0">Post</button>
                </div>
            </form>
        </div>
        <div class="event-grid">
            @foreach($events as $event)
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
                    @if ($event->employer_id == Auth::user()->id)
                    <div class="px-2">
                        <form id="delete-event{{ $event->id }}" action="{{ route('delete-event', $event->id) }}" method="POST">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="submit-btn delete border-0" onclick="confirmDelete(event, 'delete-event{{ $event->id }}')">Delete</button>
                        </form>

                    </div>
                    @endif
                </div>
            </div>
            @endforeach
        </div>
        <div class="pagination-container">
            <div class="pagination">
                {{ $events->links() }}
            </div>
        </div>
    </div>
</div>
<script>
    function confirmDelete(event, formId) {
        event.preventDefault();
        Swal.fire({
            title: "Confirmation",
            text: "Do you really want to delete this event?",
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

@endsection