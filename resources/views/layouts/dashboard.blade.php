<!DOCTYPE html>
<html lang="en" dir="ltr" data-nav-layout="vertical" data-theme-mode="light" data-header-styles="light"
    data-menu-styles="dark" data-toggled="close">

<head>
    <!-- Meta Data -->
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Easy Verifications for your Business" />
    <meta name="keywords" content="NIMC, BVN, ZEPA, Verification, Airtime,Data,Bills, Identity">
    <meta name="author" content="Zepa Developers">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>ZEPA Solutions - @yield('title') </title>
    <!-- fav icon -->
    <link rel="icon" href="{{ asset('assets/home/images/favicon/favicon.ico') }}" type="image/x-icon">
    <!-- Bootstrap Css -->
    <link id="style" href="{{ asset('assets/libs/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet">
    <!-- Style Css -->
    <link href="{{ asset('assets/css/styles.min.css') }}" rel="stylesheet">
    <!-- Icons Css -->
    <link href="{{ asset('assets/css/icons.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('assets/css/custom3.css') }}">
    @yield('page-css')
</head>

<body>
    <!-- start preLoader -->
    <div id="preloader">
        <span class="loader"></span>
    </div>
    <!-- end preLoader -->

    @yield('content')

    <!-- End::app-content -->
    <!-- Footer Start -->
    <footer class="footer mt-auto py-3 bg-white text-center">
        @include('components.footer')
    </footer>
    <!-- Footer End -->

    <!-- Scroll To Top -->
    <div class="scrollToTop">
        <span class="arrow"><i class="ri-arrow-up-s-fill fs-20"></i></span>
    </div>
    <div id="responsive-overlay"></div>
    <!-- Scroll To Top -->
    <script src="{{ asset('assets/kyc/js/jquery-3.7.1.min.js') }}"></script>
    <!-- Popper JS -->
    <script src="{{ asset('assets/libs/@popperjs/core/umd/popper.min.js') }}"></script>
    <!-- Bootstrap JS -->
    <script src="{{ asset('assets/libs/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <!-- Defaultmenu JS -->
    <script src="{{ asset('assets/js/defaultmenu.min.js') }}"></script>
    <!-- Sticky JS -->
    <script src="{{ asset('assets/js/sticky.js') }}"></script>
    <!-- Custom JS -->
    @yield('page-js')
    <script src="{{ asset('assets/js/config.js') }}"></script>
    <script src="{{ asset('assets/js/logout.js') }}"></script>

</body>

</html>
