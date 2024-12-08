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
        initialView: 'timeGridWeek',
        events: function(info, successCallback, failureCallback) {
            fetch('/agency/calendar', {
                method: 'GET',
                headers: {
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                console.log(data);
                console.log(FullCalendar.version);
                successCallback(data);
            })
            .catch(error => failureCallback(error));
        },
        views: {
            dayGridMonth: {
                displayEventTime: false, 
            },
            timeGridWeek: {
                displayEventTime: true, 
            },
            timeGridDay: {
                displayEventTime: true, 
            },
        },
        eventTimeFormat: {
            hour: '2-digit',
            minute: '2-digit',
            hour12: false,
        },
    });
    calendar.render();
});
</script>
@endsection