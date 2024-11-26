@extends('layouts.dashboard')
@section('title', 'KYC Verification')
@section('content')
    <!------App Header ----->
    @include('components.app-header')
    <!-- Start::app-sidebar -->

    @include('components.app-sidebar')
    <!-- Start::app-content -->
    <div class="main-content app-content">
        <div class="container-fluid">
            <!-- Start::page-header -->
            <div class="d-md-flex d-block align-items-center justify-content-between my-2 page-header-breadcrumb">
                <div>
                    <p class="fw-semibold fs-18 mb-0">KYC Verification</p>
                    <span class="fs-semibold text-muted">The KYC module allows admins to process customer due
                        diligence </span>
                </div>
                <div class="alert alert-outline-light d-flex align-items-center shadow-lg" role="alert">
                    <div>
                        <small class="fw-semibold mb-0 fs-15 ">Referral Code :
                            {{ Auth::user()->referral_code }}</small>
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
                                                    <span class="avatar avatar-md avatar-rounded bg-success-transparent">
                                                        <i class="las la-user-check  fs-16"></i>
                                                    </span>
                                                </div>
                                                <div class="flex-fill ms-3">
                                                    <div
                                                        class="d-flex align-items-center justify-content-between flex-wrap">
                                                        <div>
                                                            <p class="text-muted mb-0">Verified</p>
                                                            <h4 class="fw-semibold mt-1">{{ $verified }}</h4>
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
                                                    <div
                                                        class="d-flex align-items-center justify-content-between flex-wrap">
                                                        <div>
                                                            <p class="text-muted mb-0">Pending</p>
                                                            <h4 class="fw-semibold mt-1">{{ $pending }}</h4>
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
                                                    <div
                                                        class="d-flex align-items-center justify-content-between flex-wrap">
                                                        <div>
                                                            <p class="text-muted mb-0">Rejected</p>
                                                            <h4 class="fw-semibold mt-1">{{ $rejected }}</h4>
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
                                        <div class="card-body" style="background:#fafafc;">

                                            <div class="row">
                                                <!-- Tab Content -->
                                                <div class="col-12">
                                                    <div class="tab-content mt-3">
                                                        <!-- Pending Tab -->
                                                        <div class="tab-pane show rounded cust1 active text-muted"
                                                            id="pending" role="tabpanel">

                                                            <div class="col-12 bg-light mb-3">
                                                                <form action="{{ route('verification.kyc') }}"
                                                                    method="GET">
                                                                    <div class="row">
                                                                        <div class="col-md-6">
                                                                            <input type="text" name="search"
                                                                                class="form-control"
                                                                                value="{{ request('search') }}"
                                                                                placeholder="Search by name or email">
                                                                        </div>

                                                                        <div class="col-md-6">
                                                                            <button type="submit"
                                                                                class="btn btn-primary w-100">Filter</button>
                                                                        </div>
                                                                    </div>
                                                                </form>
                                                            </div>
                                                            @if (!$users->isEmpty())
                                                                @php
                                                                    $currentPage = $users->currentPage();
                                                                    $perPage = $users->perPage();
                                                                    $serialNumber = ($currentPage - 1) * $perPage + 1; // Initialize serial number once
                                                                @endphp
                                                                <div class="table-responsive">
                                                                    <table class="table text-nowrap"
                                                                        style="background:#fafafc !important">
                                                                        <thead>
                                                                            <tr>
                                                                                <th class="cust2 text-light" width="5%"
                                                                                    scope="col">ID</th>
                                                                                <th class="cust2 text-light" scope="col">
                                                                                    Email Address</th>
                                                                                <th class="cust2 text-light" scope="col">
                                                                                    Account Name</th>
                                                                                <th class="cust2 text-light" scope="col">
                                                                                    Phone No.</th>
                                                                                <th class="cust2 text-light" scope="col">
                                                                                    Action</th>
                                                                            </tr>
                                                                        </thead>
                                                                        <tbody>
                                                                            @foreach ($users as $data)
                                                                                <tr>
                                                                                    <th scope="row">
                                                                                        {{ $serialNumber++ }}
                                                                                    </th>
                                                                                    <td>{{ $data->email }}</td>
                                                                                    <td>{{ $data->last_name . ' ' . $data->middle_name . ' ' . $data->first_name }}
                                                                                    </td>
                                                                                    <td>{{ $data->phone_number }}</td>
                                                                                    <td>
                                                                                        <a href="javascript:void(0);"
                                                                                            data-bs-toggle="modal"
                                                                                            data-bs-target=".view"
                                                                                            data-id="{{ $data->id }}"
                                                                                            class="btn btn-icon btn-sm btn-light text-center">
                                                                                            <i class="ri-edit-line"></i>
                                                                                        </a>
                                                                                    </td>
                                                                                </tr>
                                                                            @endforeach
                                                                        </tbody>
                                                                    </table>
                                                                    <!-- Pagination -->
                                                                    <div class="d-flex justify-content-center">
                                                                        {{ $users->appends(request()->query())->links('vendor.pagination.bootstrap-5') }}
                                                                    </div>
                                                                </div>
                                                            @else
                                                                <div class="text-center">
                                                                    <img width="40%"
                                                                        src="{{ asset('assets/images/no-transaction.gif') }}"
                                                                        alt="No Pending KYC">
                                                                    <p class="text-center fw-semibold fs-15">You do not
                                                                        have any pending KYC Verification!</p>
                                                                </div>
                                                            @endif
                                                        </div>


                                                        <!-- Verified Tab -->
                                                        <div class="tab-pane text-muted cust1" id="verified"
                                                            role="tabpanel">
                                                            @if (!$verifiedUsers->isEmpty())
                                                                @php
                                                                    $currentPage = $verifiedUsers->currentPage();
                                                                    $perPage = $verifiedUsers->perPage();
                                                                    $serialNumber = ($currentPage - 1) * $perPage + 1;
                                                                @endphp
                                                                <div class="table-responsive">
                                                                    <table class="table text-nowrap"
                                                                        style="background:#fafafc !important">
                                                                        <thead>
                                                                            <tr>
                                                                                <th class="cust2 text-light"
                                                                                    width="5%" scope="col">ID</th>
                                                                                <th class="cust2 text-light"
                                                                                    scope="col">Email Address</th>
                                                                                <th class="cust2 text-light"
                                                                                    scope="col">Account Name</th>
                                                                                <th class="cust2 text-light"
                                                                                    scope="col">Phone No.</th>
                                                                                <th class="cust2 text-light"
                                                                                    scope="col">Status</th>
                                                                            </tr>
                                                                        </thead>
                                                                        <tbody>
                                                                            @foreach ($verifiedUsers as $data)
                                                                                <tr>
                                                                                    <th scope="row">
                                                                                        {{ $serialNumber++ }}</th>
                                                                                    <td>{{ $data->email }}</td>
                                                                                    <td>{{ $data->last_name . ' ' . $data->middle_name . ' ' . $data->first_name }}
                                                                                    </td>
                                                                                    <td>{{ $data->phone_number }}</td>
                                                                                    <td>
                                                                                        <span
                                                                                            class="badge bg-success-transparent">{{ $data->kyc_status }}</span>
                                                                                    </td>
                                                                                </tr>
                                                                            @endforeach
                                                                        </tbody>
                                                                    </table>
                                                                    <div class="d-flex justify-content-center">
                                                                        {{ $verifiedUsers->appends(['table1_page' => $users->currentPage(), 'table3_page' => $rejectedUsers->currentPage()])->links('vendor.pagination.bootstrap-5') }}
                                                                    </div>
                                                                </div>
                                                            @else
                                                                <center>
                                                                    <img width="40%"
                                                                        src="{{ asset('assets/images/no-transaction.gif') }}"
                                                                        alt="">
                                                                </center>
                                                                <p class="text-center fw-semibold fs-15">No Record found
                                                                </p>
                                                            @endif
                                                        </div>

                                                        <!-- Rejected Tab -->
                                                        <div class="tab-pane text-muted cust1" id="rejected"
                                                            role="tabpanel">
                                                            @if (!$rejectedUsers->isEmpty())
                                                                @php
                                                                    $currentPage = $rejectedUsers->currentPage();
                                                                    $perPage = $rejectedUsers->perPage();
                                                                    $serialNumber = ($currentPage - 1) * $perPage + 1;
                                                                @endphp
                                                                <div class="table-responsive">
                                                                    <table class="table text-nowrap"
                                                                        style="background:#fafafc !important">
                                                                        <thead>
                                                                            <tr>
                                                                                <th class="cust2 text-light"
                                                                                    width="5%" scope="col">ID</th>
                                                                                <th class="cust2 text-light"
                                                                                    scope="col">Email Address</th>
                                                                                <th class="cust2 text-light"
                                                                                    scope="col">Account Name</th>
                                                                                <th class="cust2 text-light"
                                                                                    scope="col">Phone No.</th>
                                                                                <th class="cust2 text-light"
                                                                                    scope="col">Status</th>
                                                                            </tr>
                                                                        </thead>
                                                                        <tbody>
                                                                            @foreach ($rejectedUsers as $data)
                                                                                <tr>
                                                                                    <th scope="row">
                                                                                        {{ $serialNumber++ }}</th>
                                                                                    <td>{{ $data->email }}</td>
                                                                                    <td>{{ $data->last_name . ' ' . $data->middle_name . ' ' . $data->first_name }}
                                                                                    </td>
                                                                                    <td>{{ $data->phone_number }}</td>
                                                                                    <td>
                                                                                        <span
                                                                                            class="badge bg-danger-transparent">{{ $data->kyc_status }}</span>
                                                                                    </td>
                                                                                </tr>
                                                                            @endforeach
                                                                        </tbody>
                                                                    </table>
                                                                    <div class="d-flex justify-content-center">
                                                                        {{ $rejectedUsers->appends(['table1_page' => $users->currentPage(), 'table2_page' => $verifiedUsers->currentPage()])->links('vendor.pagination.bootstrap-5') }}
                                                                    </div>
                                                                </div>
                                                            @else
                                                                <center>
                                                                    <img width="40%"
                                                                        src="{{ asset('assets/images/no-transaction.gif') }}"
                                                                        alt="">
                                                                </center>
                                                                <p class="text-center fw-semibold fs-15">No Record found
                                                                </p>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>


                                            {{-- //Modal View --}}

                                            <div class="modal fade view" id="staticBackdrop" data-bs-backdrop="static"
                                                tabindex="-1" aria-labelledby="myExtraLargeModal" style="display: none;"
                                                aria-hidden="true">
                                                <div class="modal-dialog modal-lg">
                                                    <div class="modal-content">
                                                        <!-- Preloader -->
                                                        <div id="modal-preloader2">
                                                            <div class="modal-preloader_status">
                                                                <div class="modal-preloader_spinner">
                                                                    <div class="d-flex justify-content-center">
                                                                        <div class="spinner-border" role="status"></div>
                                                                        Fetching Record..
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <!-- End Preloader -->
                                                        <div class="modal-header"
                                                            style="background-color:#2b3751; border-bottom: 1px dashed white;">
                                                            <h4 class="modal-title txt-white" style="color:aliceblue"
                                                                id="staticBackdropLabel">
                                                                Account Information </h4>

                                                            <svg data-bs-dismiss="modal"
                                                                xmlns="http://www.w3.org/2000/svg" x="0px" y="0px"
                                                                width="32" height="32" viewBox="0 0 48 48">
                                                                <path fill="#F44336" d="M21.5 4.5H26.501V43.5H21.5z"
                                                                    transform="rotate(45.001 24 24)"></path>
                                                                <path fill="#F44336" d="M21.5 4.5H26.5V43.501H21.5z"
                                                                    transform="rotate(135.008 24 24)"></path>
                                                            </svg>
                                                        </div>
                                                        <div class="modal-body dark-modal">
                                                            <div class="row">
                                                                <div class="col-md-2 ">
                                                                    <center>
                                                                        <img class="img-responsive rounded border border-dark "
                                                                            width="100%" id="label_passport"
                                                                            src="" alt="Profile Photo" />
                                                                    </center>
                                                                </div>
                                                                <div class="col-md-10">
                                                                    <div id="response"></div>
                                                                    <div class="table-responsive theme-scrollbar">
                                                                        <table border="1" class="table">
                                                                            <thead style="background-color:#2b3751;">
                                                                                <tr>
                                                                                    <th colspan="2" class="text-dark">
                                                                                        <i
                                                                                            class="fa fa-user">&nbsp;</i>Submitted
                                                                                        KYC DATA
                                                                                    </th>
                                                                                </tr>
                                                                            </thead>
                                                                            <tbody>
                                                                                <tr>
                                                                                    <th class="border-end" width="50%">
                                                                                        <span id="label_username">Account
                                                                                            Name</span>
                                                                                        <br> <span id="username"
                                                                                            class="f-w-300">N/A</span>
                                                                                    </th>
                                                                                    <th class="border-end" width="50%">
                                                                                        <span>Date
                                                                                            Of Birth</span>
                                                                                        <br> <span id="label_dob"
                                                                                            class="f-w-300">N/A</span>
                                                                                    </th>
                                                                                </tr>

                                                                                <tr>
                                                                                    <span id="userid" hidden></span>
                                                                                    <th>
                                                                                        <span>Phone Number</span>
                                                                                        <br> <span id="label_phoneno"
                                                                                            class="f-w-300">N/A</span>
                                                                                    </th>
                                                                                    <th class="border-end" width="50%">
                                                                                        <label><span>Email
                                                                                                Address</span> <span
                                                                                                id="label_verify"></span></label>

                                                                                        <br> <span id="label_email"
                                                                                            class="f-w-300">N/A</span>
                                                                                    </th>
                                                                                </tr>

                                                                                <tr>
                                                                                    <th class="border-end" width="50%">
                                                                                        <label>
                                                                                            <span>Identity
                                                                                                Type</span>

                                                                                            <br> <span id="label_identity"
                                                                                                class="f-w-300">N/A</span>
                                                                                    </th>
                                                                                    <th>
                                                                                        <span>Identity No.</span>
                                                                                        <br> <span id="label_identity_no"
                                                                                            class="f-w-300">N/A</span>
                                                                                    </th>
                                                                                </tr>

                                                                            </tbody>
                                                                        </table>
                                                                        <div class="card-footer text-end">
                                                                            <div class="col-sm-9 offset-sm-3">
                                                                                <button class="btn btn-danger"
                                                                                    type="button" id="Reject"
                                                                                    value="Reject"> Reject<div
                                                                                        class="lds-ring" id="spinner3">
                                                                                        <div></div>
                                                                                        <div></div>
                                                                                        <div></div>
                                                                                        <div></div>
                                                                                    </div></button>
                                                                                <button class="btn btn-success me-3"
                                                                                    name="Approve" id="Approve"
                                                                                    type="button">
                                                                                    Approve <div class="lds-ring"
                                                                                        id="spinner2">
                                                                                        <div></div>
                                                                                        <div></div>
                                                                                        <div></div>
                                                                                        <div></div>
                                                                                    </div>
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

@endsection
@section('page-js')
    <script src="{{ asset('assets/js/kyc.js') }}"></script>
@endsection
