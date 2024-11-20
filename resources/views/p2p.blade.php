@extends('layouts.dashboard')
@section('title', 'Wallet Transfer (P2P)')
@section('content')

   <!------App Header ----->
          @include('components.app-header')
         <!-- Start::app-sidebar -->

          @include('components.app-sidebar')

           <div class="main-content app-content">
            <div class="container-fluid">

               <!-- End::page-header -->
               <!-- Start::row-1 -->
               <div class="row mt-2">
                  <div class="col-xxl-12 col-xl-12">
                     <div class="row">
                        <div class="col-xl-12">
                           <div class="row">
                                 <div class="card custom-card">
                                      <div class="card-header  justify-content-between">
                                       <div class="card-title">
                                            <i class="ti ti-wallet fs-16 side-menu__icon"></i> Wallet to Wallet Transfer (P2P)
                                       </div>
                                    </div>
                                    <div class="card-body  mt-3">
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
                                         <div class="row text-center">
                                           <div class="col-xl-7">
                                             <div class="alert alert-light shadow-sm">
                                          <center><svg class="d-block" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="36" height="36" fill="currentColor"><path d="M12 22C6.47715 22 2 17.5228 2 12C2 6.47715 6.47715 2 12 2C17.5228 2 22 6.47715 22 12C22 17.5228 17.5228 22 12 22ZM11 11V17H13V11H11ZM11 7V9H13V7H11Z"></path></svg>
                                          <p> To initiate a wallet-to-wallet transfer, please enter the recipient's wallet ID. You can find your own wallet ID below for reference:</p>
                                         <p> Your Wallet ID:</p>
                                         <h2>{{ Auth::user()->phone_number }}</h2>
                                          Enter the recipient's wallet ID carefully to ensure a successful transfer.
                                          </center>

                                          <p class="mt-5 text-danger"> If you encounter any issues with the transfer, please don't hesitate to reach out to our Customer Care team for assistance. We're here to help</p>
                                        <a href="{{route('support')}}"><i class="las la-headset text-primary" style="font-size:64px"></i>
                                        <p>Customer Support</p></a>
                                       </div>

                                       </div>

                                         <div class="col-md-5">
                                           <div class="card-body rounded" style="background:#fff; border: 1px solid #9b9b9b;">

                                      <img class="img-fluid" src="{{ asset('assets/images/transfer-img.jpg')}}" alt="">

                                            <form id="transfer" name="transfer" action="{{route('transfer-funds')}}" method="POST">
                                                @csrf
                                                <div class="mb-3 row">
                                                    <div class="col-md-12">
                                                        <p class="mb-2 mt-3 form-label">Wallet ID</p>
                                                        <input type="text" id="Wallet_ID" name="Wallet_ID" value="" class="form-control text-center" maxlength="11" required/>
                                                       <p id="reciever"></p>
                                                      </div>

                                                      <div class="col-md-12 mb-3 ">
                                                        <p class="mb-2 form-label">Amount to transfer</p>
                                                        <input type="text" id="Amount" name="Amount" value="" class="form-control text-center" maxlength="5" required/>
                                                    </div>
                                                </div>
                                                <button type="submit" id="transfer" class="btn btn-primary"><i class="las la-exchange-alt"></i> Transfer</button>
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
         </div>


@endsection
@section('page-js')
 <script src="{{ asset('assets/js/wallet.js') }}"></script>
@endsection


