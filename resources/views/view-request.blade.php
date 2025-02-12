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
                        <!-- Grid Layout for User and Transaction Details -->
                        <div class="row">
                            <!-- User Details -->
                            <div class="col-md-6 mb-4">
                                <div class="p-3 border rounded bg-light">
                                    <h6 class="text-uppercase text-muted mb-3">Agent Information</h6>
                                    <p><i class="ti ti-user fs-16"></i> &nbsp;<strong>Full Name:</strong>
                                        {{ $requests->user->full_name }}</p>
                                    <p><i class="ti ti-mail fs-16"></i> &nbsp;<strong>Email:</strong>
                                        {{ $requests->user->email }}</p>
                                    <p><i class="ti ti-phone fs-16"></i> &nbsp;<strong>Phone:</strong>
                                        {{ $requests->user->phone_number }}</p>
                                </div>
                            </div>
                            <!-- Transaction Details -->
                            <div class="col-md-6 mb-4">
                                <div class="p-3 border rounded bg-light">
                                    <h6 class="text-uppercase text-muted mb-3">Transaction Information</h6>
                                    <p><strong>Transaction ID:</strong> {{ $requests->transactions->id }}</p>
                                    <p><strong>Amount:</strong> ₦{{ number_format($requests->transactions->amount, 2) }}
                                    </p>
                                    <p><strong>Service Type:</strong>
                                        {{ $requests->transactions->service_type }}
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- Request Details Section -->

                        @if ($request_type == 'crm')
                            <div class="mb-4">
                                <div class="p-3 border rounded bg-light">
                                    <h6 class="text-uppercase  mb-3"><span class="text-muted">Request Information
                                        </span>- <strong>CRM </strong></h6>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <p><strong>Reference No.:</strong> {{ strtoupper($requests->refno) }}</p>
                                            <p><strong>Ticket No.:</strong> {{ $requests->ticket_no }}</p>
                                            <p><strong>BMS ID.:</strong> {{ $requests->bms_ticket_no }}</p>
                                        </div>
                                        <div class="col-md-6">
                                            <p><strong>Date:</strong>
                                                {{ \Carbon\Carbon::parse($requests->created_at)->format('d/m/Y') }}</p>
                                            <p><strong>Status:</strong>
                                                @if ($requests->status == 'pending')
                                                    <span class="badge bg-warning">Pending</span>
                                                @elseif($requests->status == 'resolved')
                                                    <span class="badge bg-success">Resolved</span>
                                                @elseif($requests->status == 'processing')
                                                    <span class="badge bg-primary">Processing</span>
                                                @else
                                                    <span class="badge bg-danger">Rejected</span>
                                                @endif
                                            </p>
                                        </div>
                                    </div>
                                    <p><strong>Comments:</strong><br /> {!! $requests->reason !!}</p>
                                </div>
                            </div>
                        @elseif ($request_type == 'crm2')
                            <div class="mb-4">
                                <div class="p-3 border rounded bg-light">
                                    <h6 class="text-uppercase  mb-3"><span class="text-muted">Request Information
                                        </span>- <strong>CRM With Phone No & DOB</strong></h6>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <p><strong>Reference No.:</strong> {{ strtoupper($requests->refno) }}</p>
                                            <p><strong>Phone No.:</strong> {{ $requests->phoneno }}</p>
                                            <p><strong>Date of Birth.:</strong>
                                                {{ \Carbon\Carbon::parse($requests->dob)->format('d/m/Y') }}</p>
                                        </div>
                                        <div class="col-md-6">
                                            <p><strong>Date:</strong>
                                                {{ \Carbon\Carbon::parse($requests->created_at)->format('d/m/Y') }}</p>
                                            <p><strong>Status:</strong>
                                                @if ($requests->status == 'pending')
                                                    <span class="badge bg-warning">Pending</span>
                                                @elseif($requests->status == 'resolved')
                                                    <span class="badge bg-success">Resolved</span>
                                                @elseif($requests->status == 'processing')
                                                    <span class="badge bg-primary">Processing</span>
                                                @else
                                                    <span class="badge bg-danger">Rejected</span>
                                                @endif
                                            </p>
                                        </div>
                                    </div>
                                    <p><strong>Comments:</strong><br /> {!! $requests->reason !!}</p>
                                </div>
                            </div>
                        @elseif($request_type == 'bvn-enrollment')
                            <div class="mb-4">
                                <div class="p-3 border rounded bg-light">
                                    <h6 class="text-uppercase  mb-3"><span class="text-muted">Request Information
                                        </span>- <strong>BVN ENROLLMENT</strong></h6>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <p><strong>Reference No.:</strong> {{ strtoupper($requests->refno) }}</p>
                                            <p><strong>Request Type.:</strong> {{ strtoupper($requests->type) }}</p>
                                            <p><strong>Wallet ID.:</strong> {{ $requests->wallet_id }}</p>
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
                        @elseif($request_type == 'bvn-modification')
                            <div class="mb-4">
                                <div class="p-3 border rounded bg-light">
                                    <h6 class="text-uppercase mb-3">
                                        <span class="text-muted">Request Information</span> -
                                        <strong>BVN Modifications</strong>
                                    </h6>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <p><strong>Reference No.:</strong> {{ strtoupper($requests->refno) }}</p>
                                            <p><strong>Enrollment Center:</strong> {{ $requests->enrollment_center }}
                                            </p>
                                            <p><strong>Type:</strong> {{ $requests->type }}</p>
                                            <p><strong>BVN:</strong> {{ $requests->bvn_no }}</p>
                                            <p><strong>Modification:</strong> {{ $requests->data_to_modify }}</p>
                                        </div>
                                        <div class="col-md-6">
                                            <p><strong>Date:</strong>
                                                {{ \Carbon\Carbon::parse($requests->created_at)->format('d/m/Y') }}</p>
                                            <p><strong>Status:</strong>
                                                @if ($requests->status == 'pending')
                                                    <span class="badge bg-warning">Pending</span>
                                                @elseif($requests->status == 'resolved')
                                                    <span class="badge bg-success">Resolved</span>
                                                @elseif($requests->status == 'processing')
                                                    <span class="badge bg-primary">Processing</span>
                                                @else
                                                    <span class="badge bg-danger">Rejected</span>
                                                @endif
                                            </p>
                                        </div>
                                    </div>
                                    <p><strong>Comments:</strong><br> {!! $requests->reason !!}</p>
                                    <hr>
                                    <div class="mt-3">
                                        <h6 class="text-uppercase">Documents</h6>
                                        <p>
                                            <a href="{{ route('document.view', ['id' => $requests->id, 'type' => 'bvn-mod']) }}"
                                                class="btn btn-info btn-sm" target="_blank">
                                                <i class="ti ti-eye me-2"></i> View Document
                                            </a>
                                        </p>
                                    </div>
                                </div>

                            </div>
                        @elseif($request_type == 'upgrade')
                            <div class="mb-4">
                                <div class="p-3 border rounded bg-light">
                                    <h6 class="text-uppercase mb-3">
                                        <span class="text-muted">Request Information</span> -
                                        <strong>Account Upgrade</strong>
                                    </h6>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <p><strong>Reference No.:</strong> {{ strtoupper($requests->refno) }}</p>
                                            <p><strong>Date:</strong>
                                                {{ \Carbon\Carbon::parse($requests->created_at)->format('d/m/Y') }}</p>
                                            <p><strong>Status:</strong>
                                                @if ($requests->status == 'pending')
                                                    <span class="badge bg-warning">Pending</span>
                                                @elseif($requests->status == 'resolved')
                                                    <span class="badge bg-success">Resolved</span>
                                                @elseif($requests->status == 'processing')
                                                    <span class="badge bg-primary">Processing</span>
                                                @else
                                                    <span class="badge bg-danger">Rejected</span>
                                                @endif
                                            </p>
                                        </div>
                                    </div>
                                    <div class="mt-1">
                                        <h6 class="text-uppercase">Documents</h6>
                                        <p>
                                            <a href="{{ route('document.view', ['id' => $requests->id, 'type' => 'upgrade']) }}
