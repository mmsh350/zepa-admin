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
                        <p>Modify services and service status form this module</p>
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
                                            <h5 class="card-title">Service Status</h5>
                                        </div>
                                        <div class="card-body">
                                            <form method="POST" action="{{ route('services.updateStatus') }}">
                                                @csrf
                                                <div class="row g-3">
                                                    @foreach ($servicesStatus as $service)
                                                        <div class="col-md-6 col-lg-4">
                                                            <div
                                                                class="service-item d-flex align-items-center justify-content-between p-3 rounded shadow-sm">
                                                                <label
                                                                    class="fw-bold m-0">{{ $service->service_name }}</label>
                                                                <input type="checkbox" class="toggle-switch"
                                                                    name="services[]" value="{{ $service->id }}"
                                                                    id="service-{{ $service->id }}"
                                                                    {{ $service->is_enabled ? 'checked' : '' }}>
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                </div>
                                                <div class="text-center mt-4">
                                                    <button type="submit" class="btn btn-primary save-btn">
                                                        <i class="bx bx-save"></i> Save Changes
                                                    </button>
                                                </div>
                                            </form>

                                        </div>
                                    </div>
                                </div>

                                <div class="col-xl-12">
                                    <div class="card custom-card ">
                                        <div class="card-header">
                                            <h5 class="card-title">Other Services</h5>
                                        </div>
                                        <div class="card-body">

                                            <a href="{{ route('services.create') }}" class="btn btn-primary mb-3"><i
                                                    class="bx bx-plus"></i> Add New Service</a>

                                            <table class="table text-nowrap" style="background:#fafafc !important">
                                                <thead>
                                                    <tr>
                                                        <th>SN</th>
                                                        <th>Name</th>
                                                        <th>Category</th>
                                                        <th>Type</th>
                                                        <th>Amount</th>
                                                        <th>Status</th>
                                                        <th>Actions</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach ($services as $service)
                                                        <tr>
                                                            <td> {{ $loop->iteration }}</td>
                                                            <td>{{ $service->name }}</td>
                                                            <td>{{ $service->category }}</td>
                                                            <td>{{ $service->type }}</td>
                                                            <td>₦ {{ $service->amount }}</td>
                                                            <td>
                                                                <span
                                                                    class="badge {{ $service->status == 'enabled' ? 'bg-success' : 'bg-danger' }}">
                                                                    {{ ucfirst($service->status) }}
                                                                </span>
                                                            </td>
                                                            <td>
                                                                <a href="{{ route('services.edit', $service->id) }}"
                                                                    class="btn btn-primary btn-sm"><i
                                                                        class="bx bx-edit"></i> Edit</a>

                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>

                                            <!-- Pagination -->
                                            <div class="d-flex justify-content-center mt-3">
                                                {{ $services->links('pagination::bootstrap-5') }}
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
