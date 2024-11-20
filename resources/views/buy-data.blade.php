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
      <title>ZEPA Solutions - Buy Data </title>
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
                  @php $title="data"; $menu="Utility"; @endphp
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
                        <div class="col-xl-4">
                           <div class="row ">
                              <div class="col-xl-12">
                                 <div class="card custom-card">
                                      <div class="card-header  justify-content-between">
                                       <div class="card-title">
                                          <i class="bi bi-credit-card"></i></i> Buy Data
                                       </div>
                                    </div>
                                    <div class="card-body">

                                          <center>
                                             <img class="img-fluid" src="{{asset('assets/images/network_providers.png')}}" width="40%">
                                          </center>
                                        <p>To purchase data, select your mobile network, enter your mobile number, and choose the amount to proceed with the transaction.</p>

                                         <div class="row text-center">
                                         <div class="col-md-12">
                                           @if (session('success'))
                                             <div class="alert alert-success alert-dismissible fade show" role="alert">
                                                {!! session('success') !!}
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

                                            <form name="buy-data" method="POST" action="{{route('buydata')}}">
                                                @csrf
                                                <div class="mb-3 row">
                                                    <div class="col-md-12">
                                                      <select name="network" id="service_id" class="form-select text-center" aria-label="Default select example">
                                                      <option value="">Choose Network</option>
                                                       @foreach($servicename as $service)
                                                      <option value="{{$service->service_id}}">{{$service->service_name}}</option>
                                                       @endforeach
                                                   </select>
                                                    </div>
                                                     <div class="col-md-12 mt-3">
                                                      <select name="bundle" id="bundle" class="form-select text-center" aria-label="Default select example">
                                                      <option value="">Choose Bundle</option>
                                                      </select>
                                                    </div>
                                                    <div class="col-md-12 mt-2">
                                                        <p class="mb-2 text-muted">Amount To Pay</p>
                                                        <input type="text" id="amountToPay" readonly value="" class="form-control text-center"/>
                                                    </div>
                                                     <div class="col-md-12 mt-2">
                                                        <p class="mb-0 text-muted">Phone Number</p>
                                                        <input type="text" id="mobileno" name="mobileno" oninput="validateNumber()" value="" class="form-control phone text-center" maxlength="11" required/>
                                                        <p id="networkResult"></p>
                                                      </div>
                                                       <div class="col-md-12 mt-0">
                                                  <button type="submit" id="buy-data" class="btn btn-primary"><i class="las la-shopping-cart"></i> Buy Data</button>
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
                        <div class="col-xl-8 d-none d-md-block">
                            <div class="card custom-card">
                                <div class="card-header justify-content-between">
                                     <div class="card-title">
                                       <i class="bi bi-list-task fw-bold"></i> Data Bundle Price List
                                       </div>
                                </div>
                                <div class="card-body">

                                 <div class="row">
                                    <div class="col-md-3 border border-light rounded text-center">
                                       <center>
                                       <ul class="nav nav-tabs mt-4 flex-column vertical-tabs-2" id="myTab" role="tablist">
                                            <li class="nav-item " role="presentation">
                                                <a class="nav-link active" data-bs-toggle="tab" role="tab" aria-current="page" href="#mtn" aria-selected="true">
                                                    <img class="img-fluid" src="{{asset('assets/images/mtn.png')}}">
                                                </a>
                                            </li>
                                            <li class="nav-item" role="presentation">
                                                <a class="nav-link" data-bs-toggle="tab" role="tab" aria-current="page" href="#airtel" aria-selected="false" tabindex="-1">
                                                        <img class="img-fluid" src="{{asset('assets/images/airtel.png')}}" width="84px" height="77px">
                                                </a>
                                            </li>
                                            <li class="nav-item" role="presentation">
                                                <a class="nav-link" data-bs-toggle="tab" role="tab" aria-current="page" href="#glo" aria-selected="false" tabindex="-1">
                                                       <img class="img-fluid" src="{{asset('assets/images/glo.png')}}">
                                                 </a>
                                            </li>
                                            <li class="nav-item " role="presentation">
                                                <a class="nav-link mb-2" data-bs-toggle="tab" role="tab" aria-current="page" href="#9mobile" aria-selected="false" tabindex="">
                                                   <img class="img-fluid" src="{{asset('assets/images/9mobile.png')}}">
                                                </a>
                                            </li>
                                             <li class="nav-item " role="presentation">
                                                <a class="nav-link mb-2" data-bs-toggle="tab" role="tab" aria-current="page" href="#smile" aria-selected="false" tabindex="">
                                                   <img class="img-fluid" src="{{asset('assets/images/smile.jpg')}}" width="">
                                                </a>
                                            </li>
                                            <li class="nav-item " role="presentation">
                                                <a class="nav-link mb-2" data-bs-toggle="tab" role="tab" aria-current="page" href="#spectranet" aria-selected="false" tabindex="">
                                                   <img class="img-fluid" src="{{asset('assets/images/images.jpeg')}}" width="">
                                                </a>
                                            </li>
                                        </ul>
                                       </center>
                                    </div>
                                    <div class="col-md-9 ">
                                        <div class="tab-content">
                                            <div class="tab-pane text-muted active show" id="mtn" role="tabpanel">
                                                <div class="col-md-12  row">
                                                   <p class="fw-bold">MTN DATA PLANS</p>
                                                <p>We offer the most competitive rates for data purchases across all major networks. Check out our unbeatable prices:</p>
                                                         @if(!$priceList1->isEmpty())
                                             <div class="table-responsive">
                                                <table class="table text-nowrap" style="background:#fafafc !important">
                                                   <thead>
                                                      <tr class="table-primary">
                                                         {{-- <th width="5%" scope="col">ID</th> --}}
                                                         <th scope="col">DATA PLANS</th>
                                                        {{-- <th scope="col"></th> --}}
                                                         <th scope="col" class="text-center">PRICE</th>
                                                      </tr>
                                                   </thead>
                                                   <tbody>
                                                      @php $i = 1; @endphp
                                                      @foreach($priceList1 as $data)
                                                      <tr>
                                                         {{-- <th scope="row">{{ $i }}</th> --}}
                                                         <td>{{$data->name}}</td>

                                                         <td class="text-center">&#8358;{{number_format($data->variation_amount,2)}}</td>
                                                      </tr>
                                                      @php $i++ @endphp
                                                      @endforeach
                                                   </tbody>
                                                </table>
                                                <!-- Pagination Links -->
                                             <div class="d-flex justify-content-center">
                                                   {{ $priceList1->appends(['table2_page' => $priceList2->currentPage(), 'table3_page' => $priceList3->currentPage(), 'table4_page' => $priceList4->currentPage(), 'table5_page' => $priceList5->currentPage(), 'table6_page' => $priceList6->currentPage()])->links('vendor.pagination.bootstrap-4') }}
                                             </div>
                                             </div>
                                             @else
                                             <center><img width="65%" src="{{ asset('assets/images/no-transaction.gif')}}" alt=""></center>
                                             <p class="text-center fw-semibold  fs-15">  No Price List added yet!</p>
                                             @endif
                                             <p>Don't miss out on these amazing deals! Buy your data now and stay connected without breaking the bank!"</p>


                                                </div>
                                            </div>
                                            <div class="tab-pane text-muted" id="airtel" role="tabpanel">
                                                 <div class="col-md-12  row">
                                                   <p class="fw-bold">AIRTEL DATA PLANS</p>
                                                <p>We offer the most competitive rates for data purchases across all major networks. Check out our unbeatable prices:</p>
                                                         @if(!$priceList2->isEmpty())
                                             <div class="table-responsive">
                                                <table class="table text-nowrap" style="background:#fafafc !important">
                                                   <thead>
                                                      <tr class="table-primary">
                                                         {{-- <th width="5%" scope="col">ID</th> --}}
                                                         <th scope="col">DATA PLANS</th>
                                                          {{-- <th scope="col"></th> --}}
                                                         <th scope="col" class="text-center">PRICE</th>
                                                      </tr>
                                                   </thead>
                                                   <tbody>
                                                      @php $i = 1; @endphp
                                                      @foreach($priceList2 as $data)
                                                      <tr>
                                                         {{-- <th scope="row">{{ $i }}</th> --}}
                                                         <td>{{$data->name}}</td>

                                                         <td class="text-center">&#8358;{{number_format($data->variation_amount,2)}}</td>
                                                      </tr>
                                                      @php $i++ @endphp
                                                      @endforeach
                                                   </tbody>
                                                </table>
                                                <!-- Pagination Links -->
                                             <div class="d-flex justify-content-center">
                                                    {{ $priceList2->appends(['table1_page' => $priceList1->currentPage(), 'table3_page' => $priceList3->currentPage(), 'table4_page' => $priceList4->currentPage(), 'table5_page' => $priceList5->currentPage(), 'table6_page' => $priceList6->currentPage()])->links('vendor.pagination.bootstrap-4') }}
                                             </div>
                                             </div>
                                             @else
                                             <center><img width="65%" src="{{ asset('assets/images/no-transaction.gif')}}" alt=""></center>
                                             <p class="text-center fw-semibold  fs-15">  No Price List added yet!</p>
                                             @endif
                                             <p>Don't miss out on these amazing deals! Buy your data now and stay connected without breaking the bank!"</p>


                                                </div>
                                            </div>
                                            <div class="tab-pane text-muted" id="glo" role="tabpanel">
                                                 <div class="col-md-12  row">
                                                   <p class="fw-bold">GLO DATA PLANS</p>
                                                <p>We offer the most competitive rates for data purchases across all major networks. Check out our unbeatable prices:</p>
                                                         @if(!$priceList3->isEmpty())
                                             <div class="table-responsive">
                                                <table class="table text-nowrap" style="background:#fafafc !important">
                                                   <thead>
                                                      <tr class="table-primary">
                                                         {{-- <th width="5%" scope="col">ID</th> --}}
                                                         <th scope="col">DATA PLANS</th>
                                                          {{-- <th scope="col"></th> --}}
                                                         <th scope="col" class="text-center">PRICE</th>
                                                      </tr>
                                                   </thead>
                                                   <tbody>
                                                      @php $i = 1; @endphp
                                                      @foreach($priceList3 as $data)
                                                      <tr>
                                                         {{-- <th scope="row">{{ $i }}</th> --}}
                                                         <td>{{$data->name}}</td>

                                                         <td class="text-center">&#8358;{{number_format($data->variation_amount,2)}}</td>
                                                      </tr>
                                                      @php $i++ @endphp
                                                      @endforeach
                                                   </tbody>
                                                </table>
                                                <!-- Pagination Links -->
                                             <div class="d-flex justify-content-center">
                                                   {{ $priceList3->appends(['table1_page' => $priceList1->currentPage(), 'table2_page' => $priceList2->currentPage(), 'table4_page' => $priceList4->currentPage(), 'table5_page' => $priceList5->currentPage(), 'table6_page' => $priceList6->currentPage()])->links('vendor.pagination.bootstrap-4') }}
                                             </div>
                                             </div>
                                             @else
                                             <center><img width="65%" src="{{ asset('assets/images/no-transaction.gif')}}" alt=""></center>
                                             <p class="text-center fw-semibold  fs-15">  No Price List added yet!</p>
                                             @endif
                                             <p>Don't miss out on these amazing deals! Buy your data now and stay connected without breaking the bank!"</p>


                                                </div>
                                            </div>
                                            <div class="tab-pane text-muted" id="9mobile" role="tabpanel">
                                                 <div class="col-md-12  row">
                                                   <p class="fw-bold">9MOBILE DATA PLANS</p>
                                                <p>We offer the most competitive rates for data purchases across all major networks. Check out our unbeatable prices:</p>
                                                         @if(!$priceList4->isEmpty())
                                             <div class="table-responsive">
                                                <table class="table text-nowrap" style="background:#fafafc !important">
                                                   <thead>
                                                      <tr class="table-primary">
                                                         {{-- <th width="5%" scope="col">ID</th> --}}
                                                         <th scope="col">DATA PLANS</th>
                                                          {{-- <th scope="col"></th> --}}
                                                         <th scope="col" class="text-center">PRICE</th>
                                                      </tr>
                                                   </thead>
                                                   <tbody>
                                                      @php $i = 1; @endphp
                                                      @foreach($priceList4 as $data)
                                                      <tr>
                                                         {{-- <th scope="row">{{ $i }}</th> --}}
                                                         <td>{{$data->name}}</td>

                                                         <td class="text-center">&#8358;{{number_format($data->variation_amount,2)}}</td>
                                                      </tr>
                                                      @php $i++ @endphp
                                                      @endforeach
                                                   </tbody>
                                                </table>
                                                <!-- Pagination Links -->
                                             <div class="d-flex justify-content-center">
                                                   {{ $priceList4->appends(['table1_page' => $priceList1->currentPage(), 'table2_page' => $priceList2->currentPage(), 'table3_page' => $priceList3->currentPage(), 'table5_page' => $priceList5->currentPage(), 'table6_page' => $priceList6->currentPage()])->links('vendor.pagination.bootstrap-4') }}
                                             </div>
                                             </div>
                                             @else
                                             <center><img width="65%" src="{{ asset('assets/images/no-transaction.gif')}}" alt=""></center>
                                             <p class="text-center fw-semibold  fs-15">  No Price List added yet!</p>
                                             @endif
                                             <p>Don't miss out on these amazing deals! Buy your data now and stay connected without breaking the bank!"</p>


                                                </div>
                                            </div>
                                               <div class="tab-pane text-muted" id="smile" role="tabpanel">
                                               <div class="col-md-12  row">
                                                   <p class="fw-bold">SIMLE DATA PLANS</p>
                                                <p>We offer the most competitive rates for data purchases across all major networks. Check out our unbeatable prices:</p>
                                                         @if(!$priceList5->isEmpty())
                                             <div class="table-responsive">
                                                <table class="table text-nowrap" style="background:#fafafc !important">
                                                   <thead>
                                                      <tr class="table-primary">
                                                         {{-- <th width="5%" scope="col">ID</th> --}}
                                                         <th scope="col">DATA PLANS</th>
                                                          {{-- <th scope="col"></th> --}}
                                                         <th scope="col" class="text-center">PRICE</th>
                                                      </tr>
                                                   </thead>
                                                   <tbody>
                                                      @php $i = 1; @endphp
                                                      @foreach($priceList5 as $data)
                                                      <tr>
                                                         {{-- <th scope="row">{{ $i }}</th> --}}
                                                         <td>{{$data->name}}</td>

                                                         <td class="text-center">&#8358;{{number_format($data->variation_amount,2)}}</td>
                                                      </tr>
                                                      @php $i++ @endphp
                                                      @endforeach
                                                   </tbody>
                                                </table>
                                                <!-- Pagination Links -->
                                             <div class="d-flex justify-content-center">
                                                   {{ $priceList5->appends(['table1_page' => $priceList1->currentPage(), 'table2_page' => $priceList2->currentPage(), 'table3_page' => $priceList3->currentPage(), 'table4_page' => $priceList4->currentPage(), 'table6_page' => $priceList6->currentPage()])->links('vendor.pagination.bootstrap-4') }}
                                             </div>
                                             </div>
                                             @else
                                             <center><img width="65%" src="{{ asset('assets/images/no-transaction.gif')}}" alt=""></center>
                                             <p class="text-center fw-semibold  fs-15">  No Price List added yet!</p>
                                             @endif
                                             <p>Don't miss out on these amazing deals! Buy your data now and stay connected without breaking the bank!"</p>


                                                </div>
                                            </div>
                                               <div class="tab-pane text-muted" id="spectranet" role="tabpanel">
                                                 <div class="col-md-12  row">
                                                   <p class="fw-bold">SPECTRANET DATA PLANS</p>
                                                <p>We offer the most competitive rates for data purchases across all major networks. Check out our unbeatable prices:</p>
                                                         @if(!$priceList6->isEmpty())
                                             <div class="table-responsive">
                                                <table class="table text-nowrap" style="background:#fafafc !important">
                                                   <thead>
                                                      <tr class="table-primary">
                                                         {{-- <th width="5%" scope="col">ID</th> --}}
                                                         <th scope="col">DATA PLANS</th>
                                                         {{-- <th scope="col"></th> --}}
                                                         <th scope="col" class="text-center">PRICE</th>
                                                      </tr>
                                                   </thead>
                                                   <tbody>
                                                      @php $i = 1; @endphp
                                                      @foreach($priceList6 as $data)
                                                      <tr>
                                                         {{-- <th scope="row">{{ $i }}</th> --}}
                                                         <td>{{$data->name}}</td>

                                                         <td class="text-center">&#8358;{{number_format($data->variation_amount,2)}}</td>
                                                      </tr>
                                                      @php $i++ @endphp
                                                      @endforeach
                                                   </tbody>
                                                </table>
                                                <!-- Pagination Links -->
                                             <div class="d-flex justify-content-center">
                                                   {{ $priceList6->appends(['table1_page' => $priceList1->currentPage(), 'table2_page' => $priceList6->currentPage(), 'table3_page' => $priceList3->currentPage(), 'table4_page' => $priceList4->currentPage(), 'table5_page' => $priceList5->currentPage()])->links('vendor.pagination.bootstrap-4') }}
                                             </div>
                                             </div>
                                             @else
                                             <center><img width="65%" src="{{ asset('assets/images/no-transaction.gif')}}" alt=""></center>
                                             <p class="text-center fw-semibold  fs-15">  No Price List added yet!</p>
                                             @endif
                                             <p>Don't miss out on these amazing deals! Buy your data now and stay connected without breaking the bank!"</p>


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
      <script src="{{ asset('assets/js/data.js') }}"></script>
      <script src="{{ asset('assets/js/config.js') }}"></script>
      <script src="{{ asset('assets/js/logout.js') }}"></script>
      <script>
        $(document).ready(function() {
            // Load the active tab from localStorage
            var activeTab = localStorage.getItem('activeTab');
            if (activeTab) {
                $('#myTab a[href="' + activeTab + '"]').tab('show');
            } else {
                // Set default tab if no active tab is stored
                $('#myTab a[href="#mtn"]').tab('show');
            }
            // Store the active tab in localStorage when clicked
            $('a[data-bs-toggle="tab"]').on('shown.bs.tab', function(e) {
                localStorage.setItem('activeTab', $(e.target).attr('href'));
            });
        });
    </script>
     <script>
        document.addEventListener('DOMContentLoaded', function() {
        const form = document.querySelector('form');
        const submitButton = document.getElementById('buy-data');

        form.addEventListener('submit', function() {
            submitButton.disabled = true;
            submitButton.innerText = 'Please wait while we process your request...';
        });
      });
     </script>
    <script>

   </script>
   </body>
</html>
