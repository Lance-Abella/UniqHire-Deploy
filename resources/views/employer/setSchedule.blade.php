@extends('layout')
@section('page-title', 'Add Training Program')
@section('page-content')
<form action="{{route('set-schedule', $employee->id)}}" method="POST" class="container mb-5 add-form" style="height:90vh;">
    @csrf
    <div class="row mt-2 mb-2 border-bottom">
        <div class="text-start header-texts back-link-container">
            <a href="{{ route('jobs-show', $employee->job_id ) }}" class="m-1 back-link"><i class='bx bx-left-arrow-alt'></i></a>
            Set Schedule.
        </div>
    </div>
    <div class="form-floating mb-3">
                           <input type="date" class="form-control date" name="schedule" required placeholder="Choose Date">
                            <label for="floatingInput">Schedule Interview</label>
                            @error('schedule')
                            <span class="error-msg">{{ $message }}</span>
                            @enderror
                        </div>
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

    <div class="d-flex justify-content-evenly mt-3 prog-btn">
        <button type="reset" class="deny-btn border-0">Clear</button>
        <button type="submit" class="submit-btn border-0">Add</button>
    </div>
</form>


@endsection