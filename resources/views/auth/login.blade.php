@extends('layouts.auth')
@section('title', 'Login')
@section('content')
    <div class="container">
        <div class="row justify-content-center align-items-center authentication authentication-basic h-100">
            <div class="col-xxl-4 col-xl-5 col-lg-5 col-md-6 col-sm-8 col-12">
                <div class="my-2 d-flex justify-content-center">
                    {{-- <a href="../">
                        <img src="{{ asset('assets/images/brand-logos/logo.png')}}" alt="logo" class="desktop-logo" style="height:58px">
                        <img src="{{ asset('assets/images/brand-logos/logo-dark.jpg')}}" alt="logo" class="desktop-dark"  style="width:110px; height:58px">
                    </a> --}}
                </div>
                <div class="card custom-card">
                    <div class="card-body p-5">
                        <p class="h5 fw-semibold mb-2 text-center">Admin Portal</p>
                        {{-- <p class="mb-4 text-muted op-7 fw-normal text-center"></p> --}}
                         @if(session()->has('status'))
                            <div class="alert alert-success alert-dismissible" role="alert">
                                {{ session()->get('status') }}
                            </div>
                         @endif

                         <div class="alert alert-danger d-flex align-items-center" id="alert-danger" role="alert">
                                <svg class="flex-shrink-0 me-2 svg-danger" xmlns="http://www.w3.org/2000/svg" enable-background="new 0 0 24 24" height="1.5rem" viewBox="0 0 24 24" width="1.5rem" fill="#000000"><g><rect fill="none" height="24" width="24"/></g><g><g><g><path d="M15.73,3H8.27L3,8.27v7.46L8.27,21h7.46L21,15.73V8.27L15.73,3z M19,14.9L14.9,19H9.1L5,14.9V9.1L9.1,5h5.8L19,9.1V14.9z"/><rect height="6" width="2" x="11" y="7"/><rect height="2" width="2" x="11" y="15"/></g></g></g></svg>
                                <div id="error"></div>
                         </div>

                    <form class="theme-form needs-validation" id="login_form" novalidate>
                        @csrf
                        <div class="row gy-3">
                            <div class="col-xl-12 ">
                                <label for="signin-username" class="form-label text-default">Email ID</label>
                                <input type="email" class="form-control form-control-lg" id="email"  name="email"  placeholder="Email Address" tabindex="1"  required/>
                            </div>
                            <div class="col-xl-12 mb-2">
                                                                     <label for="signin-password" class="form-label text-default d-block">Password</label>
                                <div class="input-group">
                                    <input type="password" class="form-control form-control-lg" id="password" name="password" placeholder="Password" tabindex="2" required/>
                                    <button class="btn btn-light" type="button" onclick="createpassword('password',this)" id="button-addon2"><i class="ri-eye-off-line align-middle"></i></button>
                                </div>
                            </div>
                            <div class="col-xl-12 d-grid mt-2">
                                  <button type="button" id="btnlogin" class="btn btn-lg btn-primary btn-pry" tabindex="4"> Sign In
                                        <div class="lds-ring" id="spinner"><div></div><div></div><div></div><div></div></div>
                                  </button>
                            </div>
                        </div>
                    </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endsection
    @section('page-js')
 <!-- Config JS -->
    <script src="{{ asset('assets/js/auth.js') }}"></script>
    <script src="{{ asset('assets/js/validation.js') }}"></script>
    @endsection

