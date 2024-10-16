@extends('frontend.layouts.master')

@section('title', 'MEW || Product Enquiry')

@section('main-content')
<!-- Breadcrumbs -->
<div class="breadcrumbs">
    <div class="container">
        <div class="row">
            <div class="col-12">
                <div class="bread-inner">
                    <ul class="bread-list">
                        <li><a href="{{route('home')}}">Home<i class="ti-arrow-right"></i></a></li>
                        <li class="active"><a href="javascript:void(0);">Product Enquiry</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- End Breadcrumbs -->
<section class="shop login section">
    <div class="container">
        <div class="row">
            <div class="col-lg-6 offset-lg-3 col-12 contact-us">
                <div class="login-form form-main">
                    <div class="title">
                        <h3>Product Enquiry</h3>
                        <h4>{{$product->title}}</h4>
                    </div>
                    {{-- <div class="form-group mb-3 mt-5">
                        <label for="product" class="d-inline-block m-0">Product:</label>
                        <h6 class="m-0 d-inline-block ml-2">{{$product->title}}</h6>
                    </div> --}}
                    <form method="POST" class="form" action="{{ route('price-enquiry.submit') }}">
                        @csrf
                        <input type="hidden" name="id" value="{{$id}}" >
                        <div class="row">
                            <div class="col-12">
                                <div class="form-group">
                                    <label for="name">Contact Name <span class="text-danger">*</span></label>
                                    <input id="name" type="text" name="name" class="form-control" value="{{ old('name') }}" placeholder="Enter your Name" required autocomplete="name">
                                    @error('name')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="form-group">
                                    <label for="email">Email <span class="text-danger">*</span></label>
                                    <input id="email" type="text" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}" placeholder="Enter your email" required autocomplete="email">
                                    @error('email')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="form-group">
                                    <label for="description">Enquiry <span class="text-danger">*</span></label>
                                    <textarea class="form-control"  name="description" id="description" value="{{old('description')}}" cols="30" rows="5" placeholder="Your enquiry" required>{{old('description')}}</textarea>
                                </div>
                                @error('description')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                            <div class="col-12">
                                <div class="form-group login-btn">
                                    <button type="submit" class="btn btn-primary">Submit</button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection