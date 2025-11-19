<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Default</title>
    @vite(['resources/css/default.css', 'resources/js/app.js'])
</head>
<body>
    <div class="layout-main-container">
        <div class="layout-main-box">

            <div class="sidebar-main-container">
                <div class="logo-header-container">
                    <img class="logo-image" src="{{ asset('system_images/icon.png') }}" alt="LOGO">
                    <h2 class="logo">CKCM Network</h2>
                    <span>v1.2025.1</span>
                </div>
                <div class="user-main-container">
                    <div class="profile-container" onclick="toggleProfileModification()">
                        <img class="user-image" src="{{ asset('system_images/user.png') }}" alt="LOGO">
                        <div class="user">
                            <h3 class="user-header">
                                Hondrada John Mark
                            </h3>
                            <span class="id-s-header">
                                ID#: 131033
                            </span>
                        </div>
                    </div>
                    <div class="profile-modifacation-container">
                        <div class="profile-box-container">
                            <div class="modification-box">
                                <span>Signed in as</span>
                                <p>johnhondrada@gmail.com</p>
                            </div>
                            <div class="userLinks">
                                <a href="#">
                                    <div class="box">
                                        <span>Profile Modification</span>
                                    </div>
                                </a>
                                <div class="box">
                                    <form method="POST" action="{{('logout')}}">
                                        @csrf
                                        <button type="submit" class="logout-btn">
                                            <span>Sign Out</span>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="overview">
                        <a href="#" class="{{ Request::is('/') ? 'active' : '' }}">
                            <div class="overview-box">
                                <span>School Overview</span>
                            </div>
                        </a>
                    </div>
                    <div class="manage-container">
                        <span class="manageHeader">Manage</span>

                        <div class="manage-link-container">
                        
                        
                            <div class="manage-links">
                                <a href="my_grades">
                                    <span>My Grades</span>
                                </a>
                            </div>
                           
                            
                            <div class="manage-links">
                                <a href="{{ url('/my_class') }}" class="#">
                                    <span>My Classes</span>
                                </a>
                            </div>



                            <div class="manage-links">
                                <a href="#">
                                    <span>Student List</span>
                                </a>
                            </div>

                            <div class="manage-links">
                                <a href="#">
                                    <span>Grading & Scores</span>
                                </a>
                            </div>

                            <div class="manage-links">
                                <a href="#">
                                    <span>Student Grades</span>
                                </a>
                            </div>

                         
                            <div class="manage-links">
                                <a href="{{ url('/classes') }}">
                                    <span>All Classes</span>
                                </a>
                            </div>
                  


                            <div class="manage-links">
                                <a href="{{ url('/my_class_archive') }}">
                                    <span>My Class Archived</span>
                                </a>
                            </div>

                            <span class="manageHeader">Settings</span>

                            <div class="manage-links">
                                <a href="{{ url('/courses') }}">
                                    <span>Course</span>
                                </a>
                            </div>

                            <div class="manage-links">
                                <a href="{{ url('/departments') }}">
                                    <span>Department</span>
                                </a>
                            </div>

                            <div class="manage-links">
                                <a href="{{ url('/users') }}">
                                    <span>Users</span>
                                </a>
                            </div>
                
                        </div>

                    </div>
                </div>
            </div>

            <div class="rightbar-main-container">
                <div class="right-main-box">
                    <div class="navbar">
                        <div class="menu">
                            <img src="{{ asset('icons/bars-sort.png') }}" alt="Menu" class="menu-image">

                        </div>
                        <div class="z-notif">
                            <img src="{{ asset('icons/zoom.png') }}" alt="Zoom" class="zoom-image">
                            <img src="{{ asset('icons/bell.png') }}" alt="Notification" class="notif-image">
                        </div>
                    </div>
                    <div class="notif-box-container">
                        <div class="notif-box"></div>
                    </div>
                    <!-- Your page content below -->
                     @yield('content')
                </div>
            </div>
        </div>
    </div>
<script>
        function toggleProfileModification() {
            const modificationContainer = document.querySelector('.profile-modifacation-container');
            
            // toggle show/hide
            if (modificationContainer.style.display === "none" || modificationContainer.style.display === "") {
                modificationContainer.style.display = "block";
            } else {
                modificationContainer.style.display = "none";
            }
        }
</script>
</body>
</html>