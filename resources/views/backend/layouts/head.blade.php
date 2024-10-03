<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>MEW || DASHBOARD</title>
    @php
    $settings=DB::table('settings')->get();
    // dd($settings)
    @endphp  
    <link rel="icon" type="image/x-icon" href="{{$settings[0]->logo}}">
  
    <!-- Custom fonts for this template-->
    <link href="{{asset('backend/vendor/fontawesome-free/css/all.min.css')}}" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">
  
    <!-- Custom styles for this template-->
    <link href="{{asset('backend/css/sb-admin-2.min.css')}}" rel="stylesheet">
    @stack('styles')
    <style>
        .btn-primary {
            color: #fff !important;
        }
        nav[role="navigation"] > div:nth-child(2) {
            display: none !important;
        }
        nav[role="navigation"] {
            width: max-content !important;
            margin-left: auto !important;
            margin-bottom: 10px !important;
            margin-right: 10px !important;
        }

    </style>
  
</head>