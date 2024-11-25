
@extends('layouts.email')
@section('title', 'OTP Code')
@section('content')
    <div class="email-container">
        <!-- Header Section -->
        <div class="email-header">
            <div class="email-logo">
                  <img src="{{ asset('assets/home/images/logo/logo.png') }}" alt="ZEPA Logo">
            </div>
            <h2>Your OTP Code</h2>
        </div>

        <!-- Body Section -->
        <div class="email-body">
            <p>Dear {{ ucfirst($name) }},</p>
            <p>Your OTP code is: <strong>{{ $otp }}</strong></p>
            <p>This code is valid for a limited time. Please use it promptly.</p>
            <p>If you did not request this code, please ignore this email.</p>
        </div>

        <!-- Footer Section -->
        <div class="email-footer">
            <p>Warm regards,</p>
            <p><strong>ZEPA Solutions Team</strong></p>
        </div>
    </div>
 @endsection
