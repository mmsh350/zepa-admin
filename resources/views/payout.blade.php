@extends('layouts.dashboard')
@section('title', 'Transfer')
@section('content')
@section('page-css')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet">
@endsection
<!------App Header ----->
@include('components.app-header')
<!-- Start::app-sidebar -->

@include('components.app-sidebar')
<div class="main-content app-content">
    <div class="container-fluid">
        <!-- Start::page-header -->

        <!-- End::page-header -->
        <!-- Start::row-1 -->
        <div class="row">
            <div class="col-xxl-12 col-xl-12">
                <div class="row">

                    <div class="col-xl-12">
                        <div class="row">
                            <div class="col-xl-12">
                                <div class="card custom-card mt-4">
                                    <div class="card-header justify-content-between">
                                        <div class="card-title">
                                            Transfer to bank Account
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        <small> Transfer funds directly into your preferred bank account. If you require
                                            assistance, please don't hesitate to contact us.
                                        </small>

                                        <div class="mb-0 mt-2">
                                            @if (session('success'))
                                                <div class="alert alert-success alert-dismissible fade show"
                                                    role="alert">
                                                    {!! session('success') !!}
                                                </div>
                                            @endif

                                            @if (session('error'))
                                                <div class="alert alert-danger alert-dismissible fade show"
                                                    role="alert">
                                                    {{ session('error') }}
                                                </div>
                                            @endif


                                            @if ($errors->any())
                                                <div class="alert alert-danger alert-dismissible fade show"
                                                    role="alert">
                                                    <ul>
                                                        @foreach ($errors->all() as $error)
                                                            <li>{{ $error }}</li>
                                                        @endforeach
                                                    </ul>
                                                </div>
                                            @endif
                                            <div class="flex-space align-items-center flex-wrap">
                                                <div class="card-wrapper rounded-3 h-100 w-100 checkbox-checked">



                                                    <form class="mr-5" name="payoutForm" id="payoutForm"
                                                        method="post" action="{{ route('payout') }}">
                                                        @csrf

                                                        <div class="">

                                                            <div class="col-md-6 col-lg-6 mb-2">
                                                                <label for="input-file" class="form-label">Wallet
                                                                    Balance</label>
                                                                <select name="wbalance" id="wbalance"
                                                                    class="form-select text-center">
                                                                </select>
                                                            </div>

                                                            <div class="col-lg-6 col-md-6 mb-2">
                                                                <label for="input-file" class="form-label">How much do
                                                                    you want to
                                                                    send?</label>
                                                                <div class="input-group"
                                                                    style="flex-direction: column;">
                                                                    <div
                                                                        style="display: flex; align-items: center; width: 100%;">
                                                                        <div class="input-group-prepend">
                                                                            <div class="input-group-text">NGN</div>
                                                                        </div>
                                                                        <input class="form-control" type="number"
                                                                            id="amount" name="amount"
                                                                            placeholder="Enter amount">
                                                                    </div>
                                                                    <span id="amountNotification"
                                                                        style="color: red; display: block; margin-top: 5px; font-size: 14px; text-align: left;">
                                                                    </span>
                                                                </div>

                                                            </div>

                                                            <div class="col-md-6 col-lg-6 mb-2">
                                                                <label for="input-file" class="form-label">Bank Name
                                                                </label>
                                                                <select name="bankCode" id="bankDropdown"
                                                                    class="form-select text-center">
                                                                </select>
                                                            </div>

                                                            <div class="col-md-6 col-lg-6 mb-2">
                                                                <label for="input-file" class="form-label">Account
                                                                    Number </label>
                                                                <input type="text" class="form-control"
                                                                    name="accountNumber" maxlength="10" id="acctno"
                                                                    placeholder="">
                                                                <p id="reciever"></p>
                                                            </div>

                                                            <div class="col-md-6 col-lg-6 mb-2">
                                                                <label for="input-file" class="form-label">Remark
                                                                    (Optional) </label>
                                                                <textarea class="form-control" name="reference"></textarea>
                                                            </div>

                                                            <div class="col-xl-6 col-lg-6 col-md-6 col-sm-12">
                                                                <input type="button" id="dynamicButton" name="submit"
                                                                    class="form-control btn btn-primary btn-lg"
                                                                    value="Next">
                                                            </div>

                                                        </div>
                                                    </form>
                                                </div>

                                            </div>
                                        </div>

                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>


        <div class="modal fade" id="pinCheckModal" tabindex="-1" aria-labelledby="pinCheckModalLabel"
            data-bs-keyboard="false" aria-hidden="true">
            <!-- Scrollable modal -->
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h6 class="modal-title" id="pinCheckModalLabel">Confirm Transaction PIN</h6>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form name="pinCheckForm" id="pinCheckForm">
                            <div class="mb-3">
                                <small>No PIN? No worries! Set one up now. 👉 <u><a
                                            href="{{ route('profile.edit') }}">Settings</a></u></small><br>
                                <label for="pinInput" class="form-label mt-2">Enter your PIN</label>
                                <input type="password" id="pinInput" class="form-control" placeholder="Enter PIN"
                                    maxlength="4">
                                <div class="invalid-feedback" id="pinErrorMessage">Invalid PIN. Please try again.
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Cancel</button>
                        <button type="button" class="btn btn-primary" id="verifyPinButton">Proceed</button>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection
@section('page-js')
<script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="{{ asset('assets/js/sweetalert.js') }}"></script>
<script src="{{ asset('assets/js/select2.min.js') }}"></script>
<script>
    let bover = @json($walletBalance);
    let bover2 = @json($charges);
</script>
<script src="{{ asset('assets/js/payout.js') }}"></script>
@endsection
