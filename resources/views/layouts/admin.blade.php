<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="{{ asset('assets/dashboard/css/style.css') }}">
</head>

<body class="font-sans antialiased">
    <div class="min-h-screen bg-gray-100">
        <div class="nk-app-root" data-sidebar-collapse="lg">
            <div class="nk-main" id="nk-main">
                <div class="nk-sidebar nk-sidebar-fixed" id="sidebar">
                    <div class="nk-compact-toggle">
                        <button onClick="toggleCompact()"
                            class="btn btn-xs btn-outline-light btn-icon compact-toggle text-light bg-white rounded-3"
                            id="icon_Click">
                            <em class="icon off ni ni-chevron-left"></em>
                        </button>
                    </div>
                    <div class="nk-sidebar-element nk-sidebar-head">
                        <div class="nk-sidebar-brand">
                            <a href="{{ route('dashboard') }}" class="logo-link">
                                <div class="logo-wrap">
                                    <img class="logo-img logo-light dashboardlogo" src="/logo.png" alt="" />
                                    <img class="logo-img logo-dark dashboardlogo" src="/logo.png" alt="" />
                                    <img class="logo-img logo-icon dashboardlogo" src="/logo.png" alt="" />
                                </div>
                            </a>
                        </div>
                    </div>
                    <div class="nk-sidebar-element nk-sidebar-body">
                        <div class="nk-sidebar-content h-100" data-simplebar>
                            <div class="nk-sidebar-menu">
                                <ul class="nk-menu">
                                    <li class="nk-menu-item">
                                        <a href="{{ route('dashboard') }}" class="nk-menu-link">
                                            <span class="nk-menu-icon">
                                                <em class="icon ni ni-dashboard-fill"></em>
                                            </span>
                                            <span class="nk-menu-text">Dashboard</span>
                                        </a>
                                    </li>
                                    <li class="nk-menu-item">
                                        <a href="{{ route('setting') }}" class="nk-menu-link">
                                            <span class="nk-menu-icon">
                                                <em class="icon ni ni-setting-fill"></em>
                                            </span>
                                            <span class="nk-menu-text">Setting</span>
                                        </a>
                                    </li>
                                    <li class="nk-menu-item">
                                        <form method="POST" action="{{ route('logout') }}">
                                            @csrf
                                            <a href="route('logout')"
                                                onclick="event.preventDefault();    this.closest('form').submit();"
                                                class="nk-menu-link">
                                                <span class="nk-menu-icon">
                                                    <em class="icon ni ni-signout"></em>
                                                </span>
                                                <span class="nk-menu-text">Sign Out</span>
                                            </a>

                                        </form>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="nk-sidebar-element nk-sidebar-footer">
                        <div class="nk-sidebar-footer-extended pt-3">
                            <div class="border border-light rounded-3">
                                <a class="d-flex px-3 py-2 bg-primary bg-opacity-10 rounded-bottom-3">
                                    <div class="media-group">
                                        <div className="media-text">
                                            <h6 className="fs-6 mb-0">{{ Auth::user()->name }}</h6>
                                            <span className="text-light fs-7">
                                                {{Auth::user()->email}}
                                            </span>
                                        </div>
                                        <em class="icon ni ni-chevron-right ms-auto ps-1"></em>
                                    </div>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="nk-wrap">
                    <div class="nk-header nk-header-fixed">
                        <div class="container-fluid">
                            <div class="nk-header-wrap">
                                <div class="nk-header-logo ms-n1">
                                    <div class="nk-sidebar-toggle me-1">
                                        <button onClick="toggleshowMobileNav()"
                                            class="btn btn-sm btn-zoom btn-icon sidebar-toggle d-sm-none">
                                            <em class="icon ni ni-menu"> </em>
                                        </button>
                                        <button onClick="toggleshowMobileNav()"
                                            class="btn btn-md btn-zoom btn-icon sidebar-toggle d-none d-sm-inline-flex">
                                            <em class="icon ni ni-menu"> </em>
                                        </button>
                                    </div>
                                    <a href="index-2.html" class="logo-link">
                                        <div class="logo-wrap">
                                            <img class="logo-img logo-light" src="/logo-dashboard.png"
                                                srcset="/logo-dashboard.png 2x" alt="" />
                                            <img class="logo-img logo-dark" src="/logo-dashboard.png"
                                                srcset="/logo-dashboard.png 2x" alt="" />
                                            <img class="logo-img logo-icon" src="/logo-dashboard.png"
                                                srcset="/logo-dashboard.png 2x" alt="" />
                                        </div>
                                    </a>
                                </div>
                                <div class="nk-header-tools">
                                    <ul class="nk-quick-nav ms-2">
                                        <li class="dropdown d-inline-flex">
                                            <a class="d-inline-flex" >
                                                <div class="media media-sm media-middle media-circle text-bg-primary">
                                                    <img class="chat-avatar" alt="" />
                                                </div>
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    {{ $slot }}
                    <div class="nk-footer">
                        <div class="container-xl">
                            <div class="d-flex align-items-center flex-wrap justify-content-between mx-n3">
                                <!-- 
                                <div class="nk-footer-links px-3">
                                        <ul class="nav nav-sm">
                                        <li class="nav-item">
                                            <a class="nav-link">
                                                Home
                                            </a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link">
                                                Pricing
                                            </a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link">
                                                Privacy Policy
                                            </a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link">
                                                Cookie Policy
                                            </a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link">
                                                Terms & Conditions
                                            </a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link">
                                                Disclaimer
                                            </a>
                                        </li>
                                    </ul> 
                                </div>
                                <div class="nk-footer-copyright fs-6 px-3">
                                    &copy; 2024 All Rights Reserved to
                                </div>
                            -->
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </div>
    <script src="{{ asset('assets/dashboard/js/bundle.js') }}"></script>
    <script src="{{ asset('assets/dashboard/js/script.js') }}"></script>
    <script>
        var showMobileNav = false;
        var isCompact = false;
        function toggleCompact() {
            var sidebar = document.getElementById('sidebar');
            var icon = document.getElementById('icon_Click');
            isCompact = !isCompact;
            if (isCompact) {
                sidebar.classList.add('is-compact');
                icon.classList.add('ni-chevron-right');
                icon.classList.remove('ni-chevron-left');
            } else {
                sidebar.classList.remove('is-compact');
                icon.classList.remove('ni-chevron-right');
                icon.classList.add('ni-chevron-left');
            }
        }

        function toggleshowMobileNav() {
            showMobileNav = !showMobileNav;
        console.log('clicked');
            var sidebar = document.getElementById('sidebar');
            var nkMain = document.getElementById('nk-main');
            if (showMobileNav) {
                nkMain.append('<div onClick="toggleshowMobileNav()" class="sidebar-overlay"></div>');
                sidebar.classList.add('sidebar-active');
            } else {
                sidebar.classList.remove('sidebar-active');
            }
        }
    </script>
</body>

</html>