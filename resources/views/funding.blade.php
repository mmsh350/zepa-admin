@extends('layouts.dashboard')
@section('title', 'Fund Wallet')
@section('content')

   <!------App Header ----->
          @include('components.app-header')
         <!-- Start::app-sidebar -->

          @include('components.app-sidebar')
  <div class="main-content app-content">
            <div class="container-fluid">
               <!-- Start::page-header -->
               <div class="d-md-flex d-block align-items-center justify-content-between my-2 page-header-breadcrumb">
                  <div>
                     <p class="fw-semibold fs-18 mb-0">Wallet Funding</p>
                     <span class="fs-semibold text-muted">Select your preferred funding method to deposit funds into your wallet. If you need assistance, please don't hesitate to contact us.</span>
                  </div>
               </div>
               <!-- End::page-header -->
               <!-- Start::row-1 -->
               <div class="row">
                  <div class="col-xxl-12 col-xl-12">
                     <div class="row">
                        <div class="col-xl-12">
                           <div class="row">
                              <div class="col-xxl-4 col-lg-4 col-md-4">
                                 <div class="card custom-card overflow-hidden">
                                    <div class="card-body">
                                       <div class="d-flex align-items-top justify-content-between">
                                          <div>
                                             <span class="avatar avatar-md avatar-rounded bg-primary-transparent">
                                             <i class="ti ti-wallet fs-16"></i>
                                             </span>
                                          </div>
                                          <div class="flex-fill ms-3">
                                             <div class="d-flex align-items-center justify-content-between flex-wrap">
                                                <div>
                                                   <p class="text-muted mb-0">Wallet Balance</p>
                                                   <h4 class="fw-semibold mt-1">&#x20A6;{{number_format($walletBalance),2}}</h4>
                                                </div>
                                                {{-- <div id="crm-total-customers"><a href="{{route('p2p')}}">P2P</a></div> --}}
                                             </div>
                                          </div>
                                       </div>
                                    </div>
                                 </div>
                              </div>
                              <div class="col-xxl-4 col-lg-4 col-md-4">
                                 <div class="card custom-card overflow-hidden">
                                    <div class="card-body">
                                       <div class="d-flex align-items-top justify-content-between">
                                          <div>
                                             <span class="avatar avatar-md avatar-rounded bg-info-transparent">
                                             <i class="ti ti-briefcase fs-16"></i>
                                             </span>
                                          </div>
                                          <div class="flex-fill ms-3">
                                             <div class="d-flex align-items-center justify-content-between flex-wrap">
                                                <div>
                                                   <p class="text-muted mb-0">Deposited</p>
                                                   <h4 class="fw-semibold mt-1">&#x20A6;{{number_format($deposit),2}}</h4>
                                                </div>
                                                <div id="crm-total-deals"></div>
                                             </div>
                                          </div>
                                       </div>
                                    </div>
                                 </div>
                              </div>
                              <div class="col-xxl-4 col-lg-4 col-md-4">
                                 <div class="card custom-card overflow-hidden">
                                    <div class="card-body">
                                       <div class="d-flex align-items-top justify-content-between">
                                          <div>
                                             <span class="avatar avatar-md avatar-rounded bg-danger-transparent">
                                             <i class="ri-exchange-funds-line fs-16"></i>
                                             </span>
                                          </div>
                                          <div class="flex-fill ms-3">
                                             <div class="d-flex align-items-center justify-content-between flex-wrap">
                                                <div>
                                                   <p class="text-muted mb-0">Spent</p>
                                                   <h4 class="fw-semibold mt-1">&#x20A6;{{number_format($deposit - $walletBalance),2}}</h4>
                                                </div>
                                                <div id="crm-total-deals"></div>
                                             </div>
                                          </div>
                                       </div>
                                    </div>
                                 </div>
                              </div>
                           </div>
                        </div>
                        <div class="col-xl-4">
                           <div class="row">
                              <div class="col-xl-12">
                                 <div class="card custom-card">
                                    <div class="card-header  justify-content-between">
                                       <div class="card-title">
                                          Virtual Account Numbers
                                       </div>
                                    </div>
                                    <div class="card-body">
                                       <small class="fw-semibold">Fund your wallet instantly by depositing into the virtual account number</small>
                                       <ul class="list-unstyled crm-top-deals mb-0 mt-3">
                                          @if($virtualAccounts != null)
                                          @foreach($virtualAccounts as $data)
                                          <li>
                                             <div class="d-flex align-items-top flex-wrap">
                                                <div class="me-2">
                                                   <span class="avatar avatar-sm avatar-rounded">
                                                   @if ( $data->bankName == 'Wema bank')
                                                   <img src="{{ asset('assets/images/wema.jpg')}}" alt="">
                                                   @elseif($data->bankName == 'Moniepoint Microfinance Bank')
                                                   <img src="{{ asset('assets/images/moniepoint.jpg')}}" alt="">
                                                   @else
                                                   <img src="{{ asset('assets/images/sterling.png')}}" alt="">
                                                   @endif
                                                   </span>
                                                </div>
                                                <div class="flex-fill">
                                                   <p class="fw-semibold mb-0">{{ $data->accountName}}</p>
                                                   <span class="fs-14 acctno">{{$data->accountNo}}</span> <br>
                                                   <span class=" fs-12">{{$data->bankName}} </span>
                                                </div>
                                                <div class="fw-semibold fs-15"><a href="#" class="btn btn-light btn-sm copy-account-number">Copy</a></div>
                                             </div>
                                          </li>
                                          @endforeach
                                          @endif
                                       </ul>
                                       <hr>
                                        <small class="fw-semibol mb-2 text-danger">If your funds is not received within 30mins Please
                                          <a href="{{route('support')}}">Contact Support
                                           <i class="bx bx-headphone side-menu__icon"></i>
                                           </a>
                                        </small>
                                        <div class="alert alert-danger alert-dismissible text-center" id="errorMsg" style="display:none;" role="alert">
                                             <small id="message">Processing your request.</small>
                                        </div>
                                        <div class="alert alert-success alert-dismissible text-center" id="successMsg" style="display:none;" role="alert">
                                             <small id="smessage">Processing your request.</small>
                                        </div>


                                    </div>
                                 </div>
                              </div>

                           </div>
                        </div>
                        <div class="col-xl-8">
                            <div class="row ">
                              <div class="col-xl-12">
                           <div class="card custom-card ">
                              <div class="card-header justify-content-between">
                                 <div class="card-title">
                                 More Payment Options
                                 </div>
                              </div>
                              <div class="card-body">


                        <div class="mb-0">
                         <div id="error" style="display:none" class="alert alert-danger alert-dismissible" role="alert"></div>
                          <div class="flex-space flex-wrap align-items-center">
                          <div class="card-wrapper  rounded-3 h-100 w-100 checkbox-checked">
                          <h6 class="sub-title" >Online Payment</h6>
                         <span style="text-transform:none">Choose a payment method, enter the funding amount and continue to top up</span>

                        <div class="row mt-3">

                             <div class="col-md-6">
                              <div class="form-check radio radio-primary">
                                <input class="form-check-input" id="ptm44" type="radio" name="radio1" value="moniepoint">
                                <label class="form-check-label mb-0" for="ptm44"><img  width="50%" class="img-fluid" src="{{ asset('assets/images/monify.png') }}" alt="card"></label>
                              </div>
                            </div>

                            <div class="col-md-6">
                              <div class="form-check radio radio-primary">
                                <input class="form-check-input" id="ptm11"   type="radio" name="radio1" value="paystack">
                                <label class="form-check-label mb-0" for="ptm11"><img class="img-fluid"  width="50%" src="{{ asset('assets/images//paystack.png') }}" alt="card"><br/> Comming soon!</label>
                              </div>
                            </div>

                        </div>

                      <form class="row" name="paymentForm" id="paymentForm">
                      @csrf
                      @method('post')
                      <div class="col-4"  hidden>
                        <input class="form-control"   id="first-name" name="first-name" type="text" value="{{ Auth::user()->first_name; }}" aria-label="First name" required="">
                      </div>
                      <div class="col-4"  hidden>
                        <input class="form-control"  id="last-name" name="last-name" type="text" value="{{ Auth::user()->last_name; }}" aria-label="Last name" required="">
                      </div>
                      <div class="col-4"  hidden>
                        <input class="form-control"   id="email" name="email" type="email" value="{{ Auth::user()->email; }}" required="">
                      </div>
                      <div class="col-4"  hidden>
                        <input class="form-control"   id="phone_number" name="phone_number" type="text" value="{{ Auth::user()-> phone_number; }}" required="">
                      </div>

                      <div class="col-4"  hidden>
                        <input class="form-control" id="desc" type="desc" value="Wallet Top Up" required="">
                      </div>
                      <input type="text" hidden id="response" />
                      <input type="text" hidden id="reference" />
                      <div class="col-6 ">
                        <label class="col-sm-6 col-form-label"  >Top up Amount</label>
                        <input class="form-control border border-dark" onkeypress="return isNumberKey(event)" type="text" id="amount"  name="amount"    value="">
                      </div>
                      <div class="col-8  ">
                      <button class="example-popover btn btn-dark mb-1   mt-3 " id="topup" type="button"><i class="icofont icofont-pay">&nbsp;</i>Top Up</button>
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
            </div>
         </div>
 @endsection
  @section('page-js')
    <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
      <script type="text/javascript" src="https://sdk.monnify.com/plugin/monnify.js"></script>

  <script src="{{ asset('assets/js/sweetalert.js') }}"></script>
     <script src="{{ asset('assets/js/custom-gates.js')}}"></script>
       <script>
        window.APP_ENV = {
        MONNIFYCONTRACT: "{{ env('MONNIFYCONTRACT') }}",
        MONNIFYAPI: "{{ env('MONNIFYAPI') }}",
    };
    </script>
  @endsection



