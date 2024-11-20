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
    <title>ZEPA Solutions - Payment Notification</title> 
    
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
    <div class="page error-bg" id="particles-js">
        <!-- Start::error-page -->
        <div class="error-page">
            <div class="container">
                <div class="text-center p-5 my-auto">
                    <div class="row align-items-center justify-content-center h-100">
                        <div class="col-xl-7">
                           <figure><img src="{{ asset('assets/kyc/img/kyc-img.png')}}" width="50%" alt="" class="img-fluid"></figure>
                            <div class="row justify-content-center mb-5">
                                <div class="col-xl-6">
                                    @if ($mail_data['type'] == 'Topup')
                                       <p class="mb-0 op-7">Wallet funding of N{{$mail_data['amount']}}
                                         via {{$mail_data['bankName']}} transfer. You wallet have been credited. Your transaction reference is {{$mail_data['ref']}}</p>  
                                     <br/>
                                    @endif 
                                         <br/> 
                                         <p style="box-sizing: border-box; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif, 'Apple Color Emoji', 'Segoe UI Emoji', 'Segoe UI Symbol'; position: relative; font-size: 16px; line-height: 1.5em; margin-top: 0; text-align: left;">Warm regards,<br>ZEPA Solutions </p>
                                  </div> 

                            </div>                           
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
   <script src="{{ asset('assets/js/custom2.js')}}"></script>
     <!-- Config JS -->
    <script src="{{ asset('assets/js/config.js') }}"></script>

    <!-- Particles JS -->
    <script src="{{ asset('assets/libs/particles.js/particles.js')}}"></script>

    <!-- Error JS -->
    <script src="{{ asset('assets/js/error.js')}}"></script>
    	
</body>

</html>