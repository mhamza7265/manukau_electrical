@extends('frontend.layouts.master')

@section('title','MEA || Checkout')

@section('main-content')
<!-- Breadcrumbs -->
<div class="breadcrumbs">
    <div class="container">
        <div class="row">
            <div class="col-12">
                <div class="bread-inner">
                    <ul class="bread-list">
                        <li><a href="{{route('home')}}">Home<i class="ti-arrow-right"></i></a></li>
                        <li class="active"><a href="javascript:void(0);">Checkout</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- End Breadcrumbs -->
<section class="shop reset section">
    <div class="container">
        <div class="row">
            <div class="col-lg-6 offset-lg-3 col-12">
                <div class="login-form">
                    <h4>Checkout</h4>
                    <div class="body mt-3">
                        <form action="/charge" id="checkout-form" method="POST">
                            @csrf
                            <input type='hidden' name='stripeToken' id='stripe-token-id'> 
                            <div class="row">
                                <div class="col-12">
                                    @php
                                        $cart = Helper::getAllProductFromCart();
                                        $total = 0;
                                        foreach ($cart as $item) {
                                           $price = $item->price;
                                           $total += $price * $item->quantity;
                                        }
                                    @endphp
                                    <div class="form-group">
                                        <label for="amount">Amount (in cents):</label>
                                        <input type="text" class="form-control py-2 px-3" value="{{$total * 100}}" name="amount" id="amount" readonly>
                                    </div>
                                </div>
                                <div class="col-12">
                                    @php
                                        $userEmail = Auth::user()->email;
                                    @endphp
                                    <div class="form-group">
                                        <label for="email">Email:</label>
                                        <input type="text" class="form-control py-2 px-3" value="{{$userEmail}}" name="email" id="email" readonly>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="form-group">
                                        <label for="card-element">Credit or debit card</label>
                                        <div id="card-element">
                                            <!-- A Stripe Element will be inserted here. -->
                                        </div>
                                    </div>
                                </div>

                                <!-- Used to display form errors. -->
                                <div id="card-errors" role="alert"></div>
                                <div class="col-12">
                                    <div class="form-group login-btn">
                                        <button type="button"  class="btn btn-primary" id="pay-btn">Submit Payment</button>
                                    </div>
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
@push('scripts')
    <script src="https://js.stripe.com/v3/"></script>
    <script type="text/javascript">
        $(document).ready(function() {
            var stripe = Stripe('{{ env('STRIPE_KEY') }}')
            var elements = stripe.elements();
            var cardElement = elements.create('card',{
                style: {
                    base: {
                        color: '#32325d',
                        fontFamily: '"Helvetica Neue", Helvetica, sans-serif',
                        fontSize: '16px',
                        fontSmoothing: 'antialiased',
                        lineHeight: '24px',
                        letterSpacing: '0.025em',
                        padding: '10px',
                    },
                    invalid: {
                        color: '#fa755a',
                        iconColor: '#fa755a',
                    },
                    complete: {
                        color: '#28a745',
                    },
                },
            });
            cardElement.mount('#card-element');
            
            $("#pay-btn").click(function(){
                document.getElementById("pay-btn").disabled = true;
                stripe.createToken(cardElement).then(function(result) {
            
                    if(typeof result.error != 'undefined') {
                        document.getElementById("pay-btn").disabled = false;
                        alert(result.error.message);
                    }
            
                    /* creating token success */
                    if(typeof result.token != 'undefined') {
                        document.getElementById("stripe-token-id").value = result.token.id;
                        document.getElementById('checkout-form').submit();
                    }
                });
            })
        })
	</script>
@endpush