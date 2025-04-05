@extends('layouts.dashboard')
@section('title', 'Edit Services')
@section('content')
    <!------App Header ----->
    @include('components.app-header')
    <!-- Start::app-sidebar -->

    @include('components.app-sidebar')


    <div class="main-content app-content">
        <div class="container-fluid">
            <!-- Start::page-header -->
            <div class="d-md-flex d-block align-items-center justify-content-between my-2 page-header-breadcrumb">
                <div>
                    <p class="fw-semibold fs-18 mb-0">Services</p>
                    <span class="fs-semibold text-muted">
                        <p>Modify Services and service status form this module
                        </p>
                    </span>
                </div>
            </div>
            <!-- End::page-header -->
            <!-- Start::row-1 -->
            <div class="row">
                <div class="col-xxl-12 col-xl-12">
                    <div class="row">
                        <div class="col-xl-12">
                            <div class="row ">
                                <div class="col-xl-12">
                                    <div class="card custom-card ">
                                        <div class="card-header">
                                            <h5 class="card-title">Modify Service</h5>
                                        </div>
                                        <div class="card-body">
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

                                            <form method="POST" action="{{ route('services.update', $service->id) }}">
                                                @csrf
                                                @method('PUT')

                                                <div class="mb-3">
                                                    <label class="form-label">Service Code</label>
                                                    <input type="text" disabled name="service_code" class="form-control"
                                                        value="{{ $service->service_code }}" readonly>
                                                </div>

                                                <div class="mb-3">
                                                    <label class="form-label">Name</label>
                                                    <input type="text" name="name" class="form-control"
                                                        value="{{ $service->name }}" required>
                                                </div>

                                                <div class="mb-3">
                                                    <label class="form-label">Category</label>
                                                    <select name="category" class="form-select" required>
                                                        <option value="Upgrades"
                                                            {{ $service->category == 'Upgrades' ? 'selected' : '' }}>
                                                            Upgrades</option>
                                                        <option value="Verifications"
                                                            {{ $service->category == 'Verifications' ? 'selected' : '' }}>
                                                            Verifications</option>
                                                        <option value="Airtime"
                                                            {{ $service->category == 'Airtime' ? 'selected' : '' }}>Airtime
                                                        </option>
                                                        <option value="Data"
                                                            {{ $service->category == 'Data' ? 'selected' : '' }}>Data
                                                        </option>
                                                        <option value="A2C"
                                                            {{ $service->category == 'A2C' ? 'selected' : '' }}>A2C</option>
                                                        <option value="Electricity"
                                                            {{ $service->category == 'Electricity' ? 'selected' : '' }}>
                                                            Electricity</option>
                                                        <option value="Cable Sub"
                                                            {{ $service->category == 'Cable Sub' ? 'selected' : '' }}>Cable
                                                            Sub</option>
                                                        <option value="EPIN"
                                                            {{ $service->category == 'EPIN' ? 'selected' : '' }}>EPIN
                                                        </option>
                                                        <option value="Agency"
                                                            {{ $service->category == 'Agency' ? 'selected' : '' }}>Agency
                                                        </option>
                                                        <option value="Charges"
                                                            {{ $service->category == 'Charges' ? 'selected' : '' }}>Charges
                                                        </option>
                                                    </select>
                                                </div>

                                                <div class="mb-3">
                                                    <label class="form-label">Type</label>
                                                    <select name="type" class="form-select" required>
                                                        <option value="BMOD"
                                                            {{ $service->type == 'BMOD' ? 'selected' : '' }}>
                                                            BMOD</option>
                                                        <option value="NIN"
                                                            {{ $service->type == 'NIN' ? 'selected' : '' }}>
                                                            NIN</option>
                                                        <option value="VNIN"
                                                            {{ $service->type == 'VNIN' ? 'selected' : '' }}>VNIN
                                                        </option>
                                                        <option value="PAYOUT"
                                                            {{ $service->type == 'PAYOUT' ? 'selected' : '' }}>PAYOUT
                                                        </option>
                                                        <option value="slip_download"
                                                            {{ $service->type == 'slip_download' ? 'selected' : '' }}>
                                                            SLIP_DOWNLOAD
                                                        </option>
                                                        <option value="bvn_modification"
                                                            {{ $service->type == 'bvn_modification' ? 'selected' : '' }}>
                                                            BVN_MODIFICATION
                                                        </option>
                                                        <option value="nin_modification_general"
                                                            {{ $service->type == 'nin_modification_general' ? 'selected' : '' }}>
                                                            NIN_MODIFICATION_GENERAL
                                                        </option>
                                                        <option value="nin_modification_dob"
                                                            {{ $service->type == 'nin_modification_dob' ? 'selected' : '' }}>
                                                            NIN_MODIFICATION_DOB
                                                        </option>
                                                        <option value="nin_modification_name"
                                                            {{ $service->type == 'nin_modification_name' ? 'selected' : '' }}>
                                                            NIN_MODIFICATION_NAME
                                                        </option>
                                                        <option value="nin_modification_phone"
                                                            {{ $service->type == 'nin_modification_phone' ? 'selected' : '' }}>
                                                             NIN_MODIFICATION_PHONE
                                                        </option>
                                                        <option value="nin_modification_address"
                                                            {{ $service->type == 'nin_modification_address' ? 'selected' : '' }}>
                                                            NIN_MODIFICATION_ADDRESS
                                                        </option>
                                                        <option value="verification_v2"
                                                            {{ $service->type == 'verification_v2' ? 'selected' : '' }}>
                                                            VERIFICATION_V2
                                                        </option>
                                                        <option value="Uncategorized"
                                                            {{ $service->type == 'Uncategorized' || $service->type == null ? 'selected' : '' }}>
                                                            UNCATEGORIZED
                                                        </option>
                                                    </select>
                                                </div>

                                                <div class="mb-3">
                                                    <label class="form-label">Amount (₦)</label>
                                                    <input type="number" name="amount" class="form-control"
                                                        value="{{ $service->amount }}" required>
                                                </div>

                                                <div class="mb-3">
                                                    <label class="form-label">Description</label>
                                                    <textarea name="description" class="form-control">{{ $service->description }}</textarea>
                                                </div>

                                                <div class="mb-3">
                                                    <label class="form-label">Status</label>
                                                    <select name="status" class="form-select" required>
                                                        <option value="enabled"
                                                            {{ $service->status == 'enabled' ? 'selected' : '' }}>Enabled
                                                        </option>
                                                        <option value="disabled"
                                                            {{ $service->status == 'disabled' ? 'selected' : '' }}>Disabled
                                                        </option>
                                                    </select>
                                                </div>

                                                <button type="submit" class="btn btn-primary"><i class="bx bx-save"></i>
                                                    Update Service</button>
                                                <a href="{{ route('services.index') }}" class="btn btn-danger"><i
                                                        class="bx bx-arrow-back"></i> Cancel</a>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            @endsection
