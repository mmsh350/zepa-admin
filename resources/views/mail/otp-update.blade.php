@extends('layouts.email')
@section('title', 'OTP Update')
@section('content')
    <div class="email-container">
        <!-- Header Section -->
        <div class="email-header">
            <div class="email-logo">
                 <img src="{{ asset('assets/home/images/logo/logo.png') }}" alt="ZEPA Logo">
            </div>
        </div>

        <!-- Body Section -->
        <div class="email-body">
        <p>Dear {{ ucfirst($name) }},</p>
        <p>Your PIN has been successfully updated.</p>
        <p>If you did not make this change, please contact support immediately.</p>
        </div>

        <!-- Footer Section -->
        <div class="email-footer">
            <p>Warm regards,</p>
            <p><strong>ZEPA Solutions Team</strong></p>
        </div>
    </div>
    @endsection
