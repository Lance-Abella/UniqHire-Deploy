@extends('layout')

@section('page-title', 'Calendar')

@section('page-content')

<div class="calendar-container">
	<div class="calendar" id="calendar"></div>
</div>

<script>
	document.addEventListener('DOMContentLoaded', function() {
    var calendarEl = document.getElementById('calendar');
    var calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'dayGridMonth',
        events: function(info, successCallback, failureCallback) {
            fetch('/pwd/calendar', {
                method: 'GET',
                headers: {
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => successCallback(data))
            .catch(error => failureCallback(error));
        }
    });
    calendar.render();
});


</script>
@endsection