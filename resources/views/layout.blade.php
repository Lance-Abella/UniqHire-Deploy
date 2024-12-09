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
        @if(session('success'))
        <script>
            Swal.fire({
                title: "Success",
                text: "{{session('success')}}",
                icon: "success",
                timer: 4000,
            });
        </script>
        @endif
        @if (session('error'))
        <script>
            Swal.fire({
                title: "Error",
                text: "{{session('error')}}",
                icon: "error",
                timer: 4000,
            });
        </script>
        @endif
        @if (session('info'))
        <script>
            Swal.fire({
                title: "Info",
                text: "{{session('info')}}",
                icon: "info",
                timer: 4500,
            });
        </script>
        @endif
        @if(session('logged-in'))
        <script>
            const Toast = Swal.mixin({
                toast: true,
                position: "top-end",
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true,
                didOpen: (toast) => {
                    toast.onmouseenter = Swal.stopTimer;
                    toast.onmouseleave = Swal.resumeTimer;
                }
            });


            Toast.fire({
                icon: "success",
                title: "{{ session('logged-in') }}"
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
                                @if (!(Auth::user()->hasRole('Admin')))
                                <li class="side-item">
                                    <a href="{{ route('profile')}}" class="side-item-link {{ request()->routeIs('profile') ? 'active' : '' }}">
                                        <i class='bx bx-user-circle side-icon'></i>
                                        <span class="side-title">Profile</span>
                                    </a>
                                </li>
                                @endif
                                <!-- PWD ROLE ACCESS -->
                                @if (Auth::user()->hasRole('PWD'))
                                <li class="side-item">
                                    <a href="{{route('trainings')}}" class="side-item-link trainings-drop {{ request()->routeIs('trainings', 'show-details') ? 'active' : '' }}">
                                        <i class='bx bxs-school side-icon'></i>
                                        <span class="side-title">Trainings</span>
                                    </a>
                                </li>
                                <li class="side-item">
                                    <a href="{{route('jobs')}}" class="side-item-link trainings-drop {{ request()->routeIs('jobs', 'show-job-details') ? 'active' : '' }}">
                                        <i class='bx bx-briefcase-alt-2 side-icon'></i>
                                        <span class="side-title">Jobs</span>
                                    </a>
                                </li>
                                <li class="side-item">
                                    <a href="{{ route('pwd-calendar') }}" class="side-item-link {{ request()->routeIs('pwd-calendar') ? 'active' : '' }}">
                                        <i class='bx bx-calendar side-icon'></i>
                                        <span class="side-title">Calendar</span>
                                    </a>
                                </li>
                                <!-- <li class="side-item">
                                    <a href="#" class="side-item-link">
                                        <i class='bx bx-cog side-icon'></i>
                                        <span class="side-title">Settings</span>
                                    </a>
                                </li> -->

                                @endif

                                <!-- ADMIN ROLE ACCESS -->
                                @if (Auth::user()->hasRole('Admin'))
                                <li class="side-item"><a href="{{route('pwd-list')}}" class="side-item-link {{ request()->routeIs('pwd-list') ? 'active' : '' }}">
                                        <i class='bx bx-handicap side-icon'></i>
                                        <span class="side-title">PWDs</span>
                                    </a></li>
                                <li class="side-item"><a href="{{route('trainer-list')}}" class="side-item-link {{ request()->routeIs('trainer-list') ? 'active' : '' }}">
                                        <i class='bx bxs-school side-icon'></i>
                                        <span class="side-title">Agencies</span>
                                    </a></li>
                                <li class="side-item"><a href="{{route('employee-list')}}" class="side-item-link {{ request()->routeIs('employee-list') ? 'active' : '' }}">
                                        <i class='bx bx-briefcase-alt-2 side-icon'></i>
                                        <span class="side-title">Employers</span>
                                    </a></li>
                                <li class="side-item"><a href="{{route('sponsor-list')}}" class="side-item-link {{ request()->routeIs('sponsor-list') ? 'active' : '' }}">
                                        <i class='bx bx-dollar-circle side-icon'></i>
                                        <span class="side-title">Sponsors</span>
                                    </a></li>
                                <li class="side-item"><a href="{{route('skill-list')}}" class="side-item-link {{ request()->routeIs('skill-list', 'skill-edit') ? 'active' : '' }}">
                                        <i class='bx bxs-brain side-icon'></i>
                                        <span class="side-title">Skills</span>
                                    </a></li>
                                <li class="side-item"><a href="{{route('disability-list')}}" class="side-item-link {{ request()->routeIs('disability-list', 'disability-edit') ? 'active' : '' }}">
                                        <i class='bx bx-body side-icon'></i>
                                        <span class="side-title">Disabilities</span>
                                    </a></li>
                                <li class="side-item"><a href="{{route('social-list')}}" class="side-item-link {{ request()->routeIs('social-list', 'social-edit') ? 'active' : '' }}">
                                        <i class='bx bx-globe side-icon'></i>
                                        <span class="side-title">Socials</span>
                                    </a></li>
                                <!-- <li class="side-item"><a href="" class="side-item-link">
                                        <i class='bx bx-cog side-icon'></i>
                                        <span class="side-title">Settings</span>
                                    </a></li> -->
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
                                    <a href="{{route('manage-jobs')}}" class="side-item-link {{ request()->routeIs('manage-jobs', 'jobs-show', 'set-schedule') ? 'active' : '' }}">
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

                                @if (Auth::user()->hasRole('Sponsor'))
                                <li class="side-item">
                                    <a href="{{route('payment-history')}}" class="side-item-link {{ request()->routeIs('payment-history') ? 'active' : '' }}">
                                        <i class='bx bx-history side-icon'></i>
                                        <span class="side-title">Transactions</span>
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
                                    <li class="nav-item "><a href="{{route('pwd-list-job')}}" class="{{ request()->routeIs('pwd-list-job', 'job-details') ? 'active' : '' }}">Find Work</a></li>
                                    <li class="nav-item "><a href="{{route('events')}}" class="{{ request()->routeIs('events') ? 'active' : '' }}">Events</a></li>
                                    @endif
                                    @if (Auth::user()->hasRole('Employer'))
                                    <li class="nav-item "><a href="{{route('post-events')}}" class="{{ request()->routeIs('show-post-events') ? 'active' : '' }}">Events</a></li>
                                    @endif
                                    @if (Auth::user()->hasRole('Sponsor'))
                                    <li class="nav-item "><a href="{{route('list-of-tp')}}" class="{{ request()->routeIs('list-of-tp', 'programs-show', 'training-details') ? 'active' : '' }}">Browse Training Programs</a></li>
                                    @endif

                                    <!-- <li class="nav-item"><a href="{{ route('home') }}/#about" class="">About</a></li> -->

                                </ul>
                            </div>
                            <div>
                                <ul class="d-flex align-items-center">
                                    <li class="nav-item user-notif dropdown unread">

                                        <a href="#" class="dropdown-toggle dropdown-item" id="notificationDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                                            <i class='bx bxs-inbox' title="Notifications"></i>
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
                        Add skill/s to your profile to get better recommendations or Enroll to a training program. <a href="{{route('profile')}}">Check and Edit Profile.</a>
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
                    var notifDropdown = $('#notificationDropdown').next('.dropdown-menu');
                    var badge = $('#notification-badge');
                    notifDropdown.empty();

                    if (data.length > 0) {
                        const unreadCount = data.filter(notification => !notification.read).length;
                        if (unreadCount > 0) {
                            badge.removeClass('d-none').text(unreadCount);
                        } else {
                            badge.addClass('d-none');
                        }

                        data.forEach(function(notification) {
                            var notificationContent = '';
                            var url = notification.data.url || '#';
                            var id = notification.id;

                            // Add read/unread class to the notification item
                            var readClass = notification.read ? 'notification-read' : 'notification-unread';

                            if (notification.type === 'App\\Notifications\\NewTrainingProgramNotification') {
                                notificationContent = '<li><a class="dropdown-item ' + readClass + '" href="' + notification.data.url + '">' +
                                    '<span class="notif-owner text-cap">' +
                                    notification.data.agency_name +
                                    '</span>' +
                                    ' has posted a new training' +
                                    '<div class="notif-content sub-text">' +
                                    'entitled ' +
                                    '<span class="sub-text text-cap">' +
                                    notification.data.title +
                                    '</span>' +
                                    '. Click to check this out.' +
                                    '</div>' +
                                    '</a></li>'
                            } else if (notification.type === 'App\\Notifications\\PwdApplicationNotification') {
                                notificationContent = '<li><a class="dropdown-item ' + readClass + '" href="' + notification.data.url + '">' +
                                    'New Application for ' +
                                    '<span class="notif-owner text-cap">' +
                                    notification.data.title +
                                    '</span>' +
                                    '<div class="notif-content sub-text">' +
                                    'Click here to review application.' +
                                    '</div>' +
                                    '</a></li>';
                            } else if (notification.type === 'App\\Notifications\\ApplicationAcceptedNotification') {
                                notificationContent = '<li><a class="dropdown-item ' + readClass + '" href="' + notification.data.url + '">' +
                                    'Your application for ' +
                                    '<span class="notif-owner text-cap">' +
                                    notification.data.program_title +
                                    '</span>' +
                                    '<div class="notif-content sub-text">' +
                                    ' has been accepted by ' +
                                    '<span class="sub-text text-cap">' +
                                    notification.data.agency_name +
                                    '</span>' +
                                    '. Click to view details.' +
                                    '</div>' +
                                    '</a></li>';
                            } else if (notification.type === 'App\\Notifications\\TrainingCompletedNotification') {
                                notificationContent = '<li><a class="dropdown-item ' + readClass + '" href="' + notification.data.url + '">' +
                                    "You've completed a training, " +
                                    '<span class="notif-owner text-cap">' +
                                    notification.data.program_title +
                                    '</span>' + ' completed ' +
                                    '<div class="notif-content sub-text">' +
                                    'Your certificate is now available in your profile.' +
                                    '</div>' +
                                    '</a></li>';
                            } else if (notification.type === 'App\\Notifications\\NewJobListingNotification') {
                                notificationContent = '<li><a class="dropdown-item ' + readClass + '" href="' + notification.data.url + '">' +
                                    '<span class="notif-owner text-cap">' +
                                    notification.data.employer +
                                    '</span>' +
                                    ' is now hiring ' +
                                    '<div class="notif-content sub-text">' +
                                    "They're looking for a " +
                                    '<span class="sub-text text-cap">' + notification.data.position + '</span>' +
                                    '. Click to view details. ' +
                                    '</div>' +
                                    '</a></li>';
                            } else if (notification.type === 'App\\Notifications\\JobApplicationAcceptedNotification') {
                                notificationContent = '<li><a class="dropdown-item ' + readClass + '" href="' + notification.data.url + '">' +
                                    '<span class="notif-owner text-cap">' +
                                    notification.data.employer +
                                    '</span>' + ' has accepted your application for ' + '<span class="notif-owner text-cap">' + notification.data.position + '</span>' + '<div class="notif-content sub-text">' +
                                    "Your interview will be scheduled soon." +
                                    '</div>' +
                                    '</a></li>';
                            } else if (notification.type === 'App\\Notifications\\PwdJobApplicationNotification') {
                                notificationContent = '<li><a class="dropdown-item ' + readClass + '" href="' + notification.data.url + '">' +
                                    'New applicant has applied for the position of ' +
                                    '<span class="notif-owner text-cap">' +
                                    notification.data.position +
                                    '</span>' +
                                    '<div class="notif-content sub-text">' +
                                    ' Click here to review the application. ' +
                                    '</div>' +
                                    '</a></li>';
                            } else if (notification.type === 'App\\Notifications\\SponsorDonationNotification') {
                                notificationContent = '<li><a class="dropdown-item ' + readClass + '" href="' + notification.data.url + '">' +
                                    'New donation for  ' +
                                    '<span class="notif-owner text-cap">' +
                                    notification.data.program_title +
                                    '</span>' +
                                    '<div class="notif-content sub-text">' + '<span class="sub-text text-cap">' + notification.data.donor + '</span>' +
                                    +' has donated ' + '<span class="sub-text text-cap">' + notification.data.amount + '</span>' +
                                    '</div>' +
                                    '</a></li>';
                            } else if (notification.type === 'App\\Notifications\\JobHiredNotification') {
                                notificationContent = '<li><a class="dropdown-item ' + readClass + '" href="' + notification.data.url + '">' +
                                    'You have been hired by ' +
                                    '<span class="notif-owner text-cap">' +
                                    notification.data.title +
                                    '</span>' + ' for ' + notification.data.position +
                                    '<div class="notif-content sub-text">' +
                                    'Best of luck in your new role!' +
                                    '</div>' +
                                    '</a></li>';
                            } else if (notification.type === 'App\\Notifications\\SetScheduleNotification') {
                                notificationContent = '<li><a class="dropdown-item ' + readClass + '" href="' + notification.data.url + '">' +
                                    'Interview schedule with ' +
                                    '<span class="notif-owner text-cap">' +
                                    notification.data.title +
                                    '</span>' + 'is set' +
                                    '<div class="notif-content sub-text">' +
                                    'on ' + '<span class="sub-text text-cap">' + notification.data.date + '</span>' +
                                    ' starts at ' + '<span class="sub-text text-cap">' + notification.data.time + '</span>' +
                                    '</div>' +
                                    '</a></li>';
                            } else if (notification.type === 'App\\Notifications\\SetEventsNotification') {
                                notificationContent = '<li><a class="dropdown-item ' + readClass + '" href="' + notification.data.url + '">' +
                                    'New event has been posted by ' +
                                    '<span class="notif-owner text-cap">' +
                                    notification.data.owner +
                                    '</span>' +
                                    '<div class="notif-content sub-text">' +
                                    'entitled ' + '<span class="sub-text text-cap">' + notification.data.title + '</span>' +
                                    '. Click to view details' +
                                    '</div>' +
                                    '</a></li>';
                            }
                            notificationContent = notificationContent.replace(
                                '<li>',
                                `<li data-notification-id="${notification.id}">`
                            );
                            notifDropdown.append(notificationContent);
                        });
                    } else {
                        badge.addClass('d-none');
                        notifDropdown.append('<li><span class="dropdown-item no-notif">No notifications</span></li>');
                    }
                }).fail(function() {
                    console.error('Failed to fetch notifications');
                });
            }

            // Modified click handler for notifications
            $('#notificationDropdown').next('.dropdown-menu').on('click', 'a', function(e) {
                e.preventDefault();
                const notificationItem = $(this).closest('li');
                const id = notificationItem.data('notification-id');

                $.post('/notifications/mark-as-read', {
                        _token: '{{ csrf_token() }}',
                        id: id
                    })
                    .done(function(response) {
                        if (response.status === 'success') {
                            // Update badge count
                            const unreadCount = response.unread_count;
                            const badge = $('#notification-badge');

                            if (unreadCount > 0) {
                                badge.text(unreadCount).removeClass('d-none');
                            } else {
                                badge.addClass('d-none');
                            }

                            // Navigate to the notification URL
                            window.location.href = $(e.target).closest('a').attr('href');
                        }
                    })
                    .fail(function(error) {
                        console.error('Error marking notification as read:', error);
                        // Still navigate even if marking as read fails
                        window.location.href = $(e.target).closest('a').attr('href');
                    });
            });

            // Initial fetch
            fetchNotifications();

            // Fetch on dropdown show
            $('#notificationDropdown').on('show.bs.dropdown', fetchNotifications);
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