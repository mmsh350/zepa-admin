@extends('layouts.dashboard')
@section('title', 'User Management')
@section('content')

    @include('components.app-header')
    @include('components.app-sidebar')

    <div class="main-content app-content">
        <div class="container-fluid">

            <div class="d-md-flex d-block align-items-center justify-content-between my-2 page-header-breadcrumb">
                <div>
                    <p class="fw-semibold fs-18 mb-0">User Management</p>
                    <span class="fs-semibold text-muted">
                        <p>Access control and permission assignment</p>
                    </span>
                </div>
            </div>

            <div class="row">
                <div class="col-xxl-12 col-xl-12">

<div class="card custom-card">

                            <div class="card-body">
                                <ul class="nav nav-tabs mb-3 border-0" role="tablist">
                                    <li class="nav-item" role="presentation">
                                        <a class="nav-link active" data-bs-toggle="tab" role="tab" href="#profile" aria-selected="false" tabindex="-1">Profile</a>
                                    </li>
                                    <li class="nav-item" role="presentation">
                                        <a class="nav-link " data-bs-toggle="tab" role="tab" href="#transaction" aria-selected="true">Transactions</a>
                                    </li>
                                </ul>
                                <div class="tab-content">
                                    <div class="tab-pane text-muted active show" id="profile" role="tabpanel">
                                        <div class="row">
                                                <div class="col-md-4 border-end text-center">
                                                    <div class="mb-3 mt-3">
                                                        @if ($user->profile_pic)
                                                            <img src="data:image/jpeg;base64,{{ $user->profile_pic }}"
                                                                class="rounded-circle shadow" alt="Profile Picture"
                                                                style="width: 200px; height: 200px;  ">
                                                        @else
                                                            <img src="https://via.placeholder.com/150"
                                                                class="rounded-circle shadow" alt="No Image">
                                                        @endif
                                                    </div>
                                                    <h5 class="mb-0">{{ $user->first_name }} {{ $user->last_name }}</h5>
                                                    <p class="text-muted">{{ '@' . $user->username }}</p>
                                                    <span class="badge bg-{{ $user->is_active ? 'success' : 'secondary' }}">
                                                        {{ $user->is_active ? 'Active' : 'Inactive' }}
                                                    </span>
                                                    @if ($user->refferral_id && ($referrer = \App\Models\User::find($user->refferral_id)))
                                                        <div class="mt-4">
                                                            <h6 class="text-muted">Referred By:</h6>
                                                            <p class="mb-0">{{ $referrer->first_name }}
                                                                {{ $referrer->last_name }}</p>
                                                            <small class="text-muted">({{ $referrer->email }})</small>
                                                        </div>
                                                    @endif
                                                </div>
                                                <div class="col-md-8 mt-2">
                                                    <h4 class="mb-3">Account Details</h4>
                                                    <div class="row mb-2">
                                                        <div class="col-sm-4 text-muted">Email</div>
                                                        <div class="col-sm-8">{{ $user->email }}</div>
                                                    </div>
                                                    <div class="row mb-2">
                                                        <div class="col-sm-4 text-muted">Phone Number</div>
                                                        <div class="col-sm-8">{{ $user->phone_number }}</div>
                                                    </div>
                                                    <div class="row mb-2">
                                                        <div class="col-sm-4 text-muted">Date of Birth</div>
                                                        <div class="col-sm-8">
                                                            @if ($user->dob)
                                                                {{ \Carbon\Carbon::parse($user->dob)->format('d/m/Y') }}
                                                            @else
                                                                <span class="text-muted"> <i
                                                                        class="bx bx-info-circle text-muted"> </i> Missing
                                                                </span>
                                                            @endif
                                                        </div>
                                                    </div>
                                                    <div class="row mb-2">
                                                        <div class="col-sm-4 text-muted">Gender</div>
                                                        <div class="col-sm-8">
                                                            @if ($user->gender)
                                                                {{ ucfirst($user->gender) }}
                                                            @else
                                                                <span class="text-muted"><i
                                                                        class="bx bx-info-circle text-muted"></i>
                                                                    Missing</span>
                                                            @endif
                                                        </div>
                                                    </div>
                                                    <div class="row mb-2">
                                                        <div class="col-sm-4 text-muted">Role</div>
                                                        <div class="col-sm-8">{{ ucfirst($user->role) }}</div>
                                                    </div>
                                                    <div class="row mb-2">
                                                        <div class="col-sm-4 text-muted">KYC Status</div>
                                                        <div class="col-sm-8">{{ $user->kyc_status }}</div>
                                                    </div>
                                                    <div class="row mb-2">
                                                        <div class="col-sm-4 text-muted">ID Type / Number</div>
                                                        <div class="col-sm-8">{{ $user->idType }} / {{ $user->idNumber }}
                                                        </div>
                                                    </div>
                                                    <div class="row mb-2">
                                                        <div class="col-sm-4 text-muted">Email Verified</div>
                                                        <div class="col-sm-8">
                                                            {{ $user->email_verified_at ? $user->email_verified_at->format('d/m/Y') : 'Not Verified' }}
                                                        </div>
                                                    </div>
                                                    <div class="row mb-2">
                                                        <div class="col-sm-4 text-muted">Transaction Pin</div>
                                                        <div class="col-sm-8">{{ $user->pin ? 'Created' : 'Not Created' }}
                                                        </div>
                                                    </div>
                                                    <div class="row mb-2">
                                                        <div class="col-sm-4 text-muted">Wallet Balance</div>
                                                        <div class="col-sm-8">
                                                            ₦{{ number_format(optional($user->wallet)->balance, 2) }}</div>
                                                    </div>

                                                    <hr>

                                                    <h5 class="mb-3">Other Info</h5>
                                                    <div class="row mb-2">
                                                        <div class="col-sm-4 text-muted">Wallet Created</div>
                                                        <div class="col-sm-8">{{ $user->wallet_is_created ? 'Yes' : 'No' }}
                                                        </div>
                                                    </div>
                                                    <div class="row mb-2">
                                                        <div class="col-sm-4 text-muted">Virtual Wallet</div>
                                                        <div class="col-sm-8">
                                                            {{ $user->vwallet_is_created ? 'Yes' : 'No' }}</div>
                                                    </div>
                                                    <div class="row mb-2">
                                                        <div class="col-sm-4 text-muted">Referral Code / Bonus</div>
                                                        <div class="col-sm-8">{{ strtoupper($user->referral_code) }} /
                                                            {{ $user->referral_bonus }}</div>
                                                    </div>
                                                    <div class="row mb-2">
                                                        <div class="col-sm-4 text-muted">Notifications</div>
                                                        <div class="col-sm-8">
                                                            {{ $user->notification ? 'Sound Set' : 'Not Set' }}</div>
                                                    </div>
                                                    <div class="row mb-2">
                                                        <div class="col-sm-4 text-muted">Daily Limit</div>
                                                        <div class="col-sm-8"> ₦{{ number_format($user->daily_limit, 2) }}
                                                        </div>
                                                    </div>
                                                    <div class="row mb-2">
                                                        <div class="col-sm-4 text-muted">Sign In Times</div>
                                                        <div class="col-sm-8">
                                                            Current: {{ $user->current_sign_in_at ?? '-' }}<br>
                                                            Last: {{ $user->last_sign_in_at ?? '-' }}
                                                        </div>
                                                    </div>
                                                    <div class="row mb-2">
                                                        <div class="col-sm-4 text-muted">Created At</div>
                                                        <div class="col-sm-8">{{ $user->created_at->format('d/m/Y') }}
                                                        </div>
                                                    </div>
                                                    <div class="row mb-2">
                                                        <div class="col-sm-4 text-muted">Updated At</div>
                                                        <div class="col-sm-8">{{ $user->updated_at->format('d/m/Y') }}
                                                        </div>
                                                    </div>

                                                    <div class="mt-4">
                                                        <a href="{{ route('users.index') }}" class="btn btn-secondary">Back
                                                            to Users</a>
                                                        <a href="{{ route('users.edit', $user) }}"
                                                            class="btn btn-primary">Edit User</a>
                                                    </div>
                                                </div>
                                            </div>
                                    </div>
                                    <div class="tab-pane text-muted " id="transaction" role="tabpanel">
                                        <div class="row">
