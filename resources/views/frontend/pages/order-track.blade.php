@extends('frontend.layouts.master')

@section('title','MEW || Track Order')

@section('main-content')
    <!-- Breadcrumbs -->
    <div class="breadcrumbs">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <div class="bread-inner">
                        <ul class="bread-list">
                            <li><a href="{{route('home')}}">Home<i class="ti-arrow-right"></i></a></li>
                            <li class="active"><a href="javascript:void(0);">Track Order</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- End Breadcrumbs -->
<section class="tracking_box_area section_gap py-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="tracking_box_inner col-lg-6 col-12 contact-us">
                <div class="form-main">
                    <div class="title">
                        <h3>Track Order</h3>
                    </div>
                    <p>To track your order please enter your Order ID in the box below and press the "Track" button. This was given
                        to you on your receipt and in the confirmation email you should have received.</p>
                    <form class="row tracking_form my-4 form" action="{{route('product.track.order')}}" method="post" novalidate="novalidate">
                    @csrf
                        <div class="col-md-12 form-group">
                            <input type="text" class="form-control p-2"  name="order_number" placeholder="Enter your order number">
                        </div>
                        <div class="col-md-12 form-group">
                            <button type="submit" value="submit" class="btn submit_btn">Track Order</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection