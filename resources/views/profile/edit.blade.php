@extends('layouts.dashboard')
@section('title', 'Settings')
@section('content')
    <div class="page">
        <!------App Header ----->
        @include('components.app-header')
        <!-- Start::app-sidebar -->

        @include('components.app-sidebar')

        <!-- Start::app-content -->
        <div class="main-content app-content custom-margin-top">
            <div class="container-fluid">

                <!-- Start::Password Update Section -->
                <div class="row mt-4">
                    <div class="col-xxl-12  col-md-6">
                        <div class="card custom-card mb-4">
                            <div class="card-header">
                                <h5 class="card-title">Update Password</h5>
                            </div>
                            <div class="card-body">
                                @if (session('status'))
                                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                                        Password Update Successful.
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

                                <form id="password-form" method="post" action="{{ route('profile.update') }}">
                                    @csrf
                                    @method('put')
                                    <div class="mb-3">
                                        <label for="current_password" class="form-label">Current Password</label>
                                        <input type="password" class="form-control" id="current_password"
                                            name="current_password" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="new_password" class="form-label">New Password</label>
                                        <input type="password" class="form-control" id="new_password" name="new_password"
                                            required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="new_password_confirmation" class="form-label">Confirm New
                                            Password</label>
                                        <input type="password" class="form-control" id="new_password_confirmation"
                                            name="new_password_confirmation" required>
                                    </div>
                                    <button type="submit" id="change_password" class="btn btn-primary">Update
                                        Password</button>
                                </form>
                            </div>
                        </div>
                    </div>


                    <!-- Start::PIN Modification Section -->

                    <div class="col-xxl-12  col-md-6">
                        <div class="card custom-card mb-4">
                            <div class="card-header">
                                <h5 class="card-title">Create/Update Transaction PIN</h5>
                            </div>
                            <div class="card-body">
                                <small class="text-dark">To create or update your PIN, enter your password and the One-Time
                                    Password (OTP) sent to your registered email."</small>
                                <div class="mb-2 mt-2" id="errMsg"></div>
                                <form id="update-pin-form">
                                    @csrf
                                    <div class="mb-3">
                                        <label for="password_for_pin" class="form-label">Enter Your Password</label>
                                        <input type="password" class="form-control" id="password_for_pin" name="password"
                                            required>
                                    </div>
                                    <button type="submit" id="send-otp" class="btn btn-primary"> Send OTP
                                        <div class="lds-ring" id="spinner">
                                            <div></div>
                                            <div></div>
                                            <div></div>
                                            <div></div>
                                        </div>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>

                    <!-- OTP Modal -->
                    <div class="modal fade" id="otpModal" tabindex="-1" aria-labelledby="otpModalLabel"
                        aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="otpModalLabel">Enter OTP</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                        aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <div class="mb-2" id="modal_err"></div>
                                    <form id="otp-form">
                                        @csrf
                                        <div class="mb-3">
                                            <p>OTP sent to your registered email address. Please check your inbox. <br />
                                            </p>
                                            <label for="otp" class="form-label">OTP</label>
                                            <input type="text" class="form-control" maxlength="6" id="otp"
                                                name="otp" required>
                                        </div>
                                        <div class="mb-3">
                                            <label for="new_pin" class="form-label">New PIN</label>
                                            <input type="text" class="form-control" maxlength="4" id="new_pin"
                                                name="pin" required>
                                        </div>
                                    </form>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Close</button>
                                    <button type="button" class="btn btn-primary" id="verify-otp">Verify OTP
                                        <div class="lds-ring" id="spinner2">
                                            <div></div>
                                            <div></div>
                                            <div></div>
                                            <div></div>
                                        </div>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>


                    <!-- Start::Notification Settings Section -->

                    <div class="col-xxl-12  col-md-6">
                        <div class="card custom-card mb-4">
                            <div class="card-header">
                                <h5 class="card-title">Notification Settings</h5>
                            </div>
                            <div class="card-body">
                                @if (session('success'))
                                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                                        {{ session('success') }}
                                    </div>
                                @endif
                                <form id="notify-form" method="post" action="{{ route('notification.update') }}">
                                    @csrf
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="notification_sound"
                                            name="notification_sound" {{ $notificationsEnabled ? 'checked' : '' }}>
                                        <label class="form-check-label" for="notification_sound">
                                            Enable Notification Sound
                                        </label>
                                    </div>
                                    <button type="submit" id="notify" class="btn btn-primary mt-3">Save
                                        Settings</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>


            </div>
        </div>
        <!-- End::row-1 -->
    </div>
@endsection
@section('page-js')
    <script>
        const pinVerifyRoute = @json(route('pin.verify'));
        const pinUpdateRoute = @json(route('pin.update'));
    </script>
    <script src="{{ asset('assets/js/settings.js') }}"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Select the form and the submit button
            const form = document.getElementById('password-form');
            const submitButton = document.getElementById('change_password');

            // Add event listener to the form
            if (form && submitButton) {
                form.addEventListener('submit', function(event) {
                    // Prevent multiple submissions
                    if (!submitButton.disabled) {
                        submitButton.disabled = true;
                        submitButton.innerText = 'Processing request...';
                    }
                });
            }

            const form2 = document.getElementById('notify-form');
            // Select the notify button
            const notifyButton = document.getElementById('notify');

            // Add event listener to the notify button
            if (form2 && notifyButton) {
                form2.addEventListener('submit', function() {
                    if (!notifyButton.disabled) {
                        notifyButton.disabled = true;
                        notifyButton.innerText = 'Processing request...';
                    }
                });
            }
        });
    </script>
@endsection
