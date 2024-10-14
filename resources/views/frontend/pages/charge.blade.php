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
                        <form action="{{route('checkout.payment')}}" id="checkout-form" method="POST">
                            @csrf
                            <input type='hidden' name='stripeToken' id='stripe-token-id'> 
                            <input type="hidden" name="first_name" value="{{ old('first_name', $firstName) }}" required>
                            <input type="hidden" name="last_name" value="{{ old('last_name', $lastName) }}" required>
                            <input type="hidden" name="phone" value="{{ old('phone', $phone) }}" required>
                            <input type="hidden" name="address1" value="{{ old('address1', $address1) }}" required>
                            <input type="hidden" name="address2" value="{{ old('address2', $address2) }}" >
                            <input type="hidden" name="post_code" value="{{ old('post_code', $postCode) }}">
                            <input type="hidden" name="coupon" value="{{ old('coupon', $coupon) }}" >
                            <input type="hidden" name="shipping" value="{{ old('shipping', $shipping) }}" required>
                            <input type="hidden" name="payment_method" value="{{ old('payment_method', $paymentMethod) }}" required>
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
                                    {{-- @php
                                        $userEmail = Auth::user()->email;
                                    @endphp --}}
                                    <div class="form-group">
                                        <label for="email">Email:</label>
                                        <input type="text" class="form-control py-2 px-3" value="{{ old('email', $email) }}" name="email" id="email" readonly>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="form-group">
                                        <label for="card-element">Credit or debit card</label>
                                        <div id="card-element">
                                            <!-- A Stripe Element will be inserted here. -->
                                        </div>
                                    </div>
                                    {{-- <span  id="card-errors" role="alert"></span> --}}

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
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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
                $('#pay-btn').text('PLEASE WAIT...');

                $.ajax({
                url: "{{route('checkout.payment')}}",
                method: 'POST',
                contentType: 'application/json',
                data: JSON.stringify({
                    amount: $('input[name="amount"]').val(),
                    email: $('input[name="email"]').val()
                }),
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                success: function(data) {
                    console.log('data', data);
                    // Use the client_secret to confirm the card payment
                    stripe.confirmCardPayment(data.client_secret, {
                    payment_method: {
                        card: cardElement // Assuming 'card' is defined and initialized elsewhere
                    }
                    }).then(function(result) {
                    if (result.error) {
                        // Show error to your customer (e.g., insufficient funds)
                        $('#card-errors').text(result.error.message);
                        swal.fire({
                            title: "Error",
                            text: result.error.message,
                            icon: "error",
                        })
                        $('#pay-btn').prop('disabled', false);
                        $('#pay-btn').text('SUBMIT PAYMENT');
                    } else {
                        if (result.paymentIntent.status === 'succeeded') {
                        // Payment succeeded, redirect to success page
                        const dataToSend = encodeURIComponent(JSON.stringify(data.order));
                        window.location.href = '/payment-success?data=' + dataToSend;
                        }
                    }
                    });
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    // Handle error response
                    console.error('AJAX error:', textStatus, errorThrown);
                    swal.fire({
                        title: 'Oops',
                        text: errorThrown,
                        icon: "error",
                    })
                    $('#card-errors').text('An error occurred. Please try again.');
                    $('#pay-btn').prop('disabled', false);
                }
                });

            })
        })
	</script>
@endpush