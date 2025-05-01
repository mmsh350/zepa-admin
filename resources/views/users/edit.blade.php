@extends('layouts.dashboard')
@section('title', 'Edit User')
@section('content')

    @include('components.app-header')
    @include('components.app-sidebar')

    <div class="main-content app-content">
        <div class="container-fluid">

            <div class="d-md-flex d-block align-items-center justify-content-between my-2 page-header-breadcrumb">
                <div>
                    <p class="fw-semibold fs-18 mb-0">Edit User</p>
                    <span class="fs-semibold text-muted">
                        <p>Update user details and settings</p>
                    </span>
                </div>
            </div>
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
                    <div class="card custom-card">
                        <div class="card-body">
                            <form action="{{ route('users.update', $user->id) }}" method="POST"
                                enctype="multipart/form-data" id="form">
                                @csrf
                                @method('PUT')

                                <div class="row">
                                    <!-- Left Sidebar with Profile Image -->
                                    <div class="col-md-4 text-center">
                                        <div class="mb-3 mt-3">
                                            @if ($user->profile_pic)
                                                <img src="data:image/jpeg;base64,{{ $user->profile_pic }}"
                                                    class="rounded-circle shadow" alt="Profile Picture"
                                                    style="width: 200px; height: 200px; object-fit: cover;">
                                            @else
                                                <img src="https://via.placeholder.com/150" class="rounded-circle shadow"
                                                    alt="No Image">
                                            @endif
                                        </div>

                                        <div class="mb-3">
                                            <label for="profile_pic" class="form-label">Change Profile Picture</label>
                                            <input type="file" class="form-control" name="profile_pic" id="profile_pic">
                                        </div>

                                    </div>

                                    <!-- Right Content for Editing -->
                                    <div class="col-md-8">
                                        <h4 class="mb-3">Account Details</h4>

                                        <div class="row mb-2">
                                            <div class="col-sm-4 text-muted">First Name</div>
                                            <div class="col-sm-8">
                                                <input type="text" class="form-control" name="first_name"
                                                    value="{{ old('first_name', $user->first_name) }}">
                                            </div>
                                        </div>

                                        <div class="row mb-2">
                                            <div class="col-sm-4 text-muted">Last Name</div>
                                            <div class="col-sm-8">
                                                <input type="text" class="form-control" name="last_name"
                                                    value="{{ old('last_name', $user->last_name) }}">
                                            </div>
                                        </div>

                                        <div class="row mb-2">
                                            <div class="col-sm-4 text-muted">Email</div>
                                            <div class="col-sm-8">
                                                <input type="email" class="form-control" name="email"
                                                    value="{{ old('email', $user->email) }}">
                                            </div>
                                        </div>

                                        <div class="row mb-2">
                                            <div class="col-sm-4 text-muted">Phone Number</div>
                                            <div class="col-sm-8">
                                                <input type="text" class="form-control" name="phone_number"
                                                    value="{{ old('phone_number', $user->phone_number) }}">
                                            </div>
                                        </div>

                                        <div class="row mb-2">
                                            <div class="col-sm-4 text-muted">Date of Birth</div>
                                            <div class="col-sm-8">
                                                <input type="date" class="form-control" name="dob"
                                                    value="{{ old('dob', $user->dob) }}">
                                            </div>
                                        </div>

                                        <div class="row mb-2">
                                            <div class="col-sm-4 text-muted">Gender</div>
                                            <div class="col-sm-8">
                                                <select name="gender" class="form-control">
                                                    <option value="male" {{ $user->gender == 'male' ? 'selected' : '' }}>
                                                        Male</option>
                                                    <option value="female"
                                                        {{ $user->gender == 'female' ? 'selected' : '' }}>Female</option>
                                                </select>
                                            </div>
                                        </div>

                                        <div class="row mb-2">
                                            <div class="col-sm-4 text-muted">Role</div>
                                            <div class="col-sm-8">
                                                <select name="role" class="form-control">
                                                    <option value="admin" {{ $user->role == 'admin' ? 'selected' : '' }}>
                                                        Admin</option>
                                                    <option value="user" {{ $user->role == 'user' ? 'selected' : '' }}>
                                                        User</option>
                                                    <option value="agent" {{ $user->role == 'agent' ? 'selected' : '' }}>
                                                        Agent</option>
                                                </select>
                                            </div>
                                        </div>

                                        <h5 class="mt-4">Other Info</h5>
                                        <div class="row mb-2">

                                            <div class="col-sm-4 text-muted">Wallet
                                                Balance - ({{ optional($user->wallet)->balance }})</div>
                                            <div class="col-sm-8">
                                                <input type="number" step="0.01" class="form-control"
                                                    name="wallet_balance"
                                                    placeholder="Only provide when necessary, select type below" value="">
                                            </div>

                                        </div>

                                          <div class="row mb-2">
                                            <div class="col-sm-4 text-muted">Top Up Type</div>
                                            <div class="col-sm-8">
                                                <select name="topup_type" class="form-control">
                                                     <option value="" selected> Choose</option>
                                                    <option value="1"> Credit </option>
                                                    <option value="2"> Debit </option>
                                                </select>
                                            </div>
                                        </div>

                                        <div class="row mb-2">
                                            <div class="col-sm-4 text-muted">Daily Limit</div>
                                            <div class="col-sm-8">
                                                <input type="number" step="0.01" class="form-control"
                                                    name="daily_limit"
                                                    value="{{ old('daily_limit', $user->daily_limit) }}">
                                            </div>
                                        </div>

                                        <div class="row mb-2">
                                            <div class="col-sm-4 text-muted">Referral Code</div>
                                            <div class="col-sm-8">
                                                <input type="text" class="form-control" maxlength="6"
                                                    name="referral_code"
                                                    value="{{ old('referral_code', $user->referral_code) }}">
                                            </div>
                                        </div>

                                        <div class="row mb-2">
                                            <div class="col-sm-4 text-muted">Referral Bonus</div>
                                            <div class="col-sm-8">
                                                <input type="number" step="0.01" class="form-control"
                                                    name="referral_bonus"
                                                    value="{{ old('referral_bonus', $user->referral_bonus) }}">
                                            </div>
                                        </div>

                                        <div class="mt-4">
                                            <a href="{{ route('users.index') }}" class="btn btn-secondary">Back to
                                                Users</a>
                                            <button type="submit" class="btn btn-primary">Update User</button>
                                        </div>
                                    </div>
                                </div>
                            </form>
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
            const form = document.getElementById('form');

            if (form) {
                form.addEventListener('submit', function(e) {
                    const confirmed = confirm('Are you sure you want to update this user?');
                    if (!confirmed) {
                        e.preventDefault();
                    }
                });
            }
        });
    </script>
@endsection
