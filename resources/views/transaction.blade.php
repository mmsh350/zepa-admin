@extends('layouts.dashboard')
@section('title', 'Transactions')
@section('content')
    <!------App Header ----->
    @include('components.app-header')
    <!-- Start::app-sidebar -->

    @include('components.app-sidebar')

    <!-- Start::app-content -->
    <div class="main-content app-content">
        <div class="container-fluid">
            <!-- Start::page-header -->

            <!-- End::page-header -->
            <!-- Start::row-1 -->
            <div class="row mt-3">
                <div class="col-xxl-12 col-xl-12">
                    <div class="row">

                        <div class="col-xl-12">
                        </div>
                        <div class="col-xl-12">
                            <div class="row ">
                                <div class="col-xl-12">
                                    <div class="card custom-card ">
                                        <div class="card-header justify-content-between">
                                            <div class="card-title">
                                                Transactions
                                            </div>
                                        </div>
                                        <div class="card-body">

                                            <div class="table-responsive">
                                                <form method="GET" action="{{ route('transactions') }}" class="mb-3">
                                                    <div class="row">

                                                        <div class="row col-md-6">

                                                            <div class="col">
                                                                <input type="text" name="reference" class="form-control"
                                                                    placeholder="Search by Reference No."
                                                                    value="{{ request('reference') }}">
                                                            </div>

                                                            <div class="col">
                                                                <input type="date" name="date_from" class="form-control"
                                                                    value="{{ request('date_from') }}"
                                                                    placeholder="Start Date">
                                                            </div>

                                                            <div class="col">
                                                                <input type="date" name="date_to" class="form-control"
                                                                    value="{{ request('date_to') }}" placeholder="End Date">
                                                            </div>

                                                        </div>
                                                        <div class="col-md-2">
                                                            <select name="status" class="form-control">
                                                                <option value="">Select Status</option>
                                                                <option value="Approved"
                                                                    {{ request('status') == 'Approved' ? 'selected' : '' }}>
                                                                    Approved</option>
                                                                <option value="Rejected"
                                                                    {{ request('status') == 'Rejected' ? 'selected' : '' }}>
                                                                    Rejected</option>
                                                                <option value="Pending"
                                                                    {{ request('status') == 'Pending' ? 'selected' : '' }}>
                                                                    Pending</option>
                                                            </select>
                                                        </div>
                                                        <div class="col-md-2">
                                                            <select name="service_type" class="form-control">
                                                                <option value="">Select Service Type</option>

                                                                <option value="CRM"
                                                                    {{ request('service_type') == 'CRM' ? 'selected' : '' }}>
                                                                    CRM</option>
                                                                <option value="Data"
                                                                    {{ request('service_type') == 'Data' ? 'selected' : '' }}>
                                                                    Data</option>
                                                                <option value="Slip"
                                                                    {{ request('service_type') == 'Slip' ? 'selected' : '' }}>
                                                                    Slip</option>
                                                                <option value="Top up"
                                                                    {{ request('service_type') == 'Top up' ? 'selected' : '' }}>
                                                                    Top up</option>
                                                                <option value="Upgrade"
                                                                    {{ request('service_type') == 'Upgrade' ? 'selected' : '' }}>
                                                                    Upgrade</option>
                                                                <option value="Airtime"
                                                                    {{ request('service_type') == 'Airtime' ? 'selected' : '' }}>
                                                                    Airtime</option>
                                                                <option value="Transfer"
                                                                    {{ request('service_type') == 'Transfer' ? 'selected' : '' }}>
                                                                    Transfer</option>
                                                                <option value="Verification"
                                                                    {{ request('service_type') == 'Verification' ? 'selected' : '' }}>
                                                                    Verification</option>
                                                                <option value="Payout"
                                                                    {{ request('service_type') == 'Payout' ? 'selected' : '' }}>
                                                                    Payout</option>
                                                                <option value="Fee"
                                                                    {{ request('service_type') == 'Fee' ? 'selected' : '' }}>
                                                                    Developer Comm. </option>
                                                            </select>
                                                        </div>
                                                        <div class="col-md-2">
                                                            <button type="submit" class="btn btn-primary">Filter</button>
                                                        </div>
                                                    </div>
                                                </form>
                                                <small class="text-danger">Click on the reference number to generate a
                                                    transaction receipt or use the download button</small>
                                                @if (!$transactions->isEmpty())
                                                    @php
                                                        $currentPage = $transactions->currentPage();
                                                        $perPage = $transactions->perPage();
                                                        $serialNumber = ($currentPage - 1) * $perPage + 1;
                                                    @endphp

                                                    <table class="table text-nowrap" style="background:#fafafc !important">
                                                        <thead>
                                                            <tr class="table-primary">
                                                                <th width="5%" scope="col">ID</th>
                                                                <th scope="col">Reference No.</th>
                                                                <th scope="col">Service Type</th>
                                                                <th scope="col">Description</th>
                                                                <th scope="col">Amount</th>
                                                                <th scope="col" class="text-center">Status</th>
                                                                <th scope="col" class="text-center">Receipt</th>
                                                                <th scope="col">Payer</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            @php $i = 1; @endphp
                                                            @foreach ($transactions as $data)
                                                                <tr>
                                                                    <td scope="row">{{ $serialNumber++ }}</td>
                                                                    <td>
                                                                        <a target="_blank"
                                                                            href="{{ route('reciept', $data->referenceId) }}">
                                                                            {{ strtoupper($data->referenceId) }}
                                                                        </a>
                                                                    </td>
                                                                    <td>{{ $data->service_type }}</td>
                                                                    <td>{{ $data->service_description }}</td>
                                                                    <td>&#8358;{{ $data->amount }}</td>
                                                                    <td class="text-center">
                                                                        @if ($data->status == 'Approved')
                                                                            <span
                                                                                class="badge bg-outline-success">{{ Str::upper($data->status) }}</span>
                                                                        @elseif ($data->status == 'Rejected')
                                                                            <span
                                                                                class="badge bg-outline-danger">{{ Str::upper($data->status) }}</span>
                                                                        @elseif ($data->status == 'Pending')
                                                                            <span
                                                                                class="badge bg-outline-warning">{{ Str::upper($data->status) }}</span>
                                                                        @endif
                                                                    </td>
                                                                    <td class="text-center">
                                                                        <a target="_blank"
                                                                            href="{{ route('reciept', $data->referenceId) }}"
                                                                            class="btn btn-outline-primary btn-sm">
                                                                            <i class="bi bi-download"></i> Download </a>
                                                                    </td>
                                                                    <td>({{ $data->payer_name }})</td>
                                                                </tr>
                                                                @php $i++ @endphp
                                                            @endforeach
                                                        </tbody>
                                                        <tfoot
                                                            style="background: #e9ecef; font-weight: bold; border-top: 2px solid #dee2e6;">
                                                            <tr>
                                                                <th colspan="4" class="text-end" style="padding: 10px;">
                                                                    Total Amount:</th>
                                                                <th style="padding: 10px; color: #ff0f17;">
                                                                    &#8358;{{ number_format($total_amount, 2) }}</th>
                                                                <th colspan="3"></th>
                                                            </tr>
                                                        </tfoot>
                                                    </table>
                                                    <!-- Pagination Links -->
                                                    <div class="d-flex justify-content-center">
                                                        {{-- {{ $transactions->links('vendor.pagination.bootstrap-5') }} --}}
                                                        {{ $transactions->appends(request()->input())->links('vendor.pagination.bootstrap-5') }}

                                                    </div>
                                            </div>
                                        @else
                                            <center><img width="65%"
                                                    src="{{ asset('assets/images/no-transaction.gif') }}" alt="">
                                            </center>
                                            <p class="text-center fw-semibold  fs-15"> No Transaction Available!</p>
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
    <!-- End::row-1 -->
@endsection
@section('page-js')
    <script src="{{ asset('assets/js/kyc.js') }}"></script>
@endsection
