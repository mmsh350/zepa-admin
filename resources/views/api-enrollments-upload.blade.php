@extends('layouts.dashboard')

@section('title', 'Enrollment Upload List')

@section('content')
    @include('components.app-header')
    @include('components.app-sidebar')

    <div class="main-content app-content">
        <div class="container-fluid">
            <div class="d-md-flex d-block align-items-center justify-content-between my-3 page-header-breadcrumb">
                <div>
                    <h2 class="fw-semibold fs-18 mb-0">Enrollments Data Upload</h2>
                    <span class="text-muted">Upload and manage user enrollment data easily.</span>
                </div>
            </div>

            <div class="row justify-content-center">
                <div class="col-xxl-12 col-xl-12 col-lg-12">

                    {{-- 🔍 Filter Section --}}
                    <div class="card shadow-sm mb-4">
                        <div class="card-header bg-light">
                            <h5 class="mb-0">🔍 Filter Enrollments</h5>
                        </div>
                        <div class="card-body">
                            <form method="GET" action="{{ route('api.enrollments.upload') }}">
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="ticket_number" class="form-label">Ticket Number</label>
                                        <input type="text" id="ticket_number" name="ticket_number" class="form-control" value="{{ request('ticket_number') }}">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="bvn" class="form-label">BVN</label>
                                        <input type="text" id="bvn" name="bvn" class="form-control" value="{{ request('bvn') }}">
                                    </div>
                                    <div class="col-md-12 mt-2">
                                        <button type="submit" class="btn btn-primary w-100">
                                            <i class="bi bi-search me-1"></i> Search
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>

                    {{-- 📤 Upload Section --}}
                    <div class="card shadow-sm mb-5">
                        <div class="card-header bg-primary text-white">
                            <h5 class="mb-0">📤 Upload Enrollments CSV</h5>
                        </div>
                        <div class="card-body">
                            @if(session('success'))
                                <div class="alert alert-success">{{ session('success') }}</div>
                            @endif

                            @if($errors->any())
                                <div class="alert alert-danger">
                                    <strong>There were some errors with your submission:</strong>
                                    <ul class="mb-0 mt-1">
                                        @foreach($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif

                            <form action="{{ route('api.enrollments.upload.post') }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                <div class="mb-4">
                                    <label for="csv_file" class="form-label fw-semibold">Select CSV File</label>
                                    <input type="file" name="csv_file" id="csv_file" class="form-control" accept=".csv" required>
                                    <small class="text-muted">Only CSV files are allowed. Max size: 2MB.</small>
                                </div>
                                <div class="d-grid">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="bi bi-upload me-1"></i> Upload
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>

                    {{-- 📋 Enrollments Table --}}
                    <div class="card shadow-sm">
                        <div class="card-header bg-light">
                            <h5 class="mb-0">📋 Enrollment Records</h5>
                        </div>
                        <div class="card-body table-responsive">
                           <table class="table table-bordered table-striped">
    <thead class="table-light">
        <tr>
            <th>SN</th>
            <th>Ticket Number</th>
            <th>BVN</th>
            <th>Agent Name</th>
            <th>Agent Code</th>
            <th>BMS Import ID</th>
            <th>Validation Status</th>
            <th>Validation Message</th>
            <th>Validation Date</th>
        </tr>
    </thead>
    <tbody>
        @forelse($data as $enrollment)
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ $enrollment->ticket_number }}</td>
                <td>{{ $enrollment->bvn }}</td>

                <td>{{ $enrollment->agent_name }}</td>
                <td>{{ $enrollment->agent_code }}</td>
                <td>{{ $enrollment->bms_import_id }}</td>
                <td>{{ $enrollment->validation_status }}</td>
                <td>{{ $enrollment->validation_message }}</td>

                <td>{{ $enrollment->validation_date }}</td>
            </tr>
        @empty
            <tr>
                <td colspan="18" class="text-center text-muted">No enrollments found.</td>
            </tr>
        @endforelse
    </tbody>
</table>


                            {{-- Pagination --}}
                            <div class="d-flex justify-content-center mt-4">
                                {{ $data->links('vendor.pagination.bootstrap-5') }}
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
@endsection
