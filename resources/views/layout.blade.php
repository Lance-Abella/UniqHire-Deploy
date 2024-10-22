    <!DOCTYPE html>
    <html lang="en">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="csrf-token" content="{{ csrf_token() }}" />
        <title>UniqHire | @yield('page-title')</title>
        @include('slugs.links')
        <link rel="icon" href="{{ asset('images/tab-icon.png') }}">

    </head>

    <body>
        @if (session('success'))
        <script>
            Swal.fire({
                title: "Success",
                text: "{{session('success')}}",
                icon: "success",
                timer: 3000,
            });
        </script>
        @endif
        @if (session('error'))
        <script>
            Swal.fire({
                title: "Error",
                text: "{{session('error')}}",
                icon: "error",
                timer: 3000,
            });
        </script>
        @endif
        <div class="layout-container">
            @if (Auth::check())
            <nav class="sidebar">
                <header class="">
                    <div class="logo-sidebar">
                        <span class="logo-img">
                            <!-- <img src="{{ asset('images/tab-icon.png')}} " alt=""> -->
                            <i class='bx bx-menu side-icon'></i>
                        </span>
                        <div class="logo-name">
                        </div>
                    </div>
                </header>
                <div class="sidebar-menu">
                    <ul class="">
                        <li class="side-item">
                            <a href="{{ route('profile')}}" class="{{ request()->routeIs('profile') ? 'active' : '' }}">
                                <i class='bx bx-user-circle side-icon'></i>
                                <span class="side-title">Profile</span>
                            </a>
                        </li>

                        <!-- PWD ROLE ACCESS -->
                        @if (Auth::user()->hasRole('PWD'))
                        <li class="side-item">
                            <a href="{{route('trainings')}}" class="trainings-drop {{ request()->routeIs('trainings', 'show-details') ? 'active' : '' }}">
                                <i class='bx bxs-school side-icon'></i>
                                <span class="side-title">Trainings</span>
                            </a>
                        </li>
                        <li class="side-item">
                            <a href="{{ route('pwd-calendar') }}" class="{{ request()->routeIs('pwd-calendar') ? 'active' : '' }}">
                                <i class='bx bx-calendar side-icon'></i>
                                <span class="side-title">Calendar</span>
                            </a>
                        </li>
                        <li class="side-item">
                            <a href="#">
                                <i class='bx bx-cog side-icon'></i>
                                <span class="side-title">Settings</span>
                            </a>
                        </li>

                        @endif

                        <!-- ADMIN ROLE ACCESS -->
                        @if (Auth::user()->hasRole('Admin'))
                        <li class="side-item"><a href="{{route('pwd-list')}}">
                                <i class='bx bx-handicap side-icon'></i>
                                <span class="side-title">PWDs</span>
                            </a></li>
                        <li class="side-item"><a href="{{route('trainer-list')}}">
                                <i class='bx bxs-school side-icon'></i>
                                <span class="side-title">Training Agencies</span>
                            </a></li>
                        <li class="side-item"><a href="{{route('employee-list')}}">
                                <i class='bx bx-briefcase-alt-2 side-icon'></i>
                                <span class="side-title">Employers</span>
                            </a></li>
                        <li class="side-item"><a href="{{route('sponsor-list')}}">
                                <i class='bx bx-dollar-circle side-icon'></i>
                                <span class="side-title">Sponsors</span>
                            </a></li>
                        <li class="side-item"><a href="{{route('skill-list')}}">
                                <i class='bx bxs-brain side-icon'></i>
                                <span class="side-title">Manage Skills</span>
                            </a></li>
                        <li class="side-item"><a href="">
                                <i class='bx bx-cog side-icon'></i>
                                <span class="side-title">Settings</span>
                            </a></li>
                        <!-- <li><a href="#"><i class='bx bx-briefcase-alt-2 side-icon'></i><span class="side-title">Employers</span></a></li> -->
                        @endif
                        <!-- TRAINER ROLE ACCESS -->
                        @if (Auth::user()->hasRole('Training Agency'))
                        <li class="side-item">
                            <a href="{{route('programs-manage')}}" class="{{ request()->routeIs('programs-manage', 'programs-add', 'programs-edit', 'programs-show') ? 'active' : '' }}">
                                <i class='bx bxs-school side-icon'></i>
                                <span class="side-title">Training Programs</span>
                            </a>
                        </li>
                        <li class="side-item">
                            <a href="{{ route('agency-calendar') }}" class="{{ request()->routeIs('agency-calendar') ? 'active' : '' }}">
                                <i class='bx bx-calendar side-icon'></i>
                                <span class="side-title">Calendar</span>
                            </a>
                        </li>
                        @endif

                        <!-- <li><a href="#"><i class='bx bx-cog side-icon'></i><span class="side-title">Sponsor</span></a></li> -->
                    </ul>
                    <div class="sidebar-bottom">
                        <li class=""><a href="{{ url('/logout')}}"><i class='bx bx-log-out-circle side-icon'></i><span class="side-title">Logout</span></a></li>
                    </div>
                </div>
            </nav>
            <div class="">
                <div class=" content-container">
                    <nav class="navbar border-bottom">
                        <div class="navbar-container">
                            <div>
                                <ul class="d-flex align-items-center">
                                    <li class="logo-container"><a href="#"><img class="logo" src="{{ asset('images/logo.png') }}" alt=""></a></li>
                                    <li class="nav-item"><a href="{{route('home')}}" class="{{ request()->routeIs('home') ? 'active' : '' }}">Home</a></li>
                                    @if (Auth::user()->hasRole('PWD'))
                                    <li class="nav-item"><a href="{{route('pwd-list-program')}}" class="{{ request()->routeIs('pwd-list-program', 'programs-show', 'training-details') ? 'active' : '' }}">Browse Training Programs</a></li>
                                    <li class="nav-item"><a href="">Find Work</a></li>
                                    @endif
                                    @if (Auth::user()->hasRole('Sponsor'))
                                    <li class="nav-item"><a href="{{route('list-of-tp')}}" class="{{ request()->routeIs('list-of-tp', 'programs-show', 'training-details') ? 'active' : '' }}">Browse Training Programs</a></li>
                                    <li class="nav-item"><a href="">Find Work</a></li>
                                    @endif

                                    <!-- <li class="nav-item"><a href="{{ route('home') }}/#about" class="">About</a></li> -->

                                </ul>
                            </div>
                            <div>
                                <ul class="d-flex align-items-center">
                                    <li class="nav-item user-notif dropdown">

                                        <a href="#" class="dropdown-toggle" id="notificationDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                                            <i class='bx bxs-inbox'></i>
                                            <span id="notification-badge" class="badge bg-danger d-none">0</span> <!-- Badge element -->
                                        </a>
                                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="notificationDropdown">
                                            <!-- Notifications will be dynamically added here -->
                                        </ul>
                                    </li>

                                    <li class="nav-item user-index"><span>{{ Auth::user()->userInfo->name }}</span></li>
                                </ul>
                            </div>
                        </div>
                    </nav>
                </div>
                @if (Auth::user()->userInfo->skills->isEmpty() && Auth::user()->hasRole('PWD'))
                <div class="alert bg-success-subtle d-flex justify-content-center alert-dismissible fade show" role="alert">
                    <div class="close-btn d-flex justify-content-center align-items-center">
                        <button type="button" class="border-0" data-bs-dismiss="alert" aria-label="Close"><i class='bx bx-minus'></i></button>
                    </div>
                    <div class="">
                        Add skill/s to your profile to get better recommendations. <a href="{{route('profile')}}">Check and Edit Profile.</a>
                    </div>
                </div>
                @endif

                <div class="content-container">
                    @yield('page-content')
                </div>
            </div>
            @else
            <div class="container-fluid">
                @yield('auth-content')
            </div>
            @endif
        </div>
    </body>
    <script>
        $(document).ready(function() {
            function fetchNotifications() {
                $.get("{{ route('notifications.getNotifications') }}", function(data) {
                    console.log(data);
                    var notifDropdown = $('#notificationDropdown').next('.dropdown-menu');
                    var badge = $('#notification-badge');
                    notifDropdown.empty(); // Clear existing notifications

                    if (data.length > 0) {
                        badge.removeClass('d-none').text(data.length);
                        data.forEach(function(notification) {
                            var notificationContent = '';
                            if (notification.type === 'App\\Notifications\\NewTrainingProgramNotification') {
                                notificationContent = '<li><a class="dropdown-item" href="' + notification.data.url + '">' +
                                    '<span class="notif-owner text-cap">' +
                                    notification.data.agency_name +
                                    '</span>' +
                                    ' has posted a new training' +
                                    '<div class="notif-content sub-text">' +
                                    'Entitled ' +
                                    '<span class="sub-text text-cap">' +
                                    notification.data.title +
                                    '</span>' +
                                    '. Starts on ' +
                                    '<span class="sub-text">' +
                                    notification.data.start_date + //Change Format pero if dili makaya kay ayaw nlng sya iapil og display
                                    '</span>' +
                                    '. Click to check this out.' +
                                    '</div>' +
                                    '</a></li>'
                            } else if (notification.type === 'App\\Notifications\\PwdApplicationNotification') {
                                notificationContent = '<li><a class="dropdown-item" href="' + notification.data.url + '">' +
                                    'A PWD user has applied for your training program: ' +
                                    '<span class="sub-text text-cap">' +
                                    notification.data.title +
                                    '</span>' +
                                    '. Click to view application.' +
                                    '</a></li>';
                            } else if (notification.type === 'App\\Notifications\\ApplicationAcceptedNotification') {
                                notificationContent = '<li><a class="dropdown-item" href="' + notification.data.url + '">' +
                                    'Your application in ' +
                                    '<span class="notif-owner text-cap">' +
                                    notification.data.program_title +
                                    '</span>' +
                                    '<div class="notif-content sub-text">' +
                                    ' has been accepted by ' +
                                    '<span class="notif-owner text-cap">' +
                                    notification.data.agency_name +
                                    '</span>' +
                                    '. Click to view details.' +
                                    '</div>'
                                '</a></li>';
                            }
                            notifDropdown.append(notificationContent);
                        });
                    } else {
                        badge.addClass('d-none');
                        notifDropdown.append('<li><span class="dropdown-item">No notifications</span></li>');
                    }
                }).fail(function() {
                    console.error('Failed to fetch notifications');
                });
            }

            // Fetch notifications on page load
            fetchNotifications();

            // Handle dropdown showing
            $('#notificationDropdown').on('show.bs.dropdown', function() {
                fetchNotifications();
            });
        });

        function toggleSubmenu() {
            var submenu = document.getElementById('trainings-submenu');
            var icon = document.getElementById('arrow-drop');

            submenu.classList.toggle('active-drop');
            icon.classList.toggle('arrow-down');
        }
    </script>

    </html>