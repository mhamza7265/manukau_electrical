@extends('frontend.layouts.master')

@section('title', 'MEW || Verify Email')

@section('main-content')
<!-- Breadcrumbs -->
<div class="breadcrumbs">
    <div class="container">
        <div class="row">
            <div class="col-12">
                <div class="bread-inner">
                    <ul class="bread-list">
                        <li><a href="{{route('home')}}">Home<i class="ti-arrow-right"></i></a></li>
                        <li class="active"><a href="javascript:void(0);">Verify Email</a></li>

                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- End Breadcrumbs -->
<section class="shop section">
    <div class="container">
        <div class="row">
            <div class="col-lg-6 offset-lg-3 col-12 contact-us">
                <div class="verify-form form-main">
                    <div class="title">
                        <h3>Verify Your Email Address</h3>
                    </div>
                    <div class="body mt-3">
                        @if (session('resent'))
                            <div class="alert alert-success" role="alert">
                                {{ __('A fresh verification link has been sent to your email address.') }}
                            </div>
                        @endif

                        <div class="col-12"><p>{{ __('Before proceeding, please check your email for a verification link.') }}</p></div>
                        {{-- {{ __('If you did not receive the email') }}, --}}
                        <form class="d-inline" class="form" method="POST" action="{{ route('verification.resend') }}">
                            @csrf
                            <div class="col-12 mt-4">
                                <div class="form-group login-btn">
                                    <button type="submit" class="btn btn-primary">{{ __('click here to request another') }}</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
