<!DOCTYPE html>
<html lang="en" dir="ltr" data-nav-layout="vertical" data-theme-mode="light" data-header-styles="light" data-menu-styles="dark" data-toggled="close">

<head>
  <!-- Meta Data -->
  <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="description" content="Easy Verifications for your Business" />
  <meta name="keywords" content="NIMC, BVN, ZEPA, Verification, Airtime,Bills, Identity">
  <meta name="author" content="Zepa Developers">
  <title>ZEPA Solutions - Services </title>
  <!-- fav icon -->
  <link rel="icon" href="{{ asset('assets/home/images/favicon/favicon.png') }}" type="image/x-icon">
  <!-- Bootstrap Css -->
  <link id="style" href="{{ asset('assets/libs/bootstrap/css/bootstrap.min.css')}}" rel="stylesheet">
  <!-- Style Css -->
  <link href="{{ asset('assets/css/styles.min.css') }}" rel="stylesheet">
  <!-- Icons Css -->
  <link href="{{ asset('assets/css/icons.css') }}" rel="stylesheet">
  <link rel="stylesheet" href="{{ asset('assets/css/custom3.css')}}">
  <style>
   @media (max-width: 576px) {
    .custom-margin-top {
        margin-top: -400px !important; /* Adjust the value as needed */

    }
}

  </style>
</head>

