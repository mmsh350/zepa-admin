<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
      <!-- Meta Data -->
      <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
      <meta http-equiv="X-UA-Compatible" content="IE=edge">
      <meta name="viewport" content="width=device-width, initial-scale=1.0">
      <meta name="description" content="Easy Verifications for your Business"/>
      <meta name="keywords" content="NIMC, BVN, ZEPA, Verification, Airtime,Bills, Identity">
      <meta name="author" content="Zepa Developers">
      <title>ZEPA Solutions - @yield('title') </title>
       <!-- fav icon -->
      <link rel="icon" href="{{ asset('assets/home/images/favicon/favicon.png') }}" type="image/x-icon">
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.6.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        body {
            background-color: #f8f9fa;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .receipt-container {
            max-width: 600px;
            margin: 40px auto;
            padding: 25px;
            border: 1px solid #dee2e6;
            border-radius: 10px;
            background-color: #ffffff;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
        }
        .receipt-header {
            text-align: center;
            margin-bottom: 20px;
            color: #28a745;
        }
        .receipt-header i {
            font-size: 40px;
            margin-bottom: 10px;
        }
        .receipt-header h2 {
            font-weight: bold;
            font-size: 22px;
            color: #333;
        }
        .receipt-table th, .receipt-table td {
            padding: 10px;
            text-align: left;
            vertical-align: middle;
            font-size: 14px;
        }
        .receipt-table th {
            background-color: #f8f9fa;
            font-weight: bold;
            color: #444;
            width: 30%;
        }
        .receipt-table td {
            background-color: #ffffff;
            font-weight: normal;
            color: #333;
            border-top: 1px solid #dee2e6;
        }
        .total-amount {
            text-align: right;
            font-size: 18px;
            font-weight: bold;
            padding: 10px;
            background-color: #e9f7e9;
            color: #28a745;
            border-top: 1px solid #dee2e6;
        }
        .receipt-footer {
            margin-top: 20px;
            text-align: center;
            font-size: 14px;
            color: #666;
        }
        .buttons-container {
            text-align: center;
            margin-top: 20px;
        }
        .btn {
            margin: 5px;
            font-size: 16px;
        }
    </style>
</head>
<body>
    @yield('content')

<script>
    function printReceipt() {
        window.print();
    }
</script>
</body>
</html>
