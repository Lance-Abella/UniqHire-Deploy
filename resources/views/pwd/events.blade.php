@extends('layout')

@section('page-title', 'Events')

@section('page-content')

<div class="events-container">
    <div class="outer ">
        <div class="event-grid">
            <div class="event-card">
                <div class="d-flex">
                    <div class="prog-img">


                    </div>
                    <div class="d-flex justify-content-between prog-head">
                        <div class="header">
                            <h4 class="text-cap">Title</h4>
                            <p class="sub-text text-cap">Post Owner</p>
                            <p class="sub-text text-cap location" id="location-"><i class='bx bx-map sub-text'></i>Location owner</p>
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
                    <p>Description</p>
                </div>
                <div class="infos d-flex align-items-center">
                    <span><strong>Date</strong> &nbsp;</span>
                    <div class="match-info">
                        Date
                    </div>
                    <span>&nbsp; | &nbsp;</span>
                    <span><strong>Time</strong> &nbsp;</span>
                    <div class="match-info">
                        Start - End
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>

</script>
@endsection