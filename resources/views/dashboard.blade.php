 @extends('layouts.dashboard')
 @section('title', 'Dashboard')
 @section('content')

     <!------App Header ----->
     @include('components.app-header')
     <!-- Start::app-sidebar -->

     @include('components.app-sidebar')

     <!-- Start::app-content -->
     <div class="main-content app-content">
         <div class="container-fluid">
             @include('components.news')
             <!-- Start::page-header -->
             <div class="d-md-flex d-block align-items-center justify-content-between my-2 page-header-breadcrumb">
                 <div>
                     <p class="fw-semibold fs-18 mb-0">Welcome back, {{ Auth::user()->first_name }}
                         {{ Auth::user()->last_name }} !</p>
                     <span class="fs-semibold text-muted">Centralize your workflow and track all your activities, from start
                         to finish.</span>
                 </div>
                 @if (session('error'))
                     <div class="alert alert-danger alert-dismissible fade show" role="alert">
                         {{ session('error') }}
                     </div>
                 @endif
                 <div class="alert alert-outline-light d-flex align-items-center shadow-lg mt-2" role="alert">
                     <div>
                         <small class="fw-semibold mb-0 fs-15 ">Referral Code : {{ Auth::user()->referral_code }}</small>
                     </div>
                 </div>
             </div>
             <!-- End::page-header -->
             <!-- Start::row-1 -->
             <div class="row">
                 <div class="col-xxl-12 col-xl-12">
                     <div class="row">
                         <div class="col-xl-12">
                             <div class="row">
                                 <div class="col-xxl-4 col-lg-4 col-md-4">
                                     <div class="card custom-card overflow-hidden">
                                         <div class="card-body">
                                             <div class="d-flex align-items-top justify-content-between">
                                                 <div>
                                                     <span class="avatar avatar-md avatar-rounded bg-primary-transparent">
                                                         <i class="ti ti-wallet fs-16"></i>
                                                     </span>
                                                 </div>
                                                 <div class="flex-fill ms-3">
                                                     <div
                                                         class="d-flex align-items-center justify-content-between flex-wrap">
                                                         <div>

                                                             <h4 class="fw-semibold mt-1">
                                                                 &#x20A6;{{ number_format($walletBalance), 2 }}</h4>
                                                             <p class="text-muted mb-0">Wallet Balance</p>
                                                         </div>

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
                                                     <span class="avatar avatar-md avatar-rounded bg-primary-transparent">
                                                         <i class="ti ti-wallet fs-16"></i>
                                                     </span>
                                                 </div>
                                                 <div class="flex-fill ms-3">
                                                     <div
                                                         class="d-flex align-items-center justify-content-between flex-wrap">
                                                         <div>

                                                             <h4 class="fw-semibold mt-1">
                                                                 &#x20A6;{{ number_format($palmpay['availableBalance'], 2) }}
                                                             </h4>
                                                             <p class="text-muted mb-0">{{ $palmpay['accountName'] }}
                                                                 Balance</p>
                                                         </div>

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
                                                         <i class="ti ti-gift fs-16"></i>
                                                     </span>
                                                 </div>
                                                 <div class="flex-fill ms-3">
                                                     <div
                                                         class="d-flex align-items-center justify-content-between flex-wrap">
                                                         <div>

                                                             <h4 class="fw-semibold mt-1">
                                                                 &#x20A6;{{ number_format($bonusBalance), 2 }}</h4>
                                                             <p class="text-muted mb-0">Bonus Balance</p>
                                                         </div>

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
                                                         <i class="ti ti-gift fs-16"></i>
                                                     </span>
                                                 </div>
                                                 <div class="flex-fill ms-3">
                                                     <div
                                                         class="d-flex align-items-center justify-content-between flex-wrap">
                                                         <div>

                                                             <h4 class="fw-semibold mt-1">
                                                                 &#x20A6;{{ number_format($devBalance), 2 }}</h4>
                                                             <p class="text-muted mb-0">Developer Zepa Balance</p>
                                                         </div>

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
                                                     <span class="avatar avatar-md avatar-rounded bg-info-transparent">
                                                         <i class="ti ti-users fs-16"></i>
                                                     </span>
                                                 </div>
                                                 <div class="flex-fill ms-3">
                                                     <div
                                                         class="d-flex align-items-center justify-content-between flex-wrap">
                                                         <div>

                                                             <h4 class="fw-semibold mt-1">{{ $generalUserCount }}</h4>
                                                             <p class="text-muted mb-0">Users</p>
                                                         </div>
                                                     </div>
                                                 </div>
                                             </div>
                                         </div>
                                     </div>
                                 </div>
                                 <div class="col-xxl-4 col-lg-4 col-md-4 d-none d-md-block">
                                     <div class="card custom-card overflow-hidden">
                                         <div class="card-body">
                                             <div class="d-flex align-items-top justify-content-between">
                                                 <div>
                                                     <span class="avatar avatar-md avatar-rounded bg-info-transparent">
                                                         <i class="ti ti-users fs-16"></i>
                                                     </span>
                                                 </div>
                                                 <div class="flex-fill ms-3">
                                                     <div
                                                         class="d-flex align-items-center justify-content-between flex-wrap">
                                                         <div>

                                                             <h4 class="fw-semibold mt-1">{{ $agentCount }}</h4>
                                                             <p class="text-muted mb-0">Agents</p>
                                                         </div>
                                                     </div>
                                                 </div>
                                             </div>
                                         </div>
                                     </div>
                                 </div>
                                 <div class="col-xxl-4 col-lg-4 col-md-4 d-none d-md-block">
                                     <div class="card custom-card overflow-hidden">
                                         <div class="card-body">
                                             <div class="d-flex align-items-top justify-content-between">
                                                 <div>
                                                     <span class="avatar avatar-md avatar-rounded bg-success-transparent">
                                                         <i class="ti ti-archive fs-16"></i>
                                                     </span>
                                                 </div>
                                                 <div class="flex-fill ms-3">
                                                     <div
                                                         class="d-flex align-items-center justify-content-between flex-wrap">
                                                         <div>

                                                             <h4 class="fw-semibold mt-1">
                                                                 {{ $virtualAccountCount }}</h4>
                                                             <p class="text-muted mb-0">Virtual Accounts</p>
                                                         </div>

                                                     </div>
                                                 </div>
                                             </div>
                                         </div>
                                     </div>
                                 </div>
                                 <div class="col-xxl-4 col-lg-4 col-md-4 d-none d-md-block">
                                     <div class="card custom-card overflow-hidden">
                                         <div class="card-body">
                                             <div class="d-flex align-items-top justify-content-between">
                                                 <div>
                                                     <span class="avatar avatar-md avatar-rounded bg-primary-transparent">
                                                         <i class="ti ti-tag fs-16"></i>
                                                     </span>
                                                 </div>
                                                 <div class="flex-fill ms-3">
                                                     <div
                                                         class="d-flex align-items-center justify-content-between flex-wrap">
                                                         <div>

                                                             <h4 class="fw-semibold mt-1">
                                                                 {{ $servicesCount }}</h4>
                                                             <p class="text-muted mb-0">Services</p>
                                                         </div>
                                                     </div>
                                                 </div>
                                             </div>
                                         </div>
                                     </div>
                                 </div>
                                 <div class="col-xxl-4 col-lg-4 col-md-4 d-none d-md-block">
                                     <div class="card custom-card overflow-hidden">
                                         <div class="card-body">
                                             <div class="d-flex align-items-top justify-content-between">
                                                 <div>
                                                     <span class="avatar avatar-md avatar-rounded bg-warning-transparent">
                                                         <i class="ti ti-receipt fs-16"></i>
                                                     </span>
                                                 </div>
                                                 <div class="flex-fill ms-3">
                                                     <div
                                                         class="d-flex align-items-center justify-content-between flex-wrap">
                                                         <div>
                                                             <h4 class="fw-semibold mt-1">
                                                                 {{ number_format($transactionCount) }}</h4>
                                                             <p class="text-muted mb-0">Transactions</p>
                                                         </div>

                                                     </div>
                                                 </div>
                                             </div>
                                         </div>
                                     </div>
                                 </div>

                                 <div class="col-xxl-4 col-lg-4 col-md-4 d-none d-md-block">
                                     <div class="card custom-card overflow-hidden">
                                         <div class="card-body">
                                             <div class="d-flex align-items-top justify-content-between">
                                                 <div>
                                                     <span class="avatar avatar-md avatar-rounded bg-dark-transparent">
                                                         <i class="ri-fingerprint-line fs-16"></i>
                                                     </span>
                                                 </div>
                                                 <div class="flex-fill ms-3">
                                                     <div
                                                         class="d-flex align-items-center justify-content-between flex-wrap">
                                                         <div>
                                                             <h4 class="fw-semibold mt-1">
                                                                 {{ $totalIdentityCounts }} </h4>
                                                             <p class="text-muted mb-0">Identity</p>
                                                         </div>

                                                     </div>
                                                 </div>
                                             </div>
                                         </div>
                                     </div>
                                 </div>



                                 <div class="col-xxl-4 col-lg-4 col-md-4 d-none d-md-block">
                                     <div class="card custom-card overflow-hidden">
                                         <div class="card-body">
                                             <div class="d-flex align-items-top justify-content-between">
                                                 <div>
                                                     <span
                                                         class="avatar avatar-md avatar-rounded bg-secondary-transparent">
                                                         <i class="ri-briefcase-line fs-16"></i>
                                                     </span>
                                                 </div>
                                                 <div class="flex-fill ms-3">
                                                     <div
                                                         class="d-flex align-items-center justify-content-between flex-wrap">
                                                         <div>
                                                             <h4 class="fw-semibold mt-1">
                                                                 {{ $totalAgencyCounts }}</h4>
                                                             <p class="text-muted mb-0">Agency</p>
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
 @endsection
 @section('page-js')

     <script>
         const marqueeInner = document.querySelector('.marquee-inner');

         marqueeInner.addEventListener('mouseover', () => {
             marqueeInner.style.animationPlayState = 'paused';
         });

         marqueeInner.addEventListener('mouseout', () => {
             marqueeInner.style.animationPlayState = 'running';
         });
     </script>
 @endsection
