@extends('layouts.dashboard')
@section('title', 'SME Services')
@section('content')

    @include('components.app-header')
    @include('components.app-sidebar')

    <div class="main-content app-content">
        <div class="container-fluid">

            <div class="d-md-flex d-block align-items-center justify-content-between my-2 page-header-breadcrumb">
                <div>
                    <p class="fw-semibold fs-18 mb-0">SME Data</p>
                    <span class="fs-semibold text-muted">
                        <p>Manage SME services module</p>
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
                                            <h5 class="card-title">VTPASS Data Variation</h5>
                                        </div>
                                        <div class="card-body">
                                            <a href="{{ route('variation') }}" class="btn btn-danger mb-3"><i
                                                    class='bx bx-refresh'></i>
                                                Refresh Variation</a>


                                            <form method="GET" action="{{ route('sme-service') }}"
                                                class="row g-2 mb-3 align-items-end">
                                                <div class="col-sm-5 col-md-4">
                                                    <input type="text" name="search-variation" class="form-control"
                                                        placeholder="Search..." value="{{ request('search-variation') }}">
                                                </div>

                                                <div class="col-sm-4 col-md-3">
                                                    <select name="per_page" class="form-select"
                                                        onchange="this.form.submit()">
                                                        @foreach ([10, 15, 25, 50, 100] as $size)
                                                            <option value="{{ $size }}"
                                                                {{ request('per_page', 10) == $size ? 'selected' : '' }}>
                                                                Show {{ $size }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>

                                                <div class="col-sm-3 col-md-2">
                                                    <button type="submit" class="btn btn-primary w-100">Filter</button>
                                                </div>
                                            </form>


                                            <div class="table-responsive">
                                                <table class="table text-nowrap" style="background:#fafafc !important">
                                                    <thead>
                                                        <tr>
                                                            <th>SN</th>
                                                            <th>service ID</th>
                                                            <th>Name</th>
                                                            <th>Amount</th>
                                                            <th>Status</th>
                                                            <th>Actions</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach ($dataVariations as $variation)
                                                            <tr>
                                                                <td> {{ $loop->iteration }}</td>
                                                                <td>{{ $variation->service_id }}</td>
                                                                <td>{{ $variation->name }}</td>
                                                                <td>₦ {{ $variation->variation_amount }}</td>
                                                                <td>
                                                                    <span
                                                                        class="badge {{ $variation->status == 'enabled' ? 'bg-success' : 'bg-danger' }}">
                                                                        {{ ucfirst($variation->status) }}
                                                                    </span>
                                                                </td>
                                                                <td>
                                                                    <a href="{{ route('services.variation.edit', $variation->id) }}"
                                                                        class="btn btn-primary btn-sm"><i
                                                                            class="bx bx-edit"></i> Edit</a>
                                                                </td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                                <div class="d-flex justify-content-center mt-3">
                                                    {{ $dataVariations->links('pagination::bootstrap-4') }}
                                                </div>
                                            </div>

                                        </div>
                                    </div>
                                </div>

                                <div class="col-xl-12">
                                    <div class="card custom-card">
                                        <div class="card-header">
                                            <h5 class="card-title">Other SME Plan</h5>
                                        </div>
                                        <div class="card-body">

                                            <a href="{{ route('sme-service.create') }}" class="btn btn-primary mb-3"><i
                                                    class="bx bx-plus"></i> Add New Plan</a>

                                            <form method="GET" action="{{ route('sme-service') }}"
                                                class="row g-2 mb-3 align-items-end">
                                                <div class="col-sm-5 col-md-4">
                                                    <input type="text" name="search" class="form-control"
                                                        placeholder="Search..." value="{{ request('search') }}">
                                                </div>

                                                <div class="col-sm-4 col-md-3">
                                                    <select name="per_page2" class="form-select"
                                                        onchange="this.form.submit()">
                                                        @foreach ([10, 15, 25, 50, 100] as $size)
                                                            <option value="{{ $size }}"
                                                                {{ request('per_page2', 10) == $size ? 'selected' : '' }}>
                                                                Show {{ $size }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>

                                                <div class="col-sm-3 col-md-2">
                                                    <button type="submit" class="btn btn-primary w-100">Filter</button>
                                                </div>
                                            </form>
                                            <div class="table-responsive">
                                                <table class="table text-nowrap" style="background:#fafafc !important">
                                                    <thead>
                                                        <tr>
                                                            <th>SN</th>
                                                            <th>Network</th>
                                                            <th>Plan</th>
                                                            <th>size</th>
                                                            <th>validity</th>
                                                            <th>Amount</th>
                                                            <th>Status</th>
                                                            <th>Actions</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach ($smedatas as $smedata)
                                                            <tr>
                                                                <td> {{ $loop->iteration }}</td>
                                                                <td>{{ $smedata->network }}</td>
                                                                <td>{{ $smedata->plan_type }}</td>
                                                                <td>{{ $smedata->size }}</td>
                                                                <td>{{ $smedata->validity }}</td>
                                                                <td>₦ {{ $smedata->amount }}</td>
                                                                <td>
                                                                    <span
                                                                        class="badge {{ $smedata->status == 'enabled' ? 'bg-success' : 'bg-danger' }}">
                                                                        {{ ucfirst($smedata->status) }}
                                                                    </span>
                                                                </td>
                                                                <td>
                                                                    <a href="{{ route('services.smedata.edit', $smedata->id) }}"
                                                                        class="btn btn-primary btn-sm"><i
                                                                            class="bx bx-edit"></i> Edit</a>
                                                                </td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>

                                            <!-- Pagination -->
                                            <div class="d-flex justify-content-center mt-3">
                                                {{ $smedatas->links('pagination::bootstrap-5') }}
                                            </div>



                                        </div>
                                    </div>
                                </div>

                            @endsection
                            @section('page-css')
                                <style>
                                    /* Service Item Styling */
                                    .service-item {
                                        background: #f8f9fa;
                                        transition: all 0.3s ease-in-out;
                                    }

                                    .service-item:hover {
                                        background: #e9ecef;
                                    }

                                    /* Toggle Switch */
                                    .toggle-switch {
                                        width: 50px;
                                        height: 25px;
                                        cursor: pointer;
                                        appearance: none;
                                        background: #ddd;
                                        border-radius: 20px;
                                        position: relative;
                                        outline: none;
                                        transition: background 0.3s ease-in-out;
                                    }

                                    .toggle-switch:checked {
                                        background: #007bff;
                                    }

                                    .toggle-switch::before {
                                        content: "";
                                        width: 20px;
                                        height: 20px;
                                        background: #fff;
                                        border-radius: 50%;
                                        position: absolute;
                                        top: 50%;
                                        left: 5px;
                                        transform: translateY(-50%);
                                        transition: all 0.3s ease-in-out;
                                    }

                                    .toggle-switch:checked::before {
                                        left: 25px;
                                    }

                                    /* Save Button Animation */
                                    .save-btn:hover {
                                        background: #0056b3;
                                        transform: scale(1.05);
                                        transition: all 0.3s ease-in-out;
                                    }
                                </style>
                            @endsection
