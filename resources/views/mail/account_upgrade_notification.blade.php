@extends('layouts.email')
@section('title', 'KYC Status')
@section('content')
    <div class="email-container">
        <!-- Header Section -->
        <div class="email-header">
            <div class="email-logo">
                <img src="{{ asset('assets/kyc/img/kyc-img.png') }}" alt="ZEPA Solutions Logo">
            </div>
        </div>

        <!-- Body Section -->
        <div class="email-body">
            @if ($mail_data['type'] == 'Rejected')
                <p>Dear {{ $mail_data['name'] }},</p>
                <p> We regret to inform you that your request for an account upgrade on our site has been rejected. After
                    reviewing your information, we require additional verification to proceed with the upgrade. Please
                    contact support and
                    provide the necessary documentation to complete the verification process. We appreciate your cooperation
                    and look forward to re-evaluating your request.</p>
                <p>Thank you for your understanding.</p>
            @elseif($mail_data['type'] == 'Approved')
                <p>Dear {{ $mail_data['name'] }},</p>
                <p>We are pleased to inform you that your request for an account upgrade on our site has been approved. Your
                    account has been successfully upgraded, and you now have access to the additional features and benefits.
                </p>
                <p>Thank you for your continued loyalty and cooperation. If you have any questions or need assistance,
                    please don't hesitate to contact us.</p>
            @endif
        </div>
        <!-- Footer Section -->
        <div class="email-footer">
            <p>Warm regards,</p>
            <p><strong>ZEPA Solutions Team</strong></p>
        </div>
    </div>
@endsection
