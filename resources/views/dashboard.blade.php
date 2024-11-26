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
                 <div class="alert alert-outline-light d-flex align-items-center shadow-lg" role="alert">
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
                                                                 &#x20A6;{{ number_format($bonusBalance), 2 }}</h4>
                                                             <p class="text-muted mb-0">Bonus Balance</p>
                                                         </div>

                                                     </div>
                                                 </div>
                                             </div>
                                         </div>
                                     </div>
                                 </div>
                                 <div class="col-xxl-1 col-lg-1 col-md-1">
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
                                 <div class="col-xxl-1 col-lg-1 col-md-1">
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
                                 <div class="col-xxl-2 col-lg-2 col-md-2">
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
                                                                 {{ $virtualAccountCount }}</h4>
                                                             <p class="text-muted mb-0">Virtual Accounts</p>
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
                                                                 {{ $servicesCount }}</h4>
                                                             <p class="text-muted mb-0">Services</p>
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
                                                     <span class="avatar avatar-md avatar-rounded bg-danger-transparent">
                                                         <i class="ri-exchange-funds-line fs-16"></i>
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

                                 <div class="col-xxl-4 col-lg-4 col-md-4">
                                     <div class="card custom-card overflow-hidden">
                                         <div class="card-body">
                                             <div class="d-flex align-items-top justify-content-between">
                                                 <div>
                                                     <span class="avatar avatar-md avatar-rounded bg-danger-transparent">
                                                         <i class="ri-exchange-funds-line fs-16"></i>
                                                     </span>
                                                 </div>
                                                 <div class="flex-fill ms-3">
                                                     <div
                                                         class="d-flex align-items-center justify-content-between flex-wrap">
                                                         <div>
                                                             <h4 class="fw-semibold mt-1">
                                                                 {{ number_format($transactionCount) }}</h4>
                                                             <p class="text-muted mb-0">Identity</p>
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
                                                     <span class="avatar avatar-md avatar-rounded bg-danger-transparent">
                                                         <i class="ri-exchange-funds-line fs-16"></i>
                                                     </span>
                                                 </div>
                                                 <div class="flex-fill ms-3">
                                                     <div
                                                         class="d-flex align-items-center justify-content-between flex-wrap">
                                                         <div>
                                                             <h4 class="fw-semibold mt-1">
                                                                 {{ number_format($transactionCount) }}</h4>
                                                             <p class="text-muted mb-0">Utility</p>
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
                                                     <span class="avatar avatar-md avatar-rounded bg-danger-transparent">
                                                         <i class="ri-exchange-funds-line fs-16"></i>
                                                     </span>
                                                 </div>
                                                 <div class="flex-fill ms-3">
                                                     <div
                                                         class="d-flex align-items-center justify-content-between flex-wrap">
                                                         <div>
                                                             <h4 class="fw-semibold mt-1">
                                                                 {{ number_format($transactionCount) }}</h4>
                                                             <p class="text-muted mb-0">Agency</p>
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
                                                     <span class="avatar avatar-md avatar-rounded bg-danger-transparent">
                                                         <i class="ri-exchange-funds-line fs-16"></i>
                                                     </span>
                                                 </div>
                                                 <div class="flex-fill ms-3">
                                                     <div
                                                         class="d-flex align-items-center justify-content-between flex-wrap">
                                                         <div>
                                                             <h4 class="fw-semibold mt-1">
                                                                 {{ number_format($transactionCount) }}</h4>
                                                             <p class="text-muted mb-0">Upgardes</p>
                                                         </div>

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
                                                 Recent Transactions
                                             </div>
                                         </div>
                                         <div class="card-body" style="background:#fafafc;">
                                             @if (!$transactions->isEmpty())
                                                 @php
                                                     $currentPage = $transactions->currentPage(); // Current page number
                                                     $perPage = $transactions->perPage(); // Number of items per page
                                                     $serialNumber = ($currentPage - 1) * $perPage + 1; // Starting serial number for current page
                                                 @endphp
                                                 <div class="table-responsive">
                                                     <table class="table text-nowrap"
                                                         style="background:#fafafc !important">
                                                         <thead>
                                                             <tr class="table-primary">
                                                                 <th width="5%" scope="col">ID</th>
                                                                 <th scope="col">Date</th>
                                                                 <th scope="col">Type</th>
                                                                 <th scope="col">Status</th>
                                                                 <th scope="col">Description</th>
                                                                 <th scope="col">Meta Data</th>
                                                             </tr>
                                                         </thead>
                                                         <tbody>
                                                             @php $i = 1; @endphp
                                                             @foreach ($transactions as $data)
                                                                 <tr>
                                                                     <th scope="row">{{ $serialNumber++ }}</th>
                                                                     <td>{{ date('F j, Y', strtotime($data->created_at)) }}
                                                                     </td>
                                                                     <td>{{ $data->service_type }}</td>
                                                                     <td>
                                                                         @if ($data->status == 'Approved')
                                                                             <span
                                                                                 class="badge bg-outline-success">{{ $data->status }}</span>
                                                                         @elseif ($data->status == 'Rejected')
                                                                             <span
                                                                                 class="badge bg-outline-danger">{{ $data->status }}</span>
                                                                         @elseif ($data->status == 'Pending')
                                                                             <span
                                                                                 class="badge bg-outline-warning">{{ $data->status }}</span>
                                                                         @endif
                                                                     </td>
                                                                     <td>{{ $data->service_description }}</td>
                                                                     <td>{{ $data->payer_name }}
                                                                         ({{ $data->payer_phone }})
                                                                     </td>
                                                                 </tr>
                                                                 @php $i++ @endphp
                                                             @endforeach
                                                         </tbody>
                                                     </table>
                                                     <!-- Pagination Links -->
                                                     <div class="d-flex justify-content-center">
                                                         {{ $transactions->links('vendor.pagination.bootstrap-4') }}
                                                     </div>
                                                 </div>
                                             @else
                                                 <center><img width="65%"
                                                         src="{{ asset('assets/images/no-transaction.gif') }}"
                                                         alt=""></center>
                                                 <p class="text-center fw-semibold  fs-15"> No Available Transaction </p>
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
