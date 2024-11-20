<!DOCTYPE html>
<html lang="en" dir="ltr" data-nav-layout="vertical" data-vertical-style="overlay" data-theme-mode="light" data-header-styles="light" data-menu-styles="light" data-toggled="close">

<head>    
    <!-- Meta Data -->
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Easy Verifications for your Business"/>
    <meta name="keywords" content="NIMC, BVN, ZEPA, Verification, Airtime,Bills, Identity">
    <meta name="author" content="Zepa Developers">
    <title>ZEPA Solutions - Kyc Status</title> 
    
     <!-- fav icon -->
    <link rel="icon" href="{{ asset('assets/home/images/favicon/favicon.png') }}" type="image/x-icon">

    <!-- Main Theme Js -->
    <script src="{{ asset('assets/js/authentication-main.js')}}"></script>

    <!-- Bootstrap Css -->
    <link id="style" href="{{ asset('assets/libs/bootstrap/css/bootstrap.min.css')}}" rel="stylesheet" >

    <!-- Style Css -->
    <link href="{{ asset('assets/css/styles.min.css') }}" rel="stylesheet" >

    <!-- Icons Css -->
    <link href="{{ asset('assets/css/icons.min.css') }}" rel="stylesheet" >

    <!-- Custom Css -->
    <link href="{{ asset('assets/css/custom.css') }}" rel="stylesheet" >
</head>
<body>  
<!-- start preLoader -->
  <div id="preloader">
	 <span class="loader"></span>
  </div>
     
    <div class="page error-bg" id="particles-js">
        <!-- Start::error-page -->
        <div class="error-page">
            <div class="container">
                <div class="text-center p-5 my-auto">
                    <div class="row align-items-center justify-content-center h-100">
                        <div class="col-xl-7">
                           <figure><img src="{{ asset('assets/kyc/img/kyc-img.png')}}" width="50%" alt="" class="img-fluid"></figure>
                            <input type="hidden" name="_token" id="_token" value="{{ csrf_token() }}">
                            <div class="row justify-content-center mb-5">
                                <div class="col-xl-6">
                                    @if ($status == 'Rejected')
                                       <p class="mb-0 op-7">We regret to inform you that after reviewing your KYC data, we have decided to reject your application. In order to proceed, please provide correct identification details. We appreciate your cooperation and look forward to re-reviewing your application.Thank you for your understanding.</p>  
                                    @else
                                         <p class="mb-0 op-7">Thank you for submitting your KYC application. We have received your application and our team will review it within the next 30 minutes. You will receive an email notification with the status of your KYC application shortly. Alternatively, you can also check back on the portal for updates.Thank you for your patience and cooperation.</p>
                                    @endif
                                   
                                </div> 

                            </div>
                            @if ($status == 'Rejected')
                                 <button id="start-again" class="btn btn-danger"><i class="las la-history align-middle me-1 d-inline-block"></i>Try Again</a>
                            @else
                                 <a href="../" class="btn btn-primary"><i class="bi bi-a-circle align-middle me-1 d-inline-block"></i>BACK TO HOME</a>
                            @endif
                           
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="{{ asset('assets/libs/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
     <!-- JQuery -->
    <script src="{{ asset('assets/kyc/js/jquery-3.7.1.min.js')}}"></script>
    <!-- Custom -->
   <script src="{{ asset('assets/js/restart.js')}}"></script>
     <!-- Config JS -->
    <script src="{{ asset('assets/js/config.js') }}"></script>

    <!-- Particles JS -->
    <script src="{{ asset('assets/libs/particles.js/particles.js')}}"></script>

    <!-- Error JS -->
    <script src="{{ asset('assets/js/error.js')}}"></script>
    	
</body>

</html>