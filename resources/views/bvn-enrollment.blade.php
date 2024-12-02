@extends('layouts.dashboard')
@section('title', 'ZEPA Solutions - BVN Enrollment')
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
                     <p class="fw-semibold fs-18 mb-0">BVN Enrollments</p>
                     <span class="fs-semibold text-muted"> <p>Modify the status of the request from this section. You can view, update, and process requests.</p></span>
                  </div>
               </div>
               <!-- End::page-header -->
               <!-- Start::row-1 -->
               <div class="row">
                  <div class="col-xxl-12 col-xl-12">
                     <div class="row">
                        <div class="col-xl-12">
                           <div class="row">
                              <div class="col-xxl-3 col-lg-3 col-md-3">
                                 <div class="card custom-card overflow-hidden">
                                    <div class="card-body">
                                       <div class="d-flex align-items-top justify-content-between">
                                          <div>
                                             <span class="avatar avatar-md avatar-rounded bg-primary-transparent">
                                            <i class="las la-tasks"></i>
                                             </span>
                                          </div>
                                          <div class="flex-fill ms-3">
                                             <div class="d-flex align-items-center justify-content-between flex-wrap">
                                                <div>
                                                   <p class="text-muted mb-0">All Request</p>
                                                   <h4 class="fw-semibold mt-1">{{ $total_request}}</h4>
                                                </div>
                                                <div id="crm-total-customers"></div>
                                             </div>
                                          </div>
                                       </div>
                                    </div>
                                 </div>
                              </div>
                              <div class="col-xxl-3 col-lg-3 col-md-3">
                                 <div class="card custom-card overflow-hidden">
                                    <div class="card-body">
                                       <div class="d-flex align-items-top justify-content-between">
                                          <div>
                                             <span class="avatar avatar-md avatar-rounded bg-success-transparent">
                                             <i class="las la-check-double"></i>
                                             </span>
                                          </div>
                                          <div class="flex-fill ms-3">
                                             <div class="d-flex align-items-center justify-content-between flex-wrap">
                                                <div>
                                                   <p class="text-muted mb-0">Resolved</p>
                                                   <h4 class="fw-semibold mt-1">{{$resolved}}</h4>
                                                </div>
                                                <div id="crm-total-deals"></div>
                                             </div>
                                          </div>
                                       </div>
                                    </div>
                                 </div>
                              </div>
                              <div class="col-xxl-3 col-lg-3 col-md-3">
                                 <div class="card custom-card overflow-hidden">
                                    <div class="card-body">
                                       <div class="d-flex align-items-top justify-content-between">
                                          <div>
                                             <span class="avatar avatar-md avatar-rounded bg-warning-transparent">
                                             <i class="las la-list-alt"></i>
                                             </span>
                                          </div>
                                          <div class="flex-fill ms-3">
                                             <div class="d-flex align-items-center justify-content-between flex-wrap">
                                                <div>
                                                   <p class="text-muted mb-0">Pending</p>
                                                   <h4 class="fw-semibold mt-1">{{ $pending}}</h4>
                                                </div>
                                                <div id="crm-total-deals"></div>
                                             </div>
                                          </div>
                                       </div>
                                    </div>
                                 </div>
                              </div>

                              <div class="col-xxl-3 col-lg-3 col-md-3">
                                 <div class="card custom-card overflow-hidden">
                                    <div class="card-body">
                                       <div class="d-flex align-items-top justify-content-between">
                                          <div>
                                             <span class="avatar avatar-md avatar-rounded bg-danger-transparent">
                                             <i class="las la-list-alt"></i>
                                             </span>
                                          </div>
                                          <div class="flex-fill ms-3">
                                             <div class="d-flex align-items-center justify-content-between flex-wrap">
                                                <div>
                                                   <p class="text-muted mb-0">Rejected</p>
                                                   <h4 class="fw-semibold mt-1">{{ $rejected}}</h4>
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

                        <div class="col-xl-12">
                            <div class="row ">
                              <div class="col-xl-12">
                           <div class="card custom-card ">

                              <div class="card-body">
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
                        <div class="col-12  mb-3">
                            <form action="{{ route('bvn-enrollment') }}" method="GET">
                                <div class="row g-2">
                                    <div class="col-md-3">
                                        <input type="text" name="search" class="form-control"
                                            value="{{ request('search') }}" placeholder="Search Here ...">
                                    </div>

                                    <div class="col-md-3">
                                        <input type="date" name="date_from" class="form-control"
                                            value="{{ request('date_from') }}" placeholder="Start Date">
                                    </div>

                                    <div class="col-md-3">
                                        <input type="date" name="date_to" class="form-control"
                                            value="{{ request('date_to') }}" placeholder="End Date">
                                    </div>

                                    <div class="col-md-3">
                                        <button type="submit" class="btn btn-primary w-100">Filter</button>
                                    </div>
                                </div>
                            </form>
                        </div>

    @if(!$crm->isEmpty())
        @php
            // Calculate serial number based on pagination
            $currentPage = $crm->currentPage(); // Current page number
            $perPage = $crm->perPage(); // Number of items per page
            $serialNumber = ($currentPage - 1) * $perPage + 1; // Starting serial number for the current page
        @endphp

        <div class="table-responsive">
            <table class="table text-nowrap" style="background:#fafafc !important">
                <thead>
                    <tr>
                        <th width="5%" class="cust2 text-light" scope="col">ID</th>
                        <th scope="col" class="cust2 text-light">Reference No.</th>
                        <th class="cust2 text-light" >Type</th>
                        <th class="cust2 text-light" >Full Name</th>
                        <th scope="col" class="cust2 text-light">Phone Number</th>
                        <th scope="col" class="cust2 text-light">Date</th>
                        <th scope="col" class="text-center cust2 text-light">Status</th>
                        <th scope="col" class="cust2 text-light">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($crm as $data)
                        @php
                            // Fetch the related transaction
                            $transaction = $data->transactions()->first();
                            // Assuming each CRM_REQUEST has one transaction
                        @endphp
                        <tr>
                            <th scope="row">{{ $serialNumber++ }}</th>
                            <td>{{ Str::upper($data->refno)  }}</td>
                              <td>{{ strtoupper($data->type)}}</td>
                            <td>{{ $data->fullname}}</td>
                            <td>{{ $data->phone_number}}</td>
                            <td>{{ \Carbon\Carbon::parse($data->created_at)->format('d/m/Y') }}</td>
                            <td class="text-center">
                                @if ($data->status == 'successful')
                                    <span class="badge bg-success-transparent">{{ Str::upper($data->status) }}</span>
                                @elseif ($data->status == 'rejected')
                                    <span class="badge bg-danger-transparent">{{ Str::upper($data->status) }}</span>
                                @elseif ($data->status == 'pending')
                                    <span class="badge bg-warning-transparent">{{ Str::upper($data->status) }}</span>
                                @else
                                      <span class="badge bg-primary-transparent">{{ Str::upper($data->status) }}</span>
                                @endif
                            </td>
                          <td>
                            @if($data->user &&  $data->status !='rejected')
                                <a href="{{ route('view-request',  [$data->id,  $request_type]) }}" class="btn btn-icon btn-sm btn-light text-center">
                                    <i class="ri-edit-line"></i>
                                </a>
                            @else
                                <span class="text-muted"></span>
                            @endif
                        </td>

                        </tr>
                    @endforeach
                </tbody>
            </table>

            <!-- Pagination Links -->
            <div class="d-flex justify-content-center">
                {{ $crm->links('vendor.pagination.bootstrap-5') }}
            </div>
        </div>
    @else
        <div class="text-center">
            <img width="65%" src="{{ asset('assets/images/no-transaction.gif') }}" alt="No Requests Available">
            <p class="fw-semibold fs-15">No Request Available!</p>
        </div>
    @endif
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
         <!-- End::row-1 -->
      </div>
      </div>

@endsection
