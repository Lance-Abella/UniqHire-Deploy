@extends ('layout')
@section('page-title', 'Find Work')
@section('page-content')
<div class="pwd-browse-prog pwd-browse-job mb-3">
    <div class="filter-container">
        <form action="{{ route('pwd-list-program') }}" method="GET" id="filterForm">
            <div class="d-flex justify-content-between mb-3">
                <h3>Filter</h3>
                <i class='bx bx-filter-alt fs-3 sub-text text-end'></i>
            </div>

        </form>
    </div>
    <div class="list">
        <div class="mb-4 searchbar-container">
            <div class="d-flex justify-content-center">
                <form role="search" action="{{ route('pwd-list-job') }}" method="GET" id="searchForm">
                    <div class="d-flex searchbar">
                        <input class="form-control" type="search" placeholder="Search Jobs" aria-label="Search" id="searchInput" onchange="checkAndSubmit()" name="search" value="{{ request('search') }}">
                        <button class="submit-btn border-0" type="submit">Search</button>
                    </div>
                </form>
            </div>
        </div>
        <div class="d-flex flex-column align-items-center">

            <div class="prog-grid" id="prog-grid">
                <div class="mb-4">
                    <div class="recommend-label-container">
                        <span class="recommend-label">Recommended</span>
                    </div>
                </div>
                @if ($paginatedItems->isEmpty())
                <div class="sub-text no-result">No results found.</div>
                @else
                <div class="prog-grid-list">
                    @foreach ($paginatedItems as $ranked)

                    <div class="job-card mb-2" data-program-id="{{ $ranked['job']->id }}" data-lat="{{ $ranked['job']->latitude }}" data-lng="{{ $ranked['job']->longitude }}">
                        <input type="text" name="" value="{{$ranked['similarity']}}" id="">

                        <a href="" class="d-flex prog-texts">
                            <div class="prog-texts-container">
                                <div class="d-flex mb-2">
                                    <div class="prog-img" @if (!empty($ranked['job']->employer->userInfo->profile_path)) style=" background-image: url({{ asset($ranked['job']->employer->userInfo->profile_path) }}); background-repeat: no-repeat; background-size: cover; " @endif>
                                        @if (empty($ranked['job']->employer->userInfo->profile_path))
                                        <span>{{ strtoupper(substr($ranked['job']->employer->userInfo->name, 0, 1)) }}</span>
                                        @endif
                                    </div>

                                    <div class="prog-head d-flex justify-content-between" style="width:16.5rem">
                                        <div class=" header" style="width:12rem">
                                            <h4 class="text-cap">{{$ranked['job']->position}}</h4>
                                            <p class="sub-text text-cap">{{$ranked['job']->employer->userInfo->name}}</p>
                                            <p class="sub-text text-cap location">
                                                <i class='bx bx-map sub-text'></i>{{$ranked['job']->location}}
                                            </p>
                                        </div>
                                        <div class="text-end date-posted">
                                            @php
                                            $diff = $ranked['job']->created_at->diffInSeconds(now());
                                            @endphp
                                            <p class="text-end">@if ($diff < 60)
                                                    {{ $diff }}s
                                                    @elseif ($diff < 3600)
                                                    {{ floor($diff / 60) }}m
                                                    @elseif ($diff < 86400)
                                                    {{ floor($diff / 3600) }}h
                                                    @else
                                                    {{ $ranked['job']->created_at->diffForHumans() }}
                                                    @endif</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="prog-desc">
                                    <p><span><i class='bx bx-money'></i> Salary: {{ $ranked['job']->salary }}</span> | <span><i class='bx bx-briefcase'></i> Work petup: On-site</span></p>
                                </div>
                                <div class="infos">
                                    <div class="match-info">qwe</div>
                                </div>
                            </div>
                        </a>
                    </div>
                    @endforeach
                </div>
                @endif
            </div>
            <div class="pagination-container">

            </div>

        </div>
    </div>
</div>
@endsection