<div class="table-responsive">
<p>Lastest 20 Records</p>
    <small class="text-danger">Click on the reference number to generate a transaction receipt or use the download button</small>

    @if ($transactions->count())

        <table class="table text-nowrap" style="background:#fafafc !important">
            <thead>
                <tr class="table-primary">
                    <th>#</th>
                    <th>Reference No.</th>
                    <th>Service Type</th>
                    <th>Description</th>
                    <th>Amount</th>
                    <th class="text-center">Status</th>
                    <th class="text-center">Receipt</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($transactions as $data)
                    <tr>
                        <td>{{  $loop->iteration }}</td>
                        <td>
                            <a target="_blank" href="{{ route('reciept', $data->referenceId) }}">
                                {{ strtoupper($data->referenceId) }}
                            </a>
                        </td>
                        <td>{{ $data->service_type }}</td>
                        <td>{{ $data->service_description }}</td>
                        <td>&#8358;{{ number_format($data->amount, 2) }}</td>
                        <td class="text-center">
                            <span class="badge
                                {{ $data->status == 'Approved' ? 'bg-outline-success' :
                                    ($data->status == 'Rejected' ? 'bg-outline-danger' : 'bg-outline-warning') }}">
                                {{ strtoupper($data->status) }}
                            </span>
                        </td>
                        <td class="text-center">
                            <a target="_blank" href="{{ route('reciept', $data->referenceId) }}" class="btn btn-outline-primary btn-sm">
                                <i class="bi bi-download"></i> Download
                            </a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>


    @else
        <center>
            <img width="65%" src="{{ asset('assets/images/no-transaction.gif') }}" alt="">
            <p class="text-center fw-semibold fs-15 mt-2">No Transaction Available!</p>
        </center>
    @endif
</div>

                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endsection
