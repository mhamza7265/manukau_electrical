@extends('frontend.layouts.master')

@section('title', 'MEW || Payment Success')

@section('main-content')
<div class="container-fluid" style="margin: 100px 0">
    <div class="row justify-content-center">
        <div class="col-md-4 col-lg-4 col-12 payment-success">
            <div class="success-icon">
                <svg id="Layer_1" style="enable-background:new 0 0 512 512;" version="1.1" viewBox="0 0 512 512" xml:space="preserve" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink">
                    <style type="text/css">
                    .st0{fill:#db9c3d;}
                    .st1{fill:none;stroke:#FFFFFF;stroke-width:30;stroke-miterlimit:10;}
                    </style>
                    <path class="st0" d="M489,255.9c0-0.2,0-0.5,0-0.7c0-1.6,0-3.2-0.1-4.7c0-0.9-0.1-1.8-0.1-2.8c0-0.9-0.1-1.8-0.1-2.7  c-0.1-1.1-0.1-2.2-0.2-3.3c0-0.7-0.1-1.4-0.1-2.1c-0.1-1.2-0.2-2.4-0.3-3.6c0-0.5-0.1-1.1-0.1-1.6c-0.1-1.3-0.3-2.6-0.4-4  c0-0.3-0.1-0.7-0.1-1C474.3,113.2,375.7,22.9,256,22.9S37.7,113.2,24.5,229.5c0,0.3-0.1,0.7-0.1,1c-0.1,1.3-0.3,2.6-0.4,4  c-0.1,0.5-0.1,1.1-0.1,1.6c-0.1,1.2-0.2,2.4-0.3,3.6c0,0.7-0.1,1.4-0.1,2.1c-0.1,1.1-0.1,2.2-0.2,3.3c0,0.9-0.1,1.8-0.1,2.7  c0,0.9-0.1,1.8-0.1,2.8c0,1.6-0.1,3.2-0.1,4.7c0,0.2,0,0.5,0,0.7c0,0,0,0,0,0.1s0,0,0,0.1c0,0.2,0,0.5,0,0.7c0,1.6,0,3.2,0.1,4.7  c0,0.9,0.1,1.8,0.1,2.8c0,0.9,0.1,1.8,0.1,2.7c0.1,1.1,0.1,2.2,0.2,3.3c0,0.7,0.1,1.4,0.1,2.1c0.1,1.2,0.2,2.4,0.3,3.6  c0,0.5,0.1,1.1,0.1,1.6c0.1,1.3,0.3,2.6,0.4,4c0,0.3,0.1,0.7,0.1,1C37.7,398.8,136.3,489.1,256,489.1s218.3-90.3,231.5-206.5  c0-0.3,0.1-0.7,0.1-1c0.1-1.3,0.3-2.6,0.4-4c0.1-0.5,0.1-1.1,0.1-1.6c0.1-1.2,0.2-2.4,0.3-3.6c0-0.7,0.1-1.4,0.1-2.1  c0.1-1.1,0.1-2.2,0.2-3.3c0-0.9,0.1-1.8,0.1-2.7c0-0.9,0.1-1.8,0.1-2.8c0-1.6,0.1-3.2,0.1-4.7c0-0.2,0-0.5,0-0.7  C489,256,489,256,489,255.9C489,256,489,256,489,255.9z" id="XMLID_3_"/><g id="XMLID_1_">
                    <line class="st1" id="XMLID_2_" x1="213.6" x2="369.7" y1="344.2" y2="188.2"/>
                    <line class="st1" id="XMLID_4_" x1="233.8" x2="154.7" y1="345.2" y2="266.1"/>
                    </g>
                </svg>
            </div>
            <div class="title text-center">
                <h2>Payment Successful!</h2>
            </div>
            <div class="desc text-center mt-4">
                <p class="success-desc">Thank you for your payment. Your order has been successfully placed and you will get a confirmation email shortly.</p>
            </div>
            <div class="order-details mt-5">
                <h5 class="text-left">Order Details</h5>
                <div class="d-flex justify-content-between align-items-center mt-5">
                    <?php
                        if (isset($_GET['data'])) {
                            $data = json_decode(urldecode($_GET['data']), true);
                        }
                    ?>
                    <span class="text-secondary">Order Number</span>
                    <span class="font-weight-600">{{$data['order_number']}}</span>
                </div>
                <div class="d-flex justify-content-between align-items-center mt-2">
                    <span class="text-secondary">Amount Paid</span>
                    <span class="font-weight-600">${{$data['total_amount']}}</span>
                </div>
                <div class="d-flex justify-content-between align-items-center mt-2">
                    <span class="text-secondary">Date</span>
                    <span class="font-weight-600">{{date('d-m-Y')}}</span>
                </div>
            </div>
            <div class="action row justify-content-between align-items-center mt-4">
                <div class="col-lg-6 col-12 mt-1">
                    <a class="homepage-btn btn" href="/">Go to Homepage</a>
                </div>
                <div class="col-lg-6 col-12 mt-1">
                    <a class="dashboard-btn" href="{{route('user')}}" target="_blank">Go to Dashboard</a>
                </div>
            </div>
            {{-- <div class="alert alert-success" role="alert">
                <h4 class="alert-heading">Payment Success!</h4>
                <p class="mb-0">Your payment has been successfully processed.</p>
            </div> --}}
        </div>
    </div>
</div>
@endsection
@push('scripts')
    {{-- <script>
        setTimeout(function() {
            window.location.href = "{{ route('home') }}";
        },2000)
    </script> --}}
@endpush