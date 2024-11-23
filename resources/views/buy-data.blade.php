@extends('layouts.dashboard')
@section('title', 'Buy Data')
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
                        <div class="col-xl-4">
                           <div class="row ">
                              <div class="col-xl-12">
                                 <div class="card custom-card">
                                      <div class="card-header  justify-content-between">
                                       <div class="card-title">
                                          <i class="bi bi-credit-card"></i></i> Buy Data
                                       </div>
                                    </div>
                                    <div class="card-body">

                                          <center>
                                             <img class="img-fluid" src="{{asset('assets/images/network_providers.png')}}" width="40%">
                                          </center>
                                        <p>To purchase data, select your mobile network, enter your mobile number, and choose the amount to proceed with the transaction.</p>

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

                                            <form id="buyDataForm" name="buy-data" method="POST" action="{{route('buydata')}}">
                                                @csrf
                                                <div class="mb-3 row">
                                                    <div class="col-md-12">
                                                      <select name="network" id="service_id" class="form-select text-center" aria-label="Default select example">
                                                      <option value="">Choose Network</option>
                                                       @foreach($serviceNames as $service)
                                                      <option value="{{$service->service_id}}">{{$service->service_name}}</option>
                                                       @endforeach
                                                   </select>
                                                    </div>
                                                     <div class="col-md-12 mt-3">
                                                      <select name="bundle" id="bundle" class="form-select text-center" aria-label="Default select example">
                                                      <option value="">Choose Bundle</option>
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

                           </div>
                        </div>
                        <div class="col-xl-8 d-none d-md-block">
                            <div class="card custom-card">
                                <div class="card-header justify-content-between">
                                     <div class="card-title">
                                       <i class="bi bi-list-task fw-bold"></i> Data Bundle Price List
                                       </div>
                                </div>
                                <div class="card-body">

                                 <div class="row">
                                    <div class="col-md-3 border border-light rounded text-center">
                                       <center>
                                       <ul class="nav nav-tabs mt-4 flex-column vertical-tabs-2" id="myTab" role="tablist">
                                            <li class="nav-item " role="presentation">
                                                <a class="nav-link active" data-bs-toggle="tab" role="tab" aria-current="page" href="#mtn" aria-selected="true">
                                                    <img class="img-fluid" src="{{asset('assets/images/mtn.png')}}">
                                                </a>
                                            </li>
                                            <li class="nav-item" role="presentation">
                                                <a class="nav-link" data-bs-toggle="tab" role="tab" aria-current="page" href="#airtel" aria-selected="false" tabindex="-1">
                                                        <img class="img-fluid" src="{{asset('assets/images/airtel.png')}}" width="84px" height="77px">
                                                </a>
                                            </li>
                                            <li class="nav-item" role="presentation">
                                                <a class="nav-link" data-bs-toggle="tab" role="tab" aria-current="page" href="#glo" aria-selected="false" tabindex="-1">
                                                       <img class="img-fluid" src="{{asset('assets/images/glo.png')}}">
                                                 </a>
                                            </li>
                                            <li class="nav-item " role="presentation">
                                                <a class="nav-link mb-2" data-bs-toggle="tab" role="tab" aria-current="page" href="#9mobile" aria-selected="false" tabindex="">
                                                   <img class="img-fluid" src="{{asset('assets/images/9mobile.png')}}">
                                                </a>
                                            </li>
                                             <li class="nav-item " role="presentation">
                                                <a class="nav-link mb-2" data-bs-toggle="tab" role="tab" aria-current="page" href="#smile" aria-selected="false" tabindex="">
                                                   <img class="img-fluid" src="{{asset('assets/images/smile.jpg')}}" width="">
                                                </a>
                                            </li>
                                            <li class="nav-item " role="presentation">
                                                <a class="nav-link mb-2" data-bs-toggle="tab" role="tab" aria-current="page" href="#spectranet" aria-selected="false" tabindex="">
                                                   <img class="img-fluid" src="{{asset('assets/images/images.jpeg')}}" width="">
                                                </a>
                                            </li>
                                        </ul>
                                       </center>
                                    </div>
                                    <div class="col-md-9 ">
                                        <div class="tab-content">
                                            <div class="tab-pane text-muted active show" id="mtn" role="tabpanel">
                                                <div class="col-md-12  row">
                                                   <p class="fw-bold">MTN DATA PLANS</p>
                                                <p>We offer the most competitive rates for data purchases across all major networks. Check out our unbeatable prices:</p>
                                                         @if(!$priceList1->isEmpty())
                                             <div class="table-responsive">
                                                <table class="table text-nowrap" style="background:#fafafc !important">
                                                   <thead>
                                                      <tr class="table-primary">
                                                         {{-- <th width="5%" scope="col">ID</th> --}}
                                                         <th scope="col">DATA PLANS</th>
                                                        {{-- <th scope="col"></th> --}}
                                                         <th scope="col" class="text-center">PRICE</th>
                                                      </tr>
                                                   </thead>
                                                   <tbody>

                                                      @foreach($priceList1 as $data)
                                                      <tr>
                                                         {{-- <th scope="row">{{ $i }}</th> --}}
                                                         <td>{{$data->name}}</td>

                                                         <td class="text-center">&#8358;{{number_format($data->variation_amount,2)}}</td>
                                                      </tr>

                                                      @endforeach
                                                   </tbody>
                                                </table>
                                                <!-- Pagination Links -->
                                             <div class="d-flex justify-content-center">
                                                   {{ $priceList1->appends(['table2_page' => $priceList2->currentPage(), 'table3_page' => $priceList3->currentPage(), 'table4_page' => $priceList4->currentPage(), 'table5_page' => $priceList5->currentPage(), 'table6_page' => $priceList6->currentPage()])->links('vendor.pagination.bootstrap-4') }}
                                             </div>
                                             </div>
                                             @else
                                             <center><img width="65%" src="{{ asset('assets/images/no-transaction.gif')}}" alt=""></center>
                                             <p class="text-center fw-semibold  fs-15">  No Price List added yet!</p>
                                             @endif
                                             <p>Don't miss out on these amazing deals! Buy your data now and stay connected without breaking the bank!"</p>


                                                </div>
                                            </div>
                                            <div class="tab-pane text-muted" id="airtel" role="tabpanel">
                                                 <div class="col-md-12  row">
                                                   <p class="fw-bold">AIRTEL DATA PLANS</p>
                                                <p>We offer the most competitive rates for data purchases across all major networks. Check out our unbeatable prices:</p>
                                                         @if(!$priceList2->isEmpty())
                                             <div class="table-responsive">
                                                <table class="table text-nowrap" style="background:#fafafc !important">
                                                   <thead>
                                                      <tr class="table-primary">
                                                         {{-- <th width="5%" scope="col">ID</th> --}}
                                                         <th scope="col">DATA PLANS</th>
                                                          {{-- <th scope="col"></th> --}}
                                                         <th scope="col" class="text-center">PRICE</th>
                                                      </tr>
                                                   </thead>
                                                   <tbody>
                                                      @php $i = 1; @endphp
                                                      @foreach($priceList2 as $data)
                                                      <tr>
                                                         {{-- <th scope="row">{{ $i }}</th> --}}
                                                         <td>{{$data->name}}</td>

                                                         <td class="text-center">&#8358;{{number_format($data->variation_amount,2)}}</td>
                                                      </tr>
                                                      @php $i++ @endphp
                                                      @endforeach
                                                   </tbody>
                                                </table>
                                                <!-- Pagination Links -->
                                             <div class="d-flex justify-content-center">
                                                    {{ $priceList2->appends(['table1_page' => $priceList1->currentPage(), 'table3_page' => $priceList3->currentPage(), 'table4_page' => $priceList4->currentPage(), 'table5_page' => $priceList5->currentPage(), 'table6_page' => $priceList6->currentPage()])->links('vendor.pagination.bootstrap-4') }}
                                             </div>
                                             </div>
                                             @else
                                             <center><img width="65%" src="{{ asset('assets/images/no-transaction.gif')}}" alt=""></center>
                                             <p class="text-center fw-semibold  fs-15">  No Price List added yet!</p>
                                             @endif
                                             <p>Don't miss out on these amazing deals! Buy your data now and stay connected without breaking the bank!"</p>


                                                </div>
                                            </div>
                                            <div class="tab-pane text-muted" id="glo" role="tabpanel">
                                                 <div class="col-md-12  row">
                                                   <p class="fw-bold">GLO DATA PLANS</p>
                                                <p>We offer the most competitive rates for data purchases across all major networks. Check out our unbeatable prices:</p>
                                                         @if(!$priceList3->isEmpty())
                                             <div class="table-responsive">
                                                <table class="table text-nowrap" style="background:#fafafc !important">
                                                   <thead>
                                                      <tr class="table-primary">
                                                         {{-- <th width="5%" scope="col">ID</th> --}}
                                                         <th scope="col">DATA PLANS</th>
                                                          {{-- <th scope="col"></th> --}}
                                                         <th scope="col" class="text-center">PRICE</th>
                                                      </tr>
                                                   </thead>
                                                   <tbody>
                                                      @php $i = 1; @endphp
                                                      @foreach($priceList3 as $data)
                                                      <tr>
                                                         {{-- <th scope="row">{{ $i }}</th> --}}
                                                         <td>{{$data->name}}</td>

                                                         <td class="text-center">&#8358;{{number_format($data->variation_amount,2)}}</td>
                                                      </tr>
                                                      @php $i++ @endphp
                                                      @endforeach
                                                   </tbody>
                                                </table>
                                                <!-- Pagination Links -->
                                             <div class="d-flex justify-content-center">
                                                   {{ $priceList3->appends(['table1_page' => $priceList1->currentPage(), 'table2_page' => $priceList2->currentPage(), 'table4_page' => $priceList4->currentPage(), 'table5_page' => $priceList5->currentPage(), 'table6_page' => $priceList6->currentPage()])->links('vendor.pagination.bootstrap-4') }}
                                             </div>
                                             </div>
                                             @else
                                             <center><img width="65%" src="{{ asset('assets/images/no-transaction.gif')}}" alt=""></center>
                                             <p class="text-center fw-semibold  fs-15">  No Price List added yet!</p>
                                             @endif
                                             <p>Don't miss out on these amazing deals! Buy your data now and stay connected without breaking the bank!"</p>


                                                </div>
                                            </div>
                                            <div class="tab-pane text-muted" id="9mobile" role="tabpanel">
                                                 <div class="col-md-12  row">
                                                   <p class="fw-bold">9MOBILE DATA PLANS</p>
                                                <p>We offer the most competitive rates for data purchases across all major networks. Check out our unbeatable prices:</p>
                                                         @if(!$priceList4->isEmpty())
                                             <div class="table-responsive">
                                                <table class="table text-nowrap" style="background:#fafafc !important">
                                                   <thead>
                                                      <tr class="table-primary">
                                                         {{-- <th width="5%" scope="col">ID</th> --}}
                                                         <th scope="col">DATA PLANS</th>
                                                          {{-- <th scope="col"></th> --}}
                                                         <th scope="col" class="text-center">PRICE</th>
                                                      </tr>
                                                   </thead>
                                                   <tbody>
                                                      @php $i = 1; @endphp
                                                      @foreach($priceList4 as $data)
                                                      <tr>
                                                         {{-- <th scope="row">{{ $i }}</th> --}}
                                                         <td>{{$data->name}}</td>

                                                         <td class="text-center">&#8358;{{number_format($data->variation_amount,2)}}</td>
                                                      </tr>
                                                      @php $i++ @endphp
                                                      @endforeach
                                                   </tbody>
                                                </table>
                                                <!-- Pagination Links -->
                                             <div class="d-flex justify-content-center">
                                                   {{ $priceList4->appends(['table1_page' => $priceList1->currentPage(), 'table2_page' => $priceList2->currentPage(), 'table3_page' => $priceList3->currentPage(), 'table5_page' => $priceList5->currentPage(), 'table6_page' => $priceList6->currentPage()])->links('vendor.pagination.bootstrap-4') }}
                                             </div>
                                             </div>
                                             @else
                                             <center><img width="65%" src="{{ asset('assets/images/no-transaction.gif')}}" alt=""></center>
                                             <p class="text-center fw-semibold  fs-15">  No Price List added yet!</p>
                                             @endif
                                             <p>Don't miss out on these amazing deals! Buy your data now and stay connected without breaking the bank!"</p>


                                                </div>
                                            </div>
                                               <div class="tab-pane text-muted" id="smile" role="tabpanel">
                                               <div class="col-md-12  row">
                                                   <p class="fw-bold">SIMLE DATA PLANS</p>
                                                <p>We offer the most competitive rates for data purchases across all major networks. Check out our unbeatable prices:</p>
                                                         @if(!$priceList5->isEmpty())
                                             <div class="table-responsive">
                                                <table class="table text-nowrap" style="background:#fafafc !important">
                                                   <thead>
                                                      <tr class="table-primary">
                                                         {{-- <th width="5%" scope="col">ID</th> --}}
                                                         <th scope="col">DATA PLANS</th>
                                                          {{-- <th scope="col"></th> --}}
                                                         <th scope="col" class="text-center">PRICE</th>
                                                      </tr>
                                                   </thead>
                                                   <tbody>
                                                      @php $i = 1; @endphp
                                                      @foreach($priceList5 as $data)
                                                      <tr>
                                                         {{-- <th scope="row">{{ $i }}</th> --}}
                                                         <td>{{$data->name}}</td>

                                                         <td class="text-center">&#8358;{{number_format($data->variation_amount,2)}}</td>
                                                      </tr>
                                                      @php $i++ @endphp
                                                      @endforeach
                                                   </tbody>
                                                </table>
                                                <!-- Pagination Links -->
                                             <div class="d-flex justify-content-center">
                                                   {{ $priceList5->appends(['table1_page' => $priceList1->currentPage(), 'table2_page' => $priceList2->currentPage(), 'table3_page' => $priceList3->currentPage(), 'table4_page' => $priceList4->currentPage(), 'table6_page' => $priceList6->currentPage()])->links('vendor.pagination.bootstrap-4') }}
                                             </div>
                                             </div>
                                             @else
                                             <center><img width="65%" src="{{ asset('assets/images/no-transaction.gif')}}" alt=""></center>
                                             <p class="text-center fw-semibold  fs-15">  No Price List added yet!</p>
                                             @endif
                                             <p>Don't miss out on these amazing deals! Buy your data now and stay connected without breaking the bank!"</p>


                                                </div>
                                            </div>
                                               <div class="tab-pane text-muted" id="spectranet" role="tabpanel">
                                                 <div class="col-md-12  row">
                                                   <p class="fw-bold">SPECTRANET DATA PLANS</p>
                                                <p>We offer the most competitive rates for data purchases across all major networks. Check out our unbeatable prices:</p>
                                                         @if(!$priceList6->isEmpty())
                                             <div class="table-responsive">
                                                <table class="table text-nowrap" style="background:#fafafc !important">
                                                   <thead>
                                                      <tr class="table-primary">
                                                         {{-- <th width="5%" scope="col">ID</th> --}}
                                                         <th scope="col">DATA PLANS</th>
                                                         {{-- <th scope="col"></th> --}}
                                                         <th scope="col" class="text-center">PRICE</th>
                                                      </tr>
                                                   </thead>
                                                   <tbody>
                                                      @php $i = 1; @endphp
                                                      @foreach($priceList6 as $data)
                                                      <tr>
                                                         {{-- <th scope="row">{{ $i }}</th> --}}
                                                         <td>{{$data->name}}</td>

                                                         <td class="text-center">&#8358;{{number_format($data->variation_amount,2)}}</td>
                                                      </tr>
                                                      @php $i++ @endphp
                                                      @endforeach
                                                   </tbody>
                                                </table>
                                                <!-- Pagination Links -->
                                             <div class="d-flex justify-content-center">
                                                   {{ $priceList6->appends(['table1_page' => $priceList1->currentPage(), 'table2_page' => $priceList6->currentPage(), 'table3_page' => $priceList3->currentPage(), 'table4_page' => $priceList4->currentPage(), 'table5_page' => $priceList5->currentPage()])->links('vendor.pagination.bootstrap-4') }}
                                             </div>
                                             </div>
                                             @else
                                             <center><img width="65%" src="{{ asset('assets/images/no-transaction.gif')}}" alt=""></center>
                                             <p class="text-center fw-semibold  fs-15">  No Price List added yet!</p>
                                             @endif
                                             <p>Don't miss out on these amazing deals! Buy your data now and stay connected without breaking the bank!"</p>


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
  <!-- Custom JS -->
      <script src="{{ asset('assets/js/data.js') }}"></script>
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
</script>

      <script>
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
