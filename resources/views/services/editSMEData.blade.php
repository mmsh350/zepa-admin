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
                    <p class="fw-semibold fs-18 mb-0">Modify SME Data Services</p>
                    <span class="fs-semibold text-muted">

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

                                            <form method="POST"
                                                action="{{ route('services.smedata.update', $service->id) }}">
                                                @csrf
                                                @method('PUT')

                                                <div class="mb-3">
                                                    <label class="form-label">Data ID</label>
                                                    <input type="text" readonly disabled name="data_id"
                                                        class="form-control" value="{{ $service->data_id }}">
                                                </div>

                                                <div class="mb-3">
                                                    <label class="form-label">Network</label>
                                                    <input type="text" name="network" class="form-control"
                                                        value="{{ $service->network }}" required>
                                                </div>

                                                <div class="mb-3">
                                                    <label class="form-label">Plan Type</label>
                                                    <input type="text" name="plan_type" class="form-control"
                                                        value="{{ $service->plan_type }}" required>
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label">Size</label>
                                                    <input type="text" name="size" class="form-control"
                                                        value="{{ $service->size }}" required>
                                                </div>

                                                <div class="mb-3">
                                                    <label class="form-label">Validity</label>
                                                    <input type="text" name="validity" class="form-control"
                                                        value="{{ $service->validity }}" required>
                                                </div>

                                                <div class="mb-3">
                                                    <label class="form-label">Amount (₦)</label>
                                                    <input type="number" name="amount" class="form-control"
                                                        value="{{ $service->amount }}" required>
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
                                                <a href="{{ route('sme-service') }}" class="btn btn-danger"><i
                                                        class="bx bx-arrow-back"></i> Cancel</a>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            @endsection
