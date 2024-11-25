@extends('layouts.dashboard')
@section('title', 'Buy Airtime')
@section('content')

    <!------App Header ----->
    @include('components.app-header')
    <!-- Start::app-sidebar -->

    @include('components.app-sidebar')
    <!-- Start::app-content -->
    <div class="main-content app-content">
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
                                                <i class="bi bi-credit-card"></i> Buy Airtime
                                            </div>
                                        </div>
                                        <div class="card-body">

                                            <center>
                                                <img class="img-fluid"
                                                    src="{{ asset('assets/images/network_providers.png') }}" width="60%">
                                            </center>
                                            <p>To purchase airtime, select your mobile network, enter your mobile number,
                                                and choose the amount to proceed with the transaction.</p>

                                            <div class="row text-center">
                                                <div class="col-md-12">
                                                    @if (session('success'))
                                                        <div class="alert alert-success alert-dismissible fade show"
                                                            role="alert">
                                                            {!! session('success') !!}
                                                        </div>
                                                    @endif

                                                    @if (session('error'))
                                                        <div class="alert alert-danger alert-dismissible fade show"
                                                            role="alert">
                                                            {{ session('error') }}
                                                        </div>
                                                    @endif

                                                    @if ($errors->any())
                                                        <div class="alert alert-danger alert-dismissible fade show"
                                                            role="alert">
                                                            <ul>
                                                                @foreach ($errors->all() as $error)
                                                                    <li>{{ $error }}</li>
                                                                @endforeach
                                                            </ul>
                                                        </div>
                                                    @endif
                                                    <form id="buyAirtimeForm" name="buy-airtime" method="POST"
                                                        action="{{ route('buyairtime') }}">
                                                        @csrf
                                                        <div class="mb-3 row">
                                                            <div class="col-md-12">
                                                                <select name="network" class="form-select text-center"
                                                                    aria-label="Default select example">
                                                                    <option value="">Choose Network</option>
                                                                    <option value="mtn">MTN</option>
                                                                    <option value="airtel">AIRTEL</option>
                                                                    <option value="glo">GLO</option>
                                                                    <option value="etisalat">9Mobile</option>
                                                                </select>
                                                            </div>
                                                            <div class="col-md-12 mt-2">
                                                                <p class="mb-2 text-muted">Phone Number</p>
                                                                <input type="text" id="mobileno"
                                                                    oninput="validateNumber()" name="mobileno"
                                                                    value="" class="form-control phone text-center"
                                                                    maxlength="11" required />
                                                                <p id="networkResult"></p>
                                                            </div>
                                                            <div class="col-md-12">
                                                                <p class="mb-2 text-muted">Amount</p>
                                                                <input type="text" id="amount" name="amount"
                                                                    value="" class="form-control text-center"
                                                                    maxlength="4" required />
                                                            </div>
                                                        </div>
                                                        <button type="submit" id="buy-airtime" class="btn btn-primary"><i
                                                                class="las la-shopping-cart"></i> Buy Airtime</button>
                                                    </form>
                                                </div>

                                            </div>
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>
                        <div class="col-xl-8">
                            <div class="card custom-card">
                                <div class="card-header justify-content-between">
                                    <div class="card-title">
                                        <i class="bi bi-list-task fw-bold"></i> Airtime Price List
                                    </div>
                                </div>
                                <div class="card-body">
                                    <div class="col-md-12  row">
                                        <p>Don't miss out on these amazing deals! Buy your airtime now and stay connected
                                            without breaking the bank!</p>
                                        <div class="validation-info   col-md-12 mb-2" id="validation-info">
                                            @if (!$priceList->isEmpty())
                                                @php
                                                    $currentPage = $priceList->currentPage(); // Current page number
                                                    $perPage = $priceList->perPage(); // Number of items per page
                                                    $serialNumber = ($currentPage - 1) * $perPage + 1; // Starting serial number for current page
                                                @endphp
                                                <div class="table-responsive">
                                                    <table class="table text-nowrap" style="background:#fafafc !important">
                                                        <thead>
                                                            <tr class="table-primary">
                                                                <th width="5%" scope="col">ID</th>
                                                                <th scope="col">Service Name</th>
                                                                <th scope="col">Minimum </th>
                                                                <th scope="col">Maximum</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>

                                                            @foreach ($priceList as $data)
                                                                <tr>
                                                                    <th scope="row">{{ $serialNumber++ }}</th>
                                                                    <td>{{ $data->name }}</td>
                                                                    <td>&#8358;{{ number_format($data->amount, 2) }}</td>
                                                                    <td>&#8358; 5,000.00</td>
                                                                </tr>
                                                            @endforeach
                                                        </tbody>
                                                    </table>
                                                    <!-- Pagination Links -->
                                                    <div class="d-flex justify-content-center">
                                                        {{ $priceList->links('vendor.pagination.bootstrap-4') }}
                                                    </div>
                                                </div>
                                            @else
                                                <center><img width="65%"
                                                        src="{{ asset('assets/images/no-transaction.gif') }}"
                                                        alt=""></center>
                                                <p class="text-center fw-semibold  fs-15"> No Price List added yet!</p>
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
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('buyAirtimeForm');
            const submitButton = document.getElementById('buy-airtime');

            if (form && submitButton) {
                form.addEventListener('submit', function() {
                    submitButton.disabled = true;
                    submitButton.innerText = 'Please wait while we process your request...';
                });
            }
        });
    </script>

@endsection
