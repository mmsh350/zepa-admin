<!DOCTYPE html>
<html lang="en" dir="ltr" data-nav-layout="vertical" data-theme-mode="light" data-header-styles="light" data-menu-styles="dark" data-toggled="close">
   <head>
      <!-- Meta Data -->
      <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
      <meta http-equiv="X-UA-Compatible" content="IE=edge">
      <meta name="viewport" content="width=device-width, initial-scale=1.0">
      <meta name="description" content="Easy Verifications for your Business"/>
      <meta name="keywords" content="NIMC, BVN, ZEPA, Verification, Airtime,Bills, Identity">
      <meta name="author" content="Zepa Developers">
      <title>ZEPA Solutions - KYC Verification </title>
      <!-- fav icon -->
      <link rel="icon" href="{{ asset('assets/home/images/favicon/favicon.png') }}" type="image/x-icon">
      <!-- Bootstrap Css -->
      <link id="style" href="{{ asset('assets/libs/bootstrap/css/bootstrap.min.css')}}" rel="stylesheet" >
      <!-- Style Css -->
      <link href="{{ asset('assets/css/styles.min.css') }}" rel="stylesheet" >
      <!-- Icons Css -->
      <link href="{{ asset('assets/css/icons.css') }}" rel="stylesheet" >
      <link rel="stylesheet" href="{{ asset('assets/css/custom3.css')}}">
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
                   @php $title="kyc"; $menu="users"; @endphp
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
         <div class="main-content app-content">
            <div class="container-fluid">
               <!-- Start::page-header -->
               <div class="d-md-flex d-block align-items-center justify-content-between my-2 page-header-breadcrumb">
                  <div>
                     <p class="fw-semibold fs-18 mb-0">KYC Verification</p>
                     <span class="fs-semibold text-muted">The KYC module allows admins to process customer due diligence </span>
                  </div>
                  <div class="alert alert-outline-light d-flex align-items-center shadow-lg" role="alert">
                     <div>
                        <small class="fw-semibold mb-0 fs-15 ">Referral Code : {{ Auth::user()->referral_code }}</small>
                     </div>
                  </div>
               </div>
               <!-- End::page-header -->
               <!-- Start::row-1 -->
               <div class="row">
                  <div class="col-xxl-9 col-xl-12">
                     <div class="row">
                        <div class="col-xl-12">
                           <div class="row">
                              <div class="col-xxl-4 col-lg-4 col-md-4">
                                 <div class="card custom-card overflow-hidden">
                                    <div class="card-body">
                                       <div class="d-flex align-items-top justify-content-between">
                                          <div>
                                             <span class="avatar avatar-md avatar-rounded bg-success-transparent">
                                            </i><i class="las la-user-check  fs-16"></i>
                                             </span>
                                          </div>
                                          <div class="flex-fill ms-3">
                                             <div class="d-flex align-items-center justify-content-between flex-wrap">
                                                <div>
                                                   <p class="text-muted mb-0">Verified</p>
                                                   <h4 class="fw-semibold mt-1">{{$verified}}</h4>
                                                </div>
                                                <div id="crm-total-customers"></div>
                                             </div>
                                          </div>
                                       </div>
                                    </div>
                                 </div>
                              </div>
                              <div class="col-xxl-4 col-lg-4 col-md-4">
                                 <div class="card custom-card overflow-hidden">
                                    <div class="card-body">
                                       <div class="d-flex align-items-top justify-content-between">
                                          <div>
                                             <span class="avatar avatar-md avatar-rounded bg-warning-transparent">
                                             <i class="las la-user-clock fs-16"></i>
                                             </span>
                                          </div>
                                          <div class="flex-fill ms-3">
                                             <div class="d-flex align-items-center justify-content-between flex-wrap">
                                                <div>
                                                   <p class="text-muted mb-0">Pending</p>
                                                   <h4 class="fw-semibold mt-1">{{$pending}}</h4>
                                                </div>
                                                <div id="crm-total-deals"></div>
                                             </div>
                                          </div>
                                       </div>
                                    </div>
                                 </div>
                              </div>
                              <div class="col-xxl-4 col-lg-4 col-md-4">
                                 <div class="card custom-card overflow-hidden">
                                    <div class="card-body">
                                       <div class="d-flex align-items-top justify-content-between">
                                          <div>
                                             <span class="avatar avatar-md avatar-rounded bg-danger-transparent">
                                            <i class="las la-user-slash fs-16"></i>
                                             </span>
                                          </div>
                                          <div class="flex-fill ms-3">
                                             <div class="d-flex align-items-center justify-content-between flex-wrap">
                                                <div>
                                                   <p class="text-muted mb-0">Rejected</p>
                                                   <h4 class="fw-semibold mt-1">{{$rejected}}</h4>
                                                </div>
                                                <div id="crm-total-deals"></div>
                                             </div>
                                          </div>
                                       </div>
                                    </div>
                                 </div>
                              </div>
                           </div>
                        </div>
                         
                        <div class="col-xl-12">
                            <div class="row">
                              <div class="col-xl-12">
                           <div class="card custom-card ">
                              <div class="card-header justify-content-between">
                                 <div class="card-title">
                                  KYC Verification
                                 </div>
                              </div>

                              <div class="card-body" style="background:#fafafc;">
                                  <div class="row">
                                    <div class="col-md-2">
                                        <ul class="nav nav-tabs flex-column vertical-tabs-3" id="myTab" role="tablist">
                                            <li class="nav-item">
                                                <a class="nav-link active text-break" data-bs-toggle="tab" role="tab"
                                                    aria-current="page" href="#pending"
                                                    aria-selected="true">
                                                      <i class="ri-group-line me-2 align-middle d-inline-block"></i>Pending ({{$pending}})
                                                </a>
                                            </li>
                                            <li class="nav-item">
                                                <a class="nav-link text-break" data-bs-toggle="tab" role="tab"
                                                    aria-current="page" href="#verified"
                                                    aria-selected="true">
                                                    <i class="ri-user-follow-line me-2 align-middle d-inline-block"></i>Verified
                                                </a>
                                            </li>
                                            <li class="nav-item">
                                                <a class="nav-link text-break mb-0" data-bs-toggle="tab" role="tab"
                                                    aria-current="page" href="#rejected"
                                                    aria-selected="true">
                                                    <i class="ri-user-unfollow-line me-2 align-middle d-inline-block"></i>Rejected
                                                </a>
                                            </li>
                                        </ul>
                                    </div>
                                    <div class="col-md-10 ">
                                        <div class="tab-content">
                                            <div class="tab-pane show  rounded cust1 active text-muted" id="pending"
                                                role="tabpanel">
                                  
                                 @if(!$users->isEmpty())
                                  @php
                                    $currentPage = $users->currentPage(); // Current page number
                                    $perPage = $users->perPage(); // Number of items per page
                                    $serialNumber = ($currentPage - 1) * $perPage + 1; // Starting serial number for current page
                                 @endphp
                                 <div class="table-responsive">
                                    <table class="table text-nowrap" style="background:#fafafc !important">
                                       <thead>
                                          <tr class="">
                                              <th class="cust2 text-light" width="5%" scope="col">ID</th>
                                              <th class="cust2 text-light"scope="col">Email Address</th>
                                              <th class="cust2 text-light"scope="col">Account Name</th>
                                              <th class="cust2 text-light"scope="col">Phone No.</th>
                                             <th class="cust2 text-light" scope="col">Action</th>
                                          </tr>
                                       </thead>
                                       <tbody>
                                          
                                          @foreach($users as $data)
                                          <tr>
                                             <th scope="row">{{ $serialNumber++ }}</th>
                                             <td>{{$data->email}}</td>
                                              <td>
                                                   {{$data->last_name ." ".$data->middle_name." ".$data->first_name}}
                                               </td>
                                             <td>
                                                {{ $data->phone_number }}
                                             </td>
                                             <td>
                                                  
                                              <a href="javascript:void(0);" data-bs-toggle='modal' data-bs-target='.view' data-id={{$data->id}} class="btn btn-icon btn-sm btn-light text-center"><i class="ri-edit-line"></i></a> </td>
                                          </tr>
                                          @endforeach
                                       </tbody>
                                    </table>
                                    <!-- Pagination Links -->
                                   <div class="d-flex justify-content-center">
                                  {{ $users->appends(['table2_page' => $verifiedUsers->currentPage(), 'table3_page' => $rejectedUsers->currentPage()])->links('vendor.pagination.bootstrap-4') }}
                                </div>
                                 </div>
                                 @else 
                                  <center><img width="40%" src="{{ asset('assets/images/no-transaction.gif')}}" alt=""></center>
                                 <p class="text-center fw-semibold  fs-15">  You do not have any pending KYC Verification!</p>
                                 @endif 
                                </div>
                                  <div class="tab-pane text-muted cust1" id="verified"
                                                role="tabpanel">
                                                @if(!$verifiedUsers->isEmpty())
                                  @php
                                    $currentPage = $verifiedUsers->currentPage(); // Current page number
                                    $perPage = $verifiedUsers->perPage(); // Number of items per page
                                    $serialNumber = ($currentPage - 1) * $perPage + 1; // Starting serial number for current page
                                 @endphp
                                 <div class="table-responsive">
                                    <table class="table text-nowrap" style="background:#fafafc !important">
                                       <thead>
                                          <tr class="">
                                              <th class="cust2 text-light" width="5%" scope="col">ID</th>
                                              <th class="cust2 text-light"scope="col">Email Address</th>
                                              <th class="cust2 text-light"scope="col">Account Name</th>
                                              <th class="cust2 text-light"scope="col">Phone No.</th>
                                             <th class="cust2 text-light" scope="col">Status</th>
                                          </tr>
                                       </thead>
                                       <tbody>
                                          
                                          @foreach($verifiedUsers as $data)
                                          <tr>
                                             <th scope="row">{{ $serialNumber++ }}</th>
                                             <td>{{$data->email}}</td>
                                              <td>
                                                   {{$data->last_name ." ".$data->middle_name." ".$data->first_name}}
                                               </td>
                                             <td>
                                                {{ $data->phone_number }}
                                             </td>
                                             <td>
                                               <span class="badge bg-success-transparent">{{$data->kyc_status}}</span>
                                             </td>
                                          </tr>
                                          @endforeach
                                       </tbody>
                                    </table>
                                    <!-- Pagination Links -->
                                   <div class="d-flex justify-content-center">
                                       {{ $verifiedUsers->appends(['table1_page' => $users->currentPage(), 'table3_page' => $rejectedUsers->currentPage()])->links('vendor.pagination.bootstrap-4') }} 
                                   </div>
                                 </div>
                                 @else 
                                  <center><img width="40%" src="{{ asset('assets/images/no-transaction.gif')}}" alt=""></center>
                                 <p class="text-center fw-semibold  fs-15">  No Record found</p>
                                 @endif 
                                            </div>
                                    <div class="tab-pane text-muted cust1" id="rejected" role="tabpanel">
                                 @if(!$rejectedUsers->isEmpty())
                                  @php
                                    $currentPage = $rejectedUsers->currentPage(); // Current page number
                                    $perPage = $rejectedUsers->perPage(); // Number of items per page
                                    $serialNumber = ($currentPage - 1) * $perPage + 1; // Starting serial number for current page
                                 @endphp
                                 <div class="table-responsive">
                                    <table class="table text-nowrap" style="background:#fafafc !important">
                                       <thead>
                                          <tr class="">
                                              <th class="cust2 text-light" width="5%" scope="col">ID</th>
                                              <th class="cust2 text-light"scope="col">Email Address</th>
                                              <th class="cust2 text-light"scope="col">Account Name</th>
                                              <th class="cust2 text-light"scope="col">Phone No.</th>
                                             <th class="cust2 text-light" scope="col">Status</th>
                                          </tr>
                                       </thead>
                                       <tbody>
                                          
                                          @foreach($rejectedUsers as $data)
                                          <tr>
                                             <th scope="row">{{ $serialNumber++ }}</th>
                                             <td>{{$data->email}}</td>
                                              <td>
                                                   {{$data->last_name ." ".$data->middle_name." ".$data->first_name}}
                                               </td>
                                             <td>
                                                {{ $data->phone_number }}
                                             </td>
                                             <td>
                                               <span class="badge bg-danger-transparent">{{$data->kyc_status}}</span>
                                             </td>
                                          </tr>
                                          @endforeach
                                       </tbody>
                                    </table>
                                    <!-- Pagination Links -->
                                   <div class="d-flex justify-content-center">
                                        {{ $rejectedUsers->appends(['table1_page' => $users->currentPage(), 'table2_page' => $verifiedUsers->currentPage()])->links('vendor.pagination.bootstrap-4') }} 
                                   </div>
                                 </div>
                                 @else 
                                  <center><img width="40%" src="{{ asset('assets/images/no-transaction.gif')}}" alt=""></center>
                                 <p class="text-center fw-semibold  fs-15">  No Record found</p>
                                 @endif 
                                    </div>
                                        </div>
                                    </div>
                                </div>
                            
                                {{-- //Modal Vie --}}

                <div class="modal fade view"  id="staticBackdrop" data-bs-backdrop="static"  tabindex="-1" aria-labelledby="myExtraLargeModal" style="display: none;" aria-hidden="true">
                  <div class="modal-dialog modal-lg">
                  <div class="modal-content">
                       <!-- Preloader -->
                          <div id="modal-preloader2">
                              <div class="modal-preloader_status">
                              <div class="modal-preloader_spinner">
                                  <div class="d-flex justify-content-center">
                                  <div class="spinner-border" role="status"></div>
                                     Fetching  Record..
                                  </div>
                              </div>
                              </div>
                          </div>
                      <!-- End Preloader -->
                      <div class="modal-header" style="background-color:#2b3751; border-bottom: 1px dashed white;" >
                      <h4 class="modal-title txt-white" style="color:aliceblue" id="staticBackdropLabel"> Account Information </h4>
                     
                      <svg data-bs-dismiss="modal" xmlns="http://www.w3.org/2000/svg" x="0px" y="0px" width="32" height="32" viewBox="0 0 48 48">
                          <path fill="#F44336" d="M21.5 4.5H26.501V43.5H21.5z" transform="rotate(45.001 24 24)"></path><path fill="#F44336" d="M21.5 4.5H26.5V43.501H21.5z" transform="rotate(135.008 24 24)"></path>
                          </svg>
                      </div>
                      <div class="modal-body dark-modal">
                       <div class="row">
                          <div class="col-md-2 ">
                              <center>
                                  <img class="img-responsive rounded border border-dark " width="100%" id="label_passport" src="" alt="Profile Photo" />
                              </center>
                          </div>
                          <div class="col-md-10">
                            <div class="table-responsive theme-scrollbar">
                              <table border="1" class="table">
                                <thead style="background-color:#2b3751;">
                                  <tr>
                                    <th colspan="2" class="text-dark"><i class="fa fa-user">&nbsp;</i>Submitted KYC DATA
                                    </th>
                                  </tr>
                                </thead>
                                <tbody>
                                  <tr>
                                    <th class="border-end" width="50%">
                                      <span id="label_username">Account Name</span>
                                    <br> <span id="username" class="f-w-300">N/A</span>
                                    </th>
                                     <th class="border-end" width="50%"><span>Date Of Birth</span>
                                    <br> <span id="label_dob" class="f-w-300">N/A</span>
                                    </th>
                                  </tr>

                                  <tr><span id="userid" hidden></span>
                                    <th><span>Phone Number</span>
                                      <br> <span id="label_phoneno" class="f-w-300">N/A</span>
                                    </th>
                                     <th class="border-end" width="50%">
                                      <label><span>Email Address</span> <span id="label_verify"></span></label>
                                      
                                     <br> <span id="label_email" class="f-w-300">N/A</span>
                                    </th>
                                  </tr>

                                  <tr>
                                    <th class="border-end" width="50%">
                                      <label>
                                        <span>Identity Type</span> 
                                      
                                     <br> <span id="label_identity" class="f-w-300">N/A</span>
                                    </th>
                                    <th>
                                      <span>Identity No.</span>                                               
                                      <br> <span id="label_identity_no" class="f-w-300">N/A</span> 
                                    </th>
                                  </tr>
                                  
                                </tbody>
                              </table>
                              <div class="card-footer text-end">
                      <div class="col-sm-9 offset-sm-3">
                        <button class="btn btn-danger" type="button" id="Reject" value="Reject"> Reject<div class="lds-ring" id="spinner3"><div></div><div></div><div></div><div></div></div></button>
                        <button class="btn btn-success me-3" name="Approve" id="Approve" type="button">
                          Approve  <div class="lds-ring" id="spinner2"><div></div><div></div><div></div><div></div></div>
                        </button>
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
      <script src="{{ asset('assets/js/kyc.js') }}"></script>
      <script src="{{ asset('assets/js/logout.js') }}"></script>
   </body>
</html>
