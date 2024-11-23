@extends('layouts.dashboard')
@section('title', 'Buy SME Data')
@section('page-css')
  <style>
         .vertical-tabs-2 .nav-item .nav-link.active {
         background-color: #1a4082  !important;
         color: #fff;
         position: relative;
        }
        .vertical-tabs-2 .nav-item .nav-link.active::before {
            content: "";
            position: absolute;
            inset-inline-end: -.5rem;
            inset-block-start: 38%;
            transform: rotate(45deg);
            width: 1rem;
            height: 1rem;
            background-color: #3b5998 !important;
        }
        .vertical-tabs-2 .nav-item .nav-link {
	min-width: 7.5rem;
	max-width: 7.5rem;
	text-align: center;
	border: 1px solid var(--default-border);
	margin-bottom: .5rem;
	color: #fff;
	background-color: #fff;
}
 @media (max-width: 576px) {
    .custom-margin-top {
        margin-top: -100px !important; /* Adjust the value as needed */

    }
}

      </style>
@endsection
@section('content')

    <!------App Header ----->
    @include('components.app-header')
    <!-- Start::app-sidebar -->

    @include('components.app-sidebar')

<div class="main-content app-content custom-margin-top">
            <div class="container-fluid">

               <!-- End::page-header -->
               <!-- Start::row-1 -->
               <div class="row">
                  <div class="col-xxl-12 col-xl-12">
                     <div class="row mt-3">
                        <div class="col-xl-12">
                           <div class="row">
                             <div class="col-xl-2">
                             </div>
                              <div class="col-xl-7">
                                 <div class="card custom-card">
                                      <div class="card-header  justify-content-between">
                                       <div class="card-title">
                                          <i class="bi bi-credit-card"></i> Buy Cheap Data
                                       </div>
                                    </div>
                                    <div class="card-body">

                                          <center>
                                             <img class="img-fluid" src="{{asset('assets/images/network_providers.png')}}" width="40%">

                                        <p>To purchase data, select your mobile network, enter your mobile number, and choose the amount to proceed with the transaction.</p>
                                        </center>
                                         <div class="row text-center">
                                         <div class="col-md-12">
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

                                            <form id="buyDataForm" name="buy-data" method="POST" action="{{route('buy-sme-data')}}">
                                                @csrf
                                                <div class="mb-3 row">
                                                    <div class="col-md-12">
                                                        <select name="network" id="service_id" class="form-select text-center" aria-label="Default select example">
                                                            <option value="">Choose Network</option>
                                                            <option value="1">MTN</option>
                                                            <option value="2">GLO</option>
                                                            <option value="3">9MOBILE</option>
                                                            <option value="4">AIRTEL</option>
                                                            <option value="5">SMILE</option>
                                                        </select>
                                                    </div>
                                                     <div class="col-md-12 mt-3">
                                                      <select name="type" id="type" class="form-select text-center" aria-label="Default select example">
                                                      <option value=""></option>
                                                      </select>
                                                    </div>
                                                     <div class="col-md-12 mt-3">
                                                      <select name="plan" id="plan" class="form-select text-center" aria-label="Default select example">
                                                      <option value=""></option>
                                                      </select>
                                                    </div>

                                                    <div class="col-md-12 mt-2">
                                                        <p class="mb-2 text-muted">Amount To Pay</p>
                                                        <input type="text" id="amountToPay" readonly value="" class="form-control text-center"/>
                                                    </div>
                                                     <div class="col-md-12 mt-2">
                                                        <p class="mb-0 text-muted">Phone Number</p>
                                                        <input type="text" id="mobileno" name="mobileno" oninput="validateNumber()" value="" class="form-control phone text-center" maxlength="11" required/>
                                                        <p id="networkResult"></p>
                                                      </div>

                                                       <div class="col-md-12 mt-0">
                                                  <button type="submit" id="buy-data" class="btn btn-primary"><i class="las la-shopping-cart"></i> Buy Data</button>
                                                </div>
                                                </div>
                                            </form>
                                         </div>
                                         </div>
                                    </div>
                                 </div>
                              </div>
                               <div class="col-xl-2">
                             </div>

                           </div>
                        </div>


                     </div>
                  </div>
               </div>
            </div>
         </div>

@endsection

@section('page-js')
  <script src="{{ asset('assets/js/sme-data.js') }}"></script>
      <script>
   document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('buyDataForm');
    const submitButton = document.getElementById('buy-data');

    if (form && submitButton) {
        form.addEventListener('submit', function() {
            submitButton.disabled = true;
            submitButton.innerText = 'Processing your request, please wait...';
        });
    }
});

        $(document).ready(function() {
            // Load the active tab from localStorage
            var activeTab = localStorage.getItem('activeTab');
            if (activeTab) {
                $('#myTab a[href="' + activeTab + '"]').tab('show');
            } else {
                // Set default tab if no active tab is stored
                $('#myTab a[href="#mtn"]').tab('show');
            }
            // Store the active tab in localStorage when clicked
            $('a[data-bs-toggle="tab"]').on('shown.bs.tab', function(e) {
                localStorage.setItem('activeTab', $(e.target).attr('href'));
            });
        });
    </script>

@endsection


