@extends('layouts.dashboard')
@section('title', 'Services')
@section('content')

    @include('components.app-header')
    @include('components.app-sidebar')

    <div class="main-content app-content">
        <div class="container-fluid">

            <div class="d-md-flex d-block align-items-center justify-content-between my-2 page-header-breadcrumb">
                <div>
                    <p class="fw-semibold fs-18 mb-0">Manage Services</p>
                    <span class="fs-semibold text-muted">
                        <p>Add new services</p>
                    </span>
                </div>
            </div>
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
            <div class="row">
                <div class="col-xxl-12 col-xl-12">
                    <div class="row">
                        <div class="col-xl-12">
                            <div class="row ">
                                <div class="col-xl-12">
                                    <div class="card custom-card ">
                                        <div class="card-header">
                                            <h5 class="card-title">Add Service</h5>
                                        </div>
                                        <div class="card-body">
                                            <form method="POST" action="{{ route('services.store') }}">
                                                @csrf
                                                <div class="mb-3">
                                                    <label class="form-label">Service Code</label>
                                                    <input type="text" name="service_code" class="form-control" required>
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label">Name</label>
                                                    <input type="text" name="name" class="form-control" required>
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label">Category</label>
                                                    <select name="category" class="form-select" required>
                                                        <option value="">Choose</option>
                                                        <option value="Upgrades">Upgrades</option>
                                                        <option value="Verifications">Verifications</option>
                                                        <option value="Airtime">Airtime</option>
                                                        <option value="Data">Data</option>
                                                        <option value="A2C">A2C</option>
                                                        <option value="Electricity">Electricity</option>
                                                        <option value="Cable Sub">Cable Sub</option>
                                                        <option value="EPIN">EPIN</option>
                                                        <option value="Agency">Agency</option>
                                                        <option value="Charges">Charges</option>
                                                    </select>
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label">Type</label>
                                                    <select name="type" class="form-select" required>
                                                        <option value="">Choose</option>
                                                        <option value="BMOD">BMOD</option>
                                                        <option value="NIN"> NIN</option>
                                                        <option value="VNIN">VNIN</option>
                                                        <option value="PAYOUT">PAYOUT</option>
                                                        <option value="slip_download">
                                                            SLIP_DOWNLOAD </option>
                                                        <option value="bvn_modification">
                                                            BVN_MODIFICATION
                                                        </option>
                                                         <option value="nin_modification_general">
                                                            NIN_MODIFICATION_GENERAL
                                                        </option> 
                                                         <option value="nin_modification_dob">
                                                            NIN_MODIFICATION_DOB
                                                        </option> 
                                                         <option value="nin_modification_name">
                                                            NIN_MODIFICATION_NAME
                                                        </option> 
                                                         <option value="nin_modification_phone">
                                                            NIN_MODIFICATION_PHONE
                                                        </option> 
                                                         <option value="nin_modification_address">
                                                            NIN_MODIFICATION_ADDRESS
                                                        </option> 
                                                        <option value="bvn_modification">
                                                            BVN_MODIFICATION
                                                        </option>
                                                        <option value="verification_v2">
                                                            VERIFICATION_V2
                                                        </option>
                                                        <option value="Uncategorized">
                                                            UNCATEGORIZED
                                                        </option>
                                                    </select>
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label">Amount (₦)</label>
                                                    <input type="number" name="amount" class="form-control" required>
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label">Description</label>
                                                    <textarea name="description" class="form-control"></textarea>
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label">Status</label>
                                                    <select name="status" class="form-select" required>
                                                        <option value="enabled">Enabled</option>
                                                        <option value="disabled">Disabled</option>
                                                    </select>
                                                </div>
                                                <button type="submit" class="btn btn-primary">Create Service</button>

                                            </form>
                                        </div>
                                    </div>
                                </div>
                            @endsection
