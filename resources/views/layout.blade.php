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
        <div class="layout-container 
        @if (Auth::check()) grid-container @endif
        ">
            @if (Auth::check())
            <div class="grid-left">
                <div class="sidebar-sticky">
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
                                    <a href="{{ route('profile')}}" class="side-item-link {{ request()->routeIs('profile') ? 'active' : '' }}">
                                        <i class='bx bx-user-circle side-icon'></i>
                                        <span class="side-title">Profile</span>
                                    </a>
                                </li>

                                <!-- PWD ROLE ACCESS -->
                                @if (Auth::user()->hasRole('PWD'))
                                <li class="side-item">
                                    <a href="{{route('trainings')}}" class="side-item-link trainings-drop {{ request()->routeIs('trainings', 'show-details') ? 'active' : '' }}">
                                        <i class='bx bxs-school side-icon'></i>
                                        <span class="side-title">Trainings</span>
                                    </a>
                                </li>
                                <li class="side-item">
                                    <a href="{{ route('pwd-calendar') }}" class="side-item-link {{ request()->routeIs('pwd-calendar') ? 'active' : '' }}">
                                        <i class='bx bx-calendar side-icon'></i>
                                        <span class="side-title">Calendar</span>
                                    </a>
                                </li>
                                <li class="side-item">
                                    <a href="#" class="side-item-link">
                                        <i class='bx bx-cog side-icon'></i>
                                        <span class="side-title">Settings</span>
                                    </a>
                                </li>

                                @endif

                                <!-- ADMIN ROLE ACCESS -->
                                @if (Auth::user()->hasRole('Admin'))
                                <li class="side-item"><a href="{{route('pwd-list')}}" class="side-item-link">
                                        <i class='bx bx-handicap side-icon'></i>
                                        <span class="side-title">PWDs</span>
                                    </a></li>
                                <li class="side-item"><a href="{{route('trainer-list')}}" class="side-item-link">
                                        <i class='bx bxs-school side-icon'></i>
                                        <span class="side-title">Training Agencies</span>
                                    </a></li>
                                <li class="side-item"><a href="{{route('employee-list')}}" class="side-item-link">
                                        <i class='bx bx-briefcase-alt-2 side-icon'></i>
                                        <span class="side-title">Employers</span>
                                    </a></li>
                                <li class="side-item"><a href="{{route('sponsor-list')}}" class="side-item-link">
                                        <i class='bx bx-dollar-circle side-icon'></i>
                                        <span class="side-title">Sponsors</span>
                                    </a></li>
                                <li class="side-item"><a href="{{route('skill-list')}}" class="side-item-link">
                                        <i class='bx bxs-brain side-icon'></i>
                                        <span class="side-title">Manage Skills</span>
                                    </a></li>
                                <li class="side-item"><a href="" class="side-item-link">
                                        <i class='bx bx-cog side-icon'></i>
                                        <span class="side-title">Settings</span>
                                    </a></li>
                                <!-- <li><a href="#"><i class='bx bx-briefcase-alt-2 side-icon'></i><span class="side-title">Employers</span></a></li> -->
                                @endif
                                <!-- TRAINER ROLE ACCESS -->
                                @if (Auth::user()->hasRole('Training Agency'))
                                <li class="side-item">
                                    <a href="{{route('programs-manage')}}" class="side-item-link {{ request()->routeIs('programs-manage', 'programs-add', 'programs-edit', 'programs-show') ? 'active' : '' }}">
                                        <i class='bx bxs-school side-icon'></i>
                                        <span class="side-title">Programs</span>
                                    </a>
                                </li>
                                <li class="side-item">
                                    <a href="{{ route('agency-calendar') }}" class="side-item-link {{ request()->routeIs('agency-calendar') ? 'active' : '' }}">
                                        <i class='bx bx-calendar side-icon'></i>
                                        <span class="side-title">Calendar</span>
                                    </a>
                                </li>
                                @endif

                                @if (Auth::user()->hasRole('Employer'))
                                <li class="side-item">
                                    <a href="{{route('manage-jobs')}}" class="side-item-link {{ request()->routeIs('manage-jobs') ? 'active' : '' }}">
                                        <i class='bx bx-briefcase side-icon'></i>
                                        <span class="side-title">Job Listings</span>
                                    </a>
                                </li>
                                <li class="side-item">
                                    <a href="{{route('programs-manage')}}" class="side-item-link {{ request()->routeIs('programs-manage', 'programs-add', 'programs-edit', 'programs-show') ? 'active' : '' }}">
                                        <i class='bx bxs-school side-icon'></i>
                                        <span class="side-title">Programs</span>
                                    </a>
                                </li>
                                <li class="side-item">
                                    <a href="{{ route('employer-calendar') }}" class="side-item-link {{ request()->routeIs('employer-calendar') ? 'active' : '' }}">
                                        <i class='bx bx-calendar side-icon'></i>
                                        <span class="side-title">Calendar</span>
                                    </a>
                                </li>
                                @endif

                                <!-- <li><a href="#"><i class='bx bx-cog side-icon'></i><span class="side-title">Sponsor</span></a></li> -->
                            </ul>
                            <div class="sidebar-bottom">
                                <li class=""><a href="{{ url('/logout')}}" class="side-item-link"><i class='bx bx-log-out-circle side-icon'></i><span class="side-title">Logout</span></a></li>
                            </div>
                        </div>
                    </nav>
                </div>
            </div>
            <div class="grid-right">
                <div class="content-container nav-sticky mb-4">
                    <nav class="navbar border-bottom">
                        <div class="navbar-container">
                            <div>
                                <ul class="d-flex align-items-center">
                                    <li class="logo-container"><a href="#"><img class="logo" src="{{ asset('images/logo.png') }}" alt=""></a></li>
                                    <li class="nav-item "><a href="{{route('home')}}" class="{{ request()->routeIs('home') ? 'active' : '' }}">Home</a></li>
                                    @if (Auth::user()->hasRole('PWD'))
                                    <li class="nav-item "><a href="{{route('pwd-list-program')}}" class="{{ request()->routeIs('pwd-list-program', 'programs-show', 'training-details') ? 'active' : '' }}">Browse Training Programs</a></li>
                                    <li class="nav-item "><a href="{{route('pwd-list-job')}}" class="{{ request()->routeIs('pwd-list-job') ? 'active' : '' }}">Find Work</a></li>
                                    @endif
                                    @if (Auth::user()->hasRole('Sponsor'))
                                    <li class="nav-item "><a href="{{route('list-of-tp')}}" class="{{ request()->routeIs('list-of-tp', 'programs-show', 'training-details') ? 'active' : '' }}">Browse Training Programs</a></li>
                                    <li class="nav-item "><a href="">Find Work</a></li>
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
                <div class="scroll-top">
                    <a href="#"><i class='bx bx-chevron-up'></i></a>
                </div>
                <div class="content-container page-content">
                    @yield('page-content')
                </div>
                @include('slugs.footer')
            </div>
            @else
            <div class="">
                @yield('auth-content')
                @if(Route::currentRouteName() == 'landing')
                @include('slugs.footer')
                @endif
            </div>
            @endif
        </div>
        @stack('map-scripts')
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
                            var url = notification.data.url || '#';
                            var notificationId = notification.id;

                            if (notification.type === 'App\\Notifications\\NewTrainingProgramNotification') {
                                notificationContent = '<li><a class="dropdown-item" href="' + notification.data.url + '">' +
                                    '<span class="notif-owner text-cap">' +
                                    notification.data.agency_name +
                                    '</span>' +
                                    ' has posted a new training!' +
                                    '<div class="notif-content sub-text">' +
                                    'Entitled ' +
                                    '<span class="sub-text text-cap">' +
                                    notification.data.title +
                                    '</span>' +
                                    '. Click to check this out.' +
                                    '</div>' +
                                    '</a></li>'
                            } else if (notification.type === 'App\\Notifications\\PwdApplicationNotification') {
                                notificationContent = '<li><a class="dropdown-item" href="' + notification.data.url + '">' +
                                    'A PWD user has applied for your training program: ' +
                                    '<span class="notif-owner text-cap">' +
                                    notification.data.title +
                                    '</span>' +
                                    '<div class="notif-content sub-text">' +
                                    'Click to view application.' +
                                    '</div>' +
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
                                    '</div>' +
                                    '</a></li>';
                            } else if (notification.type === 'App\\Notifications\\TrainingCompletedNotification') {
                                notificationContent = '<li><a class="dropdown-item" href="' + notification.data.url + '">' +
                                    'Congratulations for completing: ' +
                                    '<span class="notif-owner text-cap">' +
                                    notification.data.program_title +
                                    '</span>' +
                                    '<div class="notif-content sub-text">' +
                                    ' You have been given a certificate, see profile. ' +
                                    '</div>' +
                                    '</a></li>';
                            } else if (notification.type === 'App\\Notifications\\NewJobListingNotification') {
                                notificationContent = '<li><a class="dropdown-item" href="' + notification.data.url + '">' +
                                    'New Job Listing: ' +
                                    '<span class="notif-owner text-cap">' +
                                    notification.data.position +
                                    '</span>' +
                                    '<div class="notif-content sub-text">' +
                                    ' Click to view details. ' +
                                    '</div>' +
                                    '</a></li>';
                            } else if (notification.type === 'App\\Notifications\\JobApplicationAcceptedNotification') {
                                notificationContent = '<li><a class="dropdown-item" href="' + notification.data.url + '">' +
                                    'Accepted by Employer: ' +
                                    '<span class="notif-owner text-cap">' +
                                    notification.data.employer +
                                    '</span>' +
                                    '<div class="notif-content sub-text">' +
                                    ' You have ' +
                                    '</div>' +
                                    '</a></li>';
                            } else if (notification.type === 'App\\Notifications\\PwdJobApplicationNotification') {
                                notificationContent = '<li><a class="dropdown-item" href="' + notification.data.url + '">' +
                                    'New applicant for: ' +
                                    '<span class="notif-owner text-cap">' +
                                    notification.data.position +
                                    '</span>' +
                                    '<div class="notif-content sub-text">' +
                                    ' Review it now. ' +
                                    '</div>' +
                                    '</a></li>';
                            } else if (notification.type === 'App\\Notifications\\SponsorDonationNotification') {
                                notificationContent = '<li><a class="dropdown-item" href="' + notification.data.url + '">' +
                                    'A sponsor has contributed: ' +
                                    '<span class="notif-owner text-cap">' +
                                    notification.data.amount +
                                    '</span>' +
                                    '<div class="notif-content sub-text">' +
                                    ' to your program: ' +
                                    notification.data.program_title +
                                    '</div>' +
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

            // Handle clicking on a notification to mark it as read
            $(document).on('click', '.dropdown-item', function() {
                var notificationId = $(this).data('id'); // Get notification ID from the data-id attribute

                // Mark as read via AJAX
                $.ajax({
                    url: "{{ route('notifications.markAsRead') }}", // Your route for marking notifications as read
                    method: "POST",
                    data: {
                        _token: "{{ csrf_token() }}",
                        notification_id: notificationId
                    },
                    success: function(response) {
                        if (response.status === 'success') {
                            // Update the badge count dynamically
                            $('#notification-badge').text(response.unread_count);
                            if (response.unread_count === 0) {
                                $('#notification-badge').addClass('d-none');
                            }

                            // You can also update the UI to indicate the notification was read
                            // You could either remove it from the list or mark it differently
                            // For example:
                            $('a[data-id="' + notificationId + '"]').css('color', 'gray');
                        }
                    },
                    error: function() {
                        console.error('Error marking notification as read');
                    }
                });
            });

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

        window.addEventListener('scroll', function() {
            var scrollButton = document.querySelector('.scroll-top');
            if (window.scrollY > 100) { // Show when scrolled down 100px
                scrollButton.classList.add('visible');
            } else {
                scrollButton.classList.remove('visible');
            }
        });
    </script>

    </html>