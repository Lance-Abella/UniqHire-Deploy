@extends('layout')

@section('page-title', 'Browse Training Programs')

@section('page-content')

<div class="pwd-browse-prog d-flex flex-column align-items-center" style="padding-top: 50px;">
    <div class="browse-area" style="width: 100%; max-width: 900px;">
        <!-- Search Bar -->
        <div class="mb-4 searchbar-container">
            <form role="search" action="{{ route('list-of-tp') }}" method="GET" id="searchForm" class="w-100">
                <div class="d-flex searchbar w-100">
                    <input class="form-control me-2" type="search" placeholder="Search Training Programs" aria-label="Search" id="searchInput" onchange="checkAndSubmit()" name="search" value="{{ request('search') }}">
                    <button class="submit-btn border-0" type="submit">Search</button>
                </div>
            </form>
        </div>

        <!-- Program Grid -->
        <div class="outer w-100">
            <div class="prog-grid" id="prog-grid">
                <!-- Recommendation Label -->
                <div class="mb-4 text-center">
                    <div class="recommend-label-container">
                        <span class="recommend-label">Recommended</span>
                    </div>
                </div>

                <!-- Training Programs -->
                @forelse ($paginatedItems as $program)
                <div class="row prog-card mb-2">
                    <div class="col">
                        <a href="{{ route('trainingprog-details', $program->id) }}" class="d-flex prog-texts">
                            <div class="prog-texts-container w-100">
                                <div class="d-flex mb-2 align-items-center">
                                    <!-- Agency Profile Image -->
                                    <div class="prog-img"
                                        @if (!empty($program->agency->userInfo->profile_path))
                                        style="background-image: url({{ asset($program->agency->userInfo->profile_path) }}); background-repeat: no-repeat; background-size: cover;"
                                        @endif>
                                        @if (empty($program->agency->userInfo->profile_path))
                                        <span>{{ strtoupper(substr($program->agency->userInfo->name, 0, 1)) }}</span>
                                        @endif
                                    </div>

                                    <!-- Program Header -->
                                    <div class="prog-head ms-3">
                                        <h4>{{ $program->title }}</h4>
                                        <p>{{ $program->agency->userInfo->name }}</p>
                                        <p>{{ $program->created_at->diffForHumans() }}</p>
                                    </div>
                                </div>
                                <!-- Program Description -->
                                <p>{{ $program->description }}</p>

                                <!-- Crowdfunding Information -->
                                @if ($program->crowdfund)
                                <p>Goal: {{ $program->crowdfund->goal }}</p>
                                @endif
                            </div>
                        </a>
                    </div>
                </div>
                @empty
                <div class="sub-text no-result text-center">No programs available.</div>
                @endforelse
            </div>
        </div>

        <!-- Pagination -->
        <div class="pagination-container mt-4">
            <div class="pagination">
                {{ $paginatedItems->links() }}
            </div>
        </div>
    </div>
</div>

<script>
    function checkAndSubmit() {
        var searchInput = document.getElementById('searchInput');
        if (searchInput.value.trim() === '') {
            document.getElementById('searchForm').submit();
        }
    }
</script>

@endsection