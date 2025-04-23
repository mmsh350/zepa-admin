@extends('layouts.dashboard')
@section('title', 'Request Details')
@section('content')

@section('page-css')
    <link rel="stylesheet" href="{{ asset('assets/libs/quill/quill.snow.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/libs/quill/quill.bubble.css') }}">
@endsection

@include('components.app-header')
@include('components.app-sidebar')

<div class="main-content app-content">
    <div class="container-fluid">
        <!-- Page Header -->
        <div class="d-flex align-items-center justify-content-between my-3">
            <h4 class="mb-0">Request Details </h4>
            <small class="pull-right fw-bold"> (Last Modified - {{ $requests->updated_at }})</small>
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
        <!-- Request Details Card -->
        <div class="row">
            <div class="col-12">
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">Details</h5>

                    </div>
                    <div class="card-body">
                        <div class="mb-4">
                            <div class="p-3 border rounded bg-light">
                                <h6 class="text-uppercase  mb-3"><span class="text-muted">Request Information
                                    </span>- <strong>BVN ENROLLMENT</strong></h6>
                                <div class="row">
                                    <div class="col-md-6">
                                        <p><strong>Reference No.:</strong> {{ strtoupper($requests->refno) }}</p>
                                        <p><strong>Request Type.:</strong> {{ strtoupper($requests->type) }}</p>

                                        <p><strong>BVN NO.:</strong> {{ $requests->bvn }}</p>
                                        <p><strong>Bank Name.:</strong> {{ $requests->bank_name }}</p>
                                        <p><strong>Account Number:</strong> {{ $requests->account_number }}</p>
                                        <p><strong>Account Name.:</strong> {{ $requests->account_name }}</p>

                                        <p><strong>Date:</strong>
                                            {{ \Carbon\Carbon::parse($requests->created_at)->format('d/m/Y') }}</p>
                                        <p><strong>Status:</strong>
                                            @if ($requests->status == 'submitted')
                                                <span class="badge bg-warning">Pending</span>
                                            @elseif($requests->status == 'successful')
                                                <span class="badge bg-success">Successful</span>
                                            @elseif($requests->status == 'processing')
                                                <span class="badge bg-primary">Processing</span>
                                            @else
                                                <span class="badge bg-danger">Rejected</span>
                                            @endif
                                        </p>
                                    </div>
                                    <div class="col-md-6">
                                        <p><strong>Full Name.:</strong> {{ $requests->fullname }}</p>
                                        <p><strong>Username.:</strong> {{ $requests->username }}</p>
                                        <p><strong>Email ID.:</strong> {{ $requests->email }}</p>
                                        <p><strong>Phone No.:</strong> {{ $requests->phone_number }}</p>
                                        <p><strong>State.:</strong> {{ $requests->state }}</p>
                                        <p><strong>LGA.:</strong> {{ $requests->lga }}</p>
                                        <p><strong>Address.:</strong> {{ $requests->address }}</p>
                                    </div>
                                </div>
                                <p><strong>Comments:</strong><br /> {!! $requests->reason !!}</p>
                            </div>
                        </div>

                        <!-- Comment and Action Section -->
                        <div class="p-3 border rounded bg-light">
                            <h6 class="text-uppercase text-muted mb-3">Action</h6>
                            <form action="{{ route('api-update-request-status', [$requests->id, $request_type]) }}"
                                method="POST" id="statusForm">
                                @csrf

                                <!-- Status Selection -->
                                <div class="mb-3">
                                    <label for="status" class="form-label"><strong>Select Status</strong></label>
                                    <select name="status" id="status" class="form-select" required>
                                        <option value="" disabled selected>-- Choose Status --</option>
                                        <option value="successful">Resolved</option>
                                        <option value="processing">Processing</option>
                                    </select>
                                </div>

                                <!-- Quill Editor Section -->
                                <div class="mb-3">
                                    <label for="editor" class="form-label"><strong>Comment</strong></label>
                                    <div id="editor" class="form-control"> </div>
                                    <input type="hidden" name="comment" id="commentInput">
                                </div>

                                <!-- Submit Button -->
                                <button type="submit" class="btn btn-primary w-100">Submit</button>
                            </form>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('page-js')

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize Quill Editor
        const quill = new Quill('#editor', {
            theme: 'snow',
            placeholder: 'Enter your comment...',
        });

        function clear() {
            quill.root.innerHTML = '';
        }
        // Toggle Refund Option
        const statusSelect = document.getElementById('status');
        const refundOption = document.getElementById('refundOption');
        statusSelect.addEventListener('change', function() {
            clear();
            if (this.value === 'processing')
                quill.root.innerHTML =
                "Thank you for reaching out. Your request has been received and is currently being processed. We will notify you promptly upon resolution."

        });

        // Handle Form Submission
        const form = document.getElementById('statusForm');
        form.addEventListener('submit', function(event) {
            // Get Quill content as HTML
            const commentContent = quill.root.innerHTML;
            // Set it in the hidden input
            document.getElementById('commentInput').value = commentContent;

            // Optionally: Validate the comment is not empty
            if (quill.getText().trim().length === 0) {
                event.preventDefault();
                alert('Please add a comment before submitting.');
            }
        });
    });
</script>

<!-- Quill Editor JS -->
<script src="{{ asset('assets/libs/quill/quill.min.js') }}"></script>
@endsection
