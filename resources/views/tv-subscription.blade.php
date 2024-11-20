<!DOCTYPE html>
<html lang="en" dir="ltr" data-nav-layout="vertical" data-theme-mode="light" data-header-styles="light" data-menu-styles="dark" data-toggled="close">
   <head>
      <!-- Meta Data -->
      <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
      <meta http-equiv="X-UA-Compatible" content="IE=edge">
      <meta name="viewport" content="width=device-width, initial-scale=1.0">
      <meta name="description" content="Easy Verifications for your Business"/>
      <meta name="keywords" content="NIMC, BVN, ZEPA, Verification, Airtime,Data,Bills, Identity">
      <meta name="author" content="Zepa Developers">
      <title>ZEPA Solutions - TV Subscription </title>
      <!-- fav icon -->
      <link rel="icon" href="{{ asset('assets/home/images/favicon/favicon.png') }}" type="image/x-icon">
      <!-- Bootstrap Css -->
      <link id="style" href="{{ asset('assets/libs/bootstrap/css/bootstrap.min.css')}}" rel="stylesheet" >
      <!-- Style Css -->
      <link href="{{ asset('assets/css/styles.min.css') }}" rel="stylesheet" >
      <!-- Icons Css -->
      <link href="{{ asset('assets/css/icons.css') }}" rel="stylesheet" >
      <link rel="stylesheet" href="{{ asset('assets/css/custom3.css')}}">
      <style>
         .vertical-tabs-2 .nav-item .nav-link.active {
         background-color: #1a4082  !important;
         color: #fff;
         position: relative;
        }
        .vertical-tabs-2 .nav-item .nav-link.active::before {
            content: "";
            position: absolute;
            inset-inline-end: -.5rem;
            inset-block-start: 38%;
            transform: rotate(45deg);
            width: 1rem;
            height: 1rem;
            background-color: #3b5998 !important;
        }
        .vertical-tabs-2 .nav-item .nav-link {
	min-width: 7.5rem;
	max-width: 7.5rem;
	text-align: center;
	border: 1px solid var(--default-border);
	margin-bottom: .5rem;
	color: #fff;
	background-color: #fff;
}
 @media (max-width: 576px) {
    .custom-margin-top {
        margin-top: -100px !important; /* Adjust the value as needed */

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
                           <audio src="{{ asset('assets/audio/notification.mp3')}}" autoplay="autoplay" ></audio>
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
                              <a  id="read" href="#" class="btn btn-primary">Mark as Read</a>
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
                  @php $title="tv"; $menu="Utility"; @endphp
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
               <!-- Start::row-1 -->
               <div class="row">
                  <div class="col-xxl-12 col-xl-12">
                     <div class="row mt-3">
                        <div class="col-xl-5">
                           <div class="row ">
                              <div class="col-xl-12">
                                 <div class="card custom-card">
                                      <div class="card-header  justify-content-between">
                                       <div class="card-title">
                                         <i class="bi bi-tv"></i> TV Subscription
                                       </div>
                                    </div>
                                    <div class="card-body">

                                          <center>
                                             <img class="img-fluid" src="{{asset('assets/images/tv.jpeg')}}" width="45%">
                                          </center>
                                        <p  class="text-center">Enjoy affordable and quality entertainment with our cheap TV subscriptions.</p>

                                         <div class="row text-center">
                                         <div class="col-md-12">
                                           @if (session('success'))
                                             <div class="alert alert-success alert-dismissible fade show" role="alert">
                                                {{ session('success') }}
                                             </div>
                                          @endif

                                             @if (session('error'))
                                             <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                                {{ session('error') }}
                                             </div>
                                          @endif

                                           @if ($errors->any())
                                          <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                             <ul>
                                                @foreach ($errors->all() as $error)
                                                      <li>{{ $error }}</li>
                                                @endforeach
                                             </ul>
                                          </div>
                                         @endif

                                            <form name="buy-tv" method="POST" action="{{route('buypin')}}">
                                                @csrf
                                                <div class="mb-3 row">

                                                     <div class="col-md-12 mb-3">
                                                      <select name="service" id="service_id" class="form-select text-center" aria-label="Default select example">
                                                      <option  value="">Select Service</option>
                                                      <option  value="gotv">GOTV</option>
                                                      <option  value="dstv">DSTV</option>
                                                      <option  value="startimes">Startimes</option>
                                                       <option value="showmax">Showmax</option>
                                                     </select>
                                                    </div>
                                                            <div class="col-lg-8 col-12">
                                                                <p class="text-muted">Enter Your SmartCard Number</p>
                                                                <input type="text" id="smart-card-no" class="form-control text-center"/>
                                                            </div>
                                                           <div class="col-lg-4 col-12">
                                                              <p class="text-muted">&nbsp;</p>
                                                                <button type="button" id="verify" class="btn btn-primary col-md-12 btn-md">Verify</button>
                                                            </div>

                                                             <div class="col-lg-12 mt-3">
                                                                <p id="reciever"></p>
                                                            </div>

                                                    <div id="details" class="col-md-12 shadow-md mt-2 mb-2 border rounded d-none">
                                                        <span class="fw-bold">Smart Card Details</span>
                                                        <div class="row border border-bottom">
                                                            <div class="col text-end">Customer Name:</div>
                                                            <div class="col" id="customer_name"></div>
                                                        </div>
                                                         <div class="row border border-bottom">
                                                            <div class="col text-end">Due Date:</div>
                                                            <div class="col" id="due_date"></div>
                                                        </div>
                                                         <div class="row border border-bottom">
                                                            <div class="col text-end">Current Bouquet:</div>
                                                            <div class="col" id="bouquet"></div>
                                                        </div>
                                                         <div class="row">
                                                            <div class="col text-end">Renewal Amount:</div>
                                                            <div class="col" id="formated_amount"></div>
                                                            <span hidden id="renew_amount"></span>
                                                        </div>
                                                    </div>

                                                     <div id="bouquet_type" class="col-md-12 mt-3 d-none">
                                                      <select name="type" id="type" class="form-select text-center" aria-label="Default select example">
                                                       <option value="">Proceed</option>
                                                       <option value="change">New/Change Bouquet</option>
                                                       <option value="renew">Renew</option>
                                                      </select>
                                                    </div>

                                                    <div id="bouquet_plan" class="col-md-12 mt-3 d-none">
                                                        <select name="plan" id="plan" class="form-select text-center" aria-label="Default select example">
                                                        <option value="">Choose Plan</option>
                                                        </select>
                                                    </div>

                                                    <div class="meta d-none">
                                                    <div class="col-md-12 mt-2">
                                                        <p class="mb-2 text-muted">Amount To Pay</p>
                                                        <input type="text" id="amountToPay" readonly value="" class="form-control text-center"/>
                                                    </div>
                                                     <div class="col-md-12 mt-2">
                                                        <p class="mb-0 text-muted">Phone Number</p>
                                                        <input type="text" id="mobileno" name="mobileno" value="" class="form-control phone text-center" maxlength="11" required/>
                                                      </div>

                                                <div class="col-md-12 mt-2">
                                                  <button type="submit" id="buy" class="btn btn-primary"><i class="las la-shopping-cart"></i> Buy Subscription</button>
                                                </div>

                                                 </div>
                                                </div>
                                            </form>
                                         </div>

                                         </div>
                                    </div>
                                 </div>
                              </div>

                           </div>
                        </div>
                        <div class="col-xl-7 d-none d-md-block">
                            <div class="card custom-card">
                                <div class="card-header justify-content-between">
                                     <div class="card-title">
                                       <i class="bi bi-list-task fw-bold"></i> Purchase History
                                       </div>
                                </div>
                                <div class="card-body">

                                 <div class="row">
                  <p>Check below history of all purchased educational pins </p>
                                  @if(!$pin->isEmpty())
                                    @php
                                    $currentPage = $pin->currentPage(); // Current page number
                                    $perPage = $pin->perPage(); // Number of items per page
                                    $serialNumber = ($currentPage - 1) * $perPage + 1; // Starting serial number for current page
                                 @endphp
                                 <div class="table-responsive">
                                    <table class="table text-nowrap" style="background:#fafafc !important">
                                       <thead>
                                          <tr class="table-primary">
                                              <th width="5%" scope="col">ID</th>
                                              <th scope="col">Type</th>
                                              <th scope="col">Token </th>
                                              <th scope="col" class="text-center">Status</th>
                                          </tr>
                                       </thead>
                                       <tbody>
                                          @foreach($pin as $data)
                                          <tr>
                                              <th scope="row">{{ $serialNumber++ }}</th>
                                              <td>{{$data->type}}</td>
                                              <td>{{Str::upper($data->token)}}</td>
                                             <td class="text-center">
                                                @if ($data->status == 'approved')
                                                <span class="badge bg-outline-success">{{ Str::upper($data->status)}}</span>
                                                @else
                                                <span class="badge bg-outline-danger">{{ Str::upper($data->status)}}</span>
                                              @endif
                                             </td>
                                          </tr>
                                          @endforeach
                                       </tbody>
                                    </table>
                                    <!-- Pagination Links -->
                                   <div class="d-flex justify-content-center">
                                        {{ $pin->links('vendor.pagination.bootstrap-4') }}
                                   </div>
                                 </div>
                                 @else
                                  <center><img width="65%" src="{{ asset('assets/images/no-transaction.gif')}}" alt=""></center>
                                 <p class="text-center fw-semibold  fs-15">  No available Pins!</p>
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
      <script src="{{ asset('assets/js/tv.js') }}"></script>
      <script src="{{ asset('assets/js/config.js') }}"></script>
      <script src="{{ asset('assets/js/logout.js') }}"></script>
       <script>
        document.addEventListener('DOMContentLoaded', function() {
        const form = document.querySelector('form');
        const submitButton = document.getElementById('buy');

        form.addEventListener('submit', function() {
            submitButton.disabled = true;
            submitButton.innerText = 'Please wait while we process your request...';
        });
      });
      </script>
   </body>
</html>