"
                                                class="btn btn-info btn-sm" target="_blank">
                                                <i class="ti ti-eye me-2"></i> View Document
                                            </a>
                                        </p>
                                    </div>
                                    <p><strong>Comments:</strong><br> {!! $requests->reason !!}</p>
                                    <hr>
                                </div>

                            </div>
                        @elseif($request_type == 'nin-services')
                            <div class="mb-4">
                                <div class="p-3 border rounded bg-light">
                                    <h6 class="text-uppercase mb-3">
                                        <span class="text-muted">Request Information</span> -
                                        <strong>NIN SERVICE</strong>
                                    </h6>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <p><strong>Reference No.:</strong> {{ strtoupper($requests->refno) }}</p>
                                            <p><strong>Tracking / NIN Number.:</strong>
                                                {{ strtoupper($requests->trackingId) }}</p>
                                            <p><strong>Service Type.:</strong>
                                                {{ strtoupper($requests->service_type) }}</p>
                                            <p><strong>Date:</strong>
                                                {{ \Carbon\Carbon::parse($requests->created_at)->format('d/m/Y') }}</p>
                                            <p><strong>Status:</strong>
                                                @if ($requests->status == 'pending')
                                                    <span class="badge bg-warning">Pending</span>
                                                @elseif($requests->status == 'resolved')
                                                    <span class="badge bg-success">Resolved</span>
                                                @elseif($requests->status == 'processing')
                                                    <span class="badge bg-primary">Processing</span>
                                                @else
                                                    <span class="badge bg-danger">Rejected</span>
                                                @endif
                                            </p>
                                        </div>
                                    </div>

                                    <p class="mt-3"><strong>Comments:</strong><br> {!! $requests->reason !!}</p>
                                    <hr>
                                </div>

                            </div>
                        @else
                            <div class="mb-4">
                                <div class="p-3 border rounded bg-light">
                                    <h6 class="text-uppercase mb-3">
                                        <span class="text-muted">Request Information</span> -
                                        <strong>VNIN SERVICE</strong>
                                    </h6>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <p><strong>Reference No.:</strong> {{ strtoupper($requests->refno) }}</p>
                                            <p><strong>Request ID.:</strong> {{ strtoupper($requests->requestId) }}</p>
                                            <p><strong>NIN Number.:</strong> {{ strtoupper($requests->nin_number) }}
                                            </p>
                                            <p><strong>BVN Number.:</strong> {{ strtoupper($requests->bvn_number) }}
                                            </p>
                                            <p><strong>Date:</strong>
                                                {{ \Carbon\Carbon::parse($requests->created_at)->format('d/m/Y') }}</p>
                                            <p><strong>Status:</strong>
                                                @if ($requests->status == 'pending')
                                                    <span class="badge bg-warning">Pending</span>
                                                @elseif($requests->status == 'resolved')
                                                    <span class="badge bg-success">Resolved</span>
                                                @elseif($requests->status == 'processing')
                                                    <span class="badge bg-primary">Processing</span>
                                                @else
                                                    <span class="badge bg-danger">Rejected</span>
                                                @endif
                                            </p>
                                        </div>
                                    </div>

                                    <p class="mt-3"><strong>Comments:</strong><br> {!! $requests->reason !!}</p>
                                    <hr>
                                </div>

                            </div>
                        @endif
                        <!-- Comment and Action Section -->
                        <div class="p-3 border rounded bg-light">
                            <h6 class="text-uppercase text-muted mb-3">Action</h6>
                            <form action="{{ route('update-request-status', [$requests->id, $request_type]) }}"
                                method="POST" id="statusForm">
                                @csrf

                                <!-- Status Selection -->
                                <div class="mb-3">
                                    <label for="status" class="form-label"><strong>Select Status</strong></label>
                                    <select name="status" id="status" class="form-select" required>
                                        <option value="" disabled selected>-- Choose Status --</option>
                                        <option value="resolved">Resolved</option>
                                        <option value="processing">Processing</option>
                                        <option value="rejected">Rejected</option>
                                    </select>
                                </div>

                                <!-- Refund Option -->
                                <div class="mb-3 d-none" id="refundOption">
                                    <label class="form-label"><strong>Refund Options</strong></label>

                                    <!-- Percentage Selection -->
                                    <div class="d-flex gap-3">
                                        <div class="form-check">
                                            <input type="radio" name="refund_percentage" value="10"
                                                id="refund10" class="form-check-input refund-percentage">
                                            <label for="refund10" class="form-check-label">10%</label>
                                        </div>
                                        <div class="form-check">
                                            <input type="radio" name="refund_percentage" value="20"
                                                id="refund20" class="form-check-input refund-percentage">
                                            <label for="refund20" class="form-check-label">20%</label>
                                        </div>
                                        <div class="form-check">
                                            <input type="radio" name="refund_percentage" value="30"
                                                id="refund30" class="form-check-input refund-percentage">
                                            <label for="refund30" class="form-check-label">30%</label>
                                        </div>
                                        <div class="form-check">
                                            <input type="radio" name="refund_percentage" value="50"
                                                id="refund50" class="form-check-input refund-percentage">
                                            <label for="refund50" class="form-check-label">50%</label>
                                        </div>
                                    </div>


                                    <!-- Calculated Refund Amount -->
                                    <div class="mt-3">
                                        <label for="refundAmount" class="form-label"><strong>Refund Amount
                                                (₦)</strong></label>
                                        <input type="text" id="refundAmount" name="refundAmount"
                                            class="form-control">
                                    </div>
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
            if (this.value === 'rejected') {

                refundOption.classList.remove('d-none');
            } else if (this.value === 'processing') {
                quill.root.innerHTML =
                    "Thank you for reaching out. Your request has been received and is currently being processed. We will notify you promptly upon resolution."
            } else {
                refundOption.classList.add('d-none');
            }
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
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const statusSelect = document.getElementById('status');
        const refundOption = document.getElementById('refundOption');
        const refundAmountInput = document.getElementById('refundAmount');
        const refundPercentageRadios = document.querySelectorAll('.refund-percentage');

        // Transaction amount (Replace with actual value if dynamic)
        const transactionAmount = {{ $requests->transactions->amount }};

        // Show or hide refund option based on status
        statusSelect.addEventListener('change', function() {
            if (this.value === 'rejected') {
                refundOption.classList.remove('d-none');
                refundAmountInput.setAttribute('required', 'required');

            } else {
                refundOption.classList.add('d-none');
                refundAmountInput.removeAttribute('required');
                refundAmountInput.value = '';
                refundPercentageRadios.forEach(radio => (radio.checked = false));
            }
        });

        // Calculate refund amount based on selected percentage
        refundPercentageRadios.forEach(radio => {
            radio.addEventListener('change', function() {
                const percentage = parseInt(this.value, 10);
                const refundAmount = (transactionAmount * percentage) / 100;
                refundAmountInput.value = `${refundAmount}`;
            });
        });
    });
</script>

<!-- Quill Editor JS -->
<script src="{{ asset('assets/libs/quill/quill.min.js') }}"></script>

<!-- Internal Quill JS -->

@endsection
