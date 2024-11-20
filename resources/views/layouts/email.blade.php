<!DOCTYPE html>
<html lang="en">

<head>
    <!-- Meta Data -->
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Easy Verifications for your Business" />
    <meta name="keywords" content="NIMC, BVN, ZEPA, Verification, Airtime, Bills, Identity">
    <meta name="author" content="Zepa Developers">
    <title>ZEPA Solutions - @yield('title')</title>

    <!-- Fav Icon -->
    <link rel="icon" href="{{ asset('assets/home/images/favicon/favicon.png') }}" type="image/x-icon">

    <!-- Styles -->
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f8f9fa;
            margin: 0;
            padding: 0;
            color: #333;
        }
        .email-container {
            max-width: 600px;
            margin: 20px auto;
            padding: 20px;
            background-color: #ffffff;
            border: 1px solid #ddd;
            border-radius: 8px;
        }
        .email-header {
            text-align: center;
            margin-bottom: 20px;
        }
        .email-body {
            font-size: 16px;
            line-height: 1.6;
            margin-bottom: 20px;
        }
        .email-footer {
            text-align: left;
            font-size: 14px;
            margin-top: 30px;
            border-top: 1px solid #ddd;
            padding-top: 20px;
        }
        .email-logo img {
            width: 100px;
            margin-bottom: 20px;
        }
        .btn-primary {
            background-color: #007bff;
            color: white;
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 5px;
        }
        .btn-primary:hover {
            background-color: #0056b3;
        }
        .email-footer p {
            margin: 0;
        }
    </style>
</head>

<body>
    @yield('content')
</body>
</html>