<body>
  <!-- start preLoader -->
  <div id="preloader">
    <span class="loader"></span>
  </div>
  <!-- end preLoader -->
  <!-- Loader -->
  <div class="page">
    <!-- app-header -->
    <header class="app-header">
      <!-- Start::main-header-container -->
      <div class="main-header-container container-fluid">
        <!-- Start::header-content-left -->
        <div class="header-content-left">
          <!-- Start::header-element -->
          <div class="header-element">
            <div class="horizontal-logo">
              <a href="{{route('dashboard')}}" class="header-logo">
                <img src="{{ asset('assets/images/brand-logos/logo.png')}}" alt="logo" class="desktop-logo">
                <img src="{{ asset('assets/images/brand-logos/logo.png')}}" alt="logo" class="toggle-logo">
                <img src="{{ asset('assets/images/brand-logos/logo.png')}}" alt="logo" class="toggle-dark">
                <img src="{{ asset('assets/images/brand-logos/logo.png')}}" alt="logo" class="desktop-white">
                <img src="{{ asset('assets/images/brand-logos/logo.png')}}" alt="logo" class="toggle-white">
              </a>
            </div>
          </div>
          <!-- End::header-element -->
          <!-- Start::header-element -->
          <div class="header-element">
            <!-- Start::header-link -->
            <a aria-label="Hide Sidebar" class="sidemenu-toggle header-link animated-arrow hor-toggle horizontal-navtoggle" data-bs-toggle="sidebar" href="javascript:void(0);"><span></span></a>
            <!-- End::header-link -->
          </div>
          <!-- End::header-element -->
        </div>
        <!-- End::header-content-left -->
        <!-- Start::header-content-right -->
        <div class="header-content-right">
          <!-- End::header-element -->
          <!-- Start::header-element -->
          <div class="header-element notifications-dropdown">
            <!-- Start::header-link|dropdown-toggle -->
            <a href="javascript:void(0);" class="header-link dropdown-toggle" data-bs-toggle="dropdown" data-bs-auto-close="outside" id="messageDropdown" aria-expanded="false">
              <i class="bx bx-bell header-link-icon"></i>
              <span class="badge bg-danger rounded-pill header-icon-badge pulse pulse-secondary" id="notification-icon-badge">{{$notifycount}}</span>
            </a>
            <!-- End::header-link|dropdown-toggle -->
            <!-- Start::main-header-dropdown -->
            <div class="main-header-dropdown dropdown-menu dropdown-menu-end" data-popper-placement="none">
              <div class="p-3">
                <div class="d-flex align-items-center justify-content-between">
                  <p class="mb-0 fs-17 fw-semibold">Notifications</p>
                  <span class="badge bg-danger-transparent" id="notifiation-data">{{$notifycount}} Unread</span>
                </div>
              </div>
              <div class="dropdown-divider"></div>
              <ul class="list-unstyled mb-0" id="header-notification-scroll">
                @if($notifycount != 0)
                <audio src="{{ asset('assets/audio/notification.mp3')}}" autoplay="autoplay"></audio>
                @endif
                @if($notifications->count() != 0)
                @foreach($notifications as $data)
                <li class="dropdown-item">
                  <div class="d-flex align-items-start">
                    <div class="pe-2">
                      @if($data->message_title == 'Account Has Been Verified')
                      <span class="avatar avatar-md bg-primary-transparent avatar-rounded"><i class="ti ti-user-check fs-18"></i></span>
                      @else
                      <span class="avatar avatar-md bg-primary-transparent avatar-rounded"><i class="ti ti-bell fs-18"></i></span>
                      @endif
                    </div>
                    <div class="flex-grow-1 d-flex align-items-center justify-content-between">
                      <div>
                        <p class="mb-0 fw-semibold">{{$data->message_title}}</p>
                        <span class="text-muted fw-normal fs-12 header-notification-text">{{$data->messages}}</span>
                      </div>
                      <div>
                      </div>
                    </div>
                  </div>
                </li>
                @endforeach
                @else
                <div class="p-5 empty-item1">
                  <div class="text-center">
                    <span class="avatar avatar-xl avatar-rounded bg-secondary-transparent">
                      <i class="ri-notification-off-line fs-2"></i>
                    </span>
                    <h6 class="fw-semibold mt-3">No New Notifications</h6>
                  </div>
                </div>
                @endif
              </ul>
              @if($notifycount != 0)
              <div class="p-3 empty-header-item1 border-top">
                <div class="d-grid">
                  <a id="read" href="#" class="btn btn-primary">Mark as Read</a>
                  <p style="display:none" id="done" class="text-danger text-center">Marked Read</p>
                </div>
              </div>
              @endif
            </div>
            <!-- End::main-header-dropdown -->
          </div>
          <!-- End::header-element -->
          <!-- Start::header-element -->
          @include('components.header')
          <!-- End::header-element -->
        </div>
        <!-- End::header-content-right -->
      </div>
      <!-- End::main-header-container -->
    </header>
    <!-- /app-header -->
    <!-- Start::app-sidebar -->
    <aside class="app-sidebar sticky" id="sidebar">
      <!-- Start::main-sidebar-header -->
      <div class="main-sidebar-header">
        <a href="{{ route('dashboard') }}" class="header-logo">
          <img src="{{ asset('assets/images/brand-logos/logo.png')}}" alt="logo" class="desktop-logo">
          <img src="{{ asset('assets/images/brand-logos/logo.png')}}" alt="logo" class="desktop-dark">
          <img src="{{ asset('assets/images/brand-logos/logo.png')}}" alt="logo" class="toggle-dark">
        </a>
      </div>
      <!-- End::main-sidebar-header -->
      <!-- Start::main-sidebar -->
      <div class="main-sidebar" id="sidebar-scroll">
        <!-- Start::nav -->
        <nav class="main-menu-container nav nav-pills flex-column sub-open">
          <div class="slide-left" id="slide-left">
            <svg xmlns="http://www.w3.org/2000/svg" fill="#7b8191" width="24" height="24" viewBox="0 0 24 24">
              <path d="M13.293 6.293 7.586 12l5.707 5.707 1.414-1.414L10.414 12l4.293-4.293z"></path>
            </svg>
          </div>
          @php $title="dashboard"; $menu="dashboard"; @endphp
          @include('components.sidebar')
          <div class="slide-right" id="slide-right">
            <svg xmlns="http://www.w3.org/2000/svg" fill="#7b8191" width="24" height="24" viewBox="0 0 24 24">
              <path d="M10.707 17.707 16.414 12l-5.707-5.707-1.414 1.414L13.586 12l-4.293 4.293z"></path>
            </svg>
          </div>
        </nav>
        <!-- End::nav -->
      </div>
      <!-- End::main-sidebar -->
    </aside>
    <!-- End::app-sidebar -->
    <!-- Start::app-content -->
    <div class="main-content app-content custom-margin-top">
      <div class="container-fluid">

        <!-- End::page-header -->
        <!-- Start::row-1 d-none d-md-block-->
        <div class="row">
          <div class="col-xxl-9 col-xl-12">

            <div class="col-xl-12 ">
              <div class="row">
                <div class="col-xl-12">
                  <div class="card custom-card">
                    <div class="card-header justify-content-between">
                      <div class="card-title">
                        Our {{ ucwords($type)}} Services
                      </div>
                    </div>
                    <div class="card-body">
                      <div class="row ">
                       @if($type == 'data')

                        <div class="col-6 col-md-3 text-center  mt-2">
                          <a href="{{route('data')}}"><img class="img-fluid border rounded" width="30%" src="{{ asset('assets/images/data-bundle.jpeg') }}">
                            <p class=" rounded fw-bold mt-2">Data Bundle</p>
                          </a>
                        </div>

                         <div class="col-6 col-md-3 text-center  mt-2">
                          <a href="{{route('sme-data')}}"><img class="img-fluid border rounded" width="32%" src="{{ asset('assets/images/sme.png') }}">
                            <p class=" rounded fw-bold mt-2">SME Data Bundle</p>
                          </a>
                        </div>

                       @elseif ($type == 'verifications')
                        <div class="col-6 col-md-3 text-center  mt-2">
                          <a href="{{route('bvn')}}"> <img class="img-fluid  rounded" width="40%" src="{{ asset('assets/images/BVN.jpeg') }}">
                            <p class=" rounded fw-bold mt-2 ">BVN Verification</p>
                          </a>
                        </div>

                         <!-- <div class="col-6 col-md-3 text-center  mt-2">
                          <a href="{{route('bank')}}"> <img class="img-fluid  rounded" width="32%" src="{{ asset('assets/images/identity.png') }}">
                            <p class=" rounded fw-bold mt-2">Verify Bank Account</p>
                          </a>
                        </div>-->

                         <div class="col-6 col-md-3 text-center  mt-2">
                          <a href="{{route('nin')}}"> <img class="img-fluid  rounded" width="40%" src="{{ asset('assets/images/nimc.png') }}">
                            <p class=" rounded fw-bold mt-2">Verify NIN using NIN</p>
                          </a>
                        </div>

                         <div class="col-6 col-md-3 text-center  mt-2">
                          <a href="{{route('nin-phone')}}"> <img class="img-fluid  rounded" width="40%" src="{{ asset('assets/images/nimc.png') }}">
                            <p class=" rounded fw-bold mt-2">Verify NIN using Phone Number</p>
                          </a>
                        </div>

                         <!-- <div class="col-6 col-md-3 text-center  mt-2">
                          <a href="{{route('nin-vnin')}}"> <img class="img-fluid  rounded" width="40%" src="{{ asset('assets/images/nimc.png') }}">
                            <p class="rounded fw-bold mt-2">Verify NIN using VNIN</p>
                          </a>
                        </div>

                        <div class="col-6 col-md-3 text-center  mt-2">
                          <a href="{{route('nin-demo')}}"> <img class="img-fluid  rounded" width="40%" src="{{ asset('assets/images/nimc.png') }}">
                            <p class="mt-2 fw-bold ">Verify NIN using  Demographic</p>
                          </a>
                        </div>-->

                         @elseif ($type == 'agency')
                          <p class="text-danger">To access our agency services, please upgrade your account. Simply navigate to the dashboard page and submit an upgrade request. Our team will assist you with the next steps. Thank you for choosing our services!</p>
                        <div class="col-6 col-md-3 text-center  mt-2">
                          <a href="{{route('bvn-modification')}}"> <img class="img-fluid  rounded" width="60%" src="{{ asset('assets/images/bvn.jpg') }}">
                            <p class=" rounded fw-bold mt-2">BVN Modification</p>
                          </a>
                        </div>

                        <div class="col-6 col-md-3 text-center  mt-2">
                          <a href="{{route('crm')}}"> <img class="img-fluid rounded" width="60%" src="{{ asset('assets/images/bvn.jpg') }}">
                            <p class="fw-bold mt-2">CRM Request</p>
                          </a>
                        </div>

                        <div class="col-6 col-md-3 text-center  mt-2">
                          <a href="{{route('account-upgrade')}}"> <img class="img-fluid rounded" width="50%" src="{{ asset('assets/images/bank.jpg') }}">
                            <p class="fw-bold mt-2">Account Upgrade</p>
                          </a>
                        </div>

                         <div class="col-6 col-md-3 text-center  mt-2">
                          <a href="{{route('crm2')}}"> <img class="img-fluid rounded" width="60%" src="{{ asset('assets/images/bvn.jpg') }}">
                            <p class=" fw-bold mt-2">Find BVN Using Phone and DOB</p>
                          </a>
                        </div>

                          <div class="col-6 col-md-3 text-center  mt-2">
                          <a href="{{route('bvn-enrollment')}}"> <img class="img-fluid rounded" width="60%" src="{{ asset('assets/images/bvn.jpg') }}">
                            <p class="fw-bold ">BVN Enrollement <br/>Agency Request
                         </p>
                          </a>
                        </div>
                       @elseif ($type == 'funding')
                        <div class="col-6 col-md-3 text-center  mt-2">
                          <a href="{{route('funding')}}"><img class="img-fluid border rounded" width="35%" src="{{ asset('assets/images/fund_wallet.png') }}">
                            <p class=" rounded fw-bold mt-2">Wallet Funding</p>
                          </a>
                        </div>

                        <div class="col-6 col-md-3 text-center  mt-2">
                          <a href="{{route('p2p')}}"><img class="img-fluid border rounded" width="31%" src="{{ asset('assets/images/p2p.jpg') }}">
                            <p class=" rounded fw-bold mt-2">Wallet Transfer</p>
                          </a>
                        </div>

                        <div class="col-6 col-md-3 text-center  mt-2">
                          <a href="{{route('claim')}}"><img class="img-fluid border rounded" width="33%" src="{{ asset('assets/images/referral_bonus.jpg') }}">
                            <p class=" rounded fw-bold mt-2">Claim Bonus</p>
                          </a>
                        </div>
                       @endif

                      </div>
                    </div>
                  </div>
                </div>

              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <!-- End::row-1 -->
  </div>
  </div>
  <!-- End::app-content -->
  <!-- Footer Start -->
  <footer class="footer mt-auto py-3 bg-white text-center">
    @include('components.footer')
  </footer>
  <!-- Footer End -->
  </div>
  <!-- Scroll To Top -->
  <div class="scrollToTop">
    <span class="arrow"><i class="ri-arrow-up-s-fill fs-20"></i></span>
  </div>
  <div id="responsive-overlay"></div>
  <!-- Scroll To Top -->
  <script src="{{ asset('assets/kyc/js/jquery-3.7.1.min.js')}}"></script>
  <!-- Popper JS -->
  <script src="{{ asset('assets/libs/@popperjs/core/umd/popper.min.js')}}"></script>
  <!-- Bootstrap JS -->
  <script src="{{ asset('assets/libs/bootstrap/js/bootstrap.bundle.min.js')}}"></script>
  <!-- Defaultmenu JS -->
  <script src="{{ asset('assets/js/defaultmenu.min.js')}}"></script>
  <!-- Sticky JS -->
  <script src="{{ asset('assets/js/sticky.js')}}"></script>
  <!-- Custom JS -->
  <script src="{{ asset('assets/js/config.js') }}"></script>
  <script src="{{ asset('assets/js/logout.js') }}"></script>
</body>

</html>
