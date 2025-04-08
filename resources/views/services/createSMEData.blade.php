@extends('layouts.dashboard')
@section('title', 'Services')
@section('content')

    @include('components.app-header')
    @include('components.app-sidebar')

    <div class="main-content app-content">
        <div class="container-fluid">

            <div class="d-md-flex d-block align-items-center justify-content-between my-2 page-header-breadcrumb">
                <div>
                    <p class="fw-semibold fs-18 mb-0">SME Plan</p>
                    <span class="fs-semibold text-muted">

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
                                            <h5 class="card-title">Add Plan</h5>
                                        </div>
                                        <div class="card-body">
                                            <form method="POST" action="{{ route('sme-service.store') }}">
                                                @csrf

                                                <div class="mb-3">
                                                    <label class="form-label">Data ID</label>
                                                    <input type="text" name="data_id" class="form-control"
                                                        value="">
                                                </div>

                                                <div class="mb-3">
                                                    <label class="form-label">Network</label>
                                                    <input type="text" name="network" class="form-control" value=""
                                                        required>
                                                </div>

                                                <div class="mb-3">
                                                    <label class="form-label">Plan Type</label>
                                                    <input type="text" name="plan_type" class="form-control"
                                                        value="" required>
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label">Size</label>
                                                    <input type="text" name="size" class="form-control" value=""
                                                        required>
                                                </div>

                                                <div class="mb-3">
                                                    <label class="form-label">Validity</label>
                                                    <input type="text" name="validity" class="form-control"
                                                        value="" required>
                                                </div>

                                                <div class="mb-3">
                                                    <label class="form-label">Amount (₦)</label>
                                                    <input type="number" name="amount" class="form-control" value=""
                                                        required>
                                                </div>

                                                <button type="submit" class="btn btn-primary">Create Service</button>

                                            </form>
                                        </div>
                                    </div>
                                </div>
                            @endsection
