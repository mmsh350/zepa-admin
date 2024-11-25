@extends('layouts.email')
@section('title', 'KYC Status')
@section('content')
    <div class="email-container">
        <!-- Header Section -->
        <div class="email-header">
            <div class="email-logo">
                <img src="{{ asset('assets/kyc/img/kyc-img.png') }}" alt="ZEPA Solutions Logo">
            </div>
            <h2>KYC Status Update</h2>
        </div>

        <!-- Body Section -->
        <div class="email-body">
            @if ($mail_data['type'] == 'Rejected')
                <p>Dear {{ $mail_data['name'] }},</p>
                <p>We regret to inform you that after reviewing your KYC data, we have decided to reject your application.
                    Please provide correct identification details in order to proceed. We appreciate your cooperation and
                    look forward to re-reviewing your application.</p>
                <p>Thank you for your understanding.</p>
            @elseif($mail_data['type'] == 'Verified')
                <p>Dear {{ $mail_data['name'] }},</p>
                <p>We are pleased to inform you that your KYC application has been successfully verified! You can now
                    proceed to access the portal by clicking the button below:</p>
            @endif
        </div>

        <!-- Footer Section -->
        <div class="email-footer">
            <p>Warm regards,</p>
            <p><strong>ZEPA Solutions Team</strong></p>
        </div>
    </div>
@endsection
