@extends('frontend.layouts.master')

@section('title','MEW || Checkout - Payment')

@section('main-content')
<!-- Breadcrumbs -->
<div class="breadcrumbs">
    <div class="container">
        <div class="row">
            <div class="col-12">
                <div class="bread-inner">
                    <ul class="bread-list">
                        <li><a href="{{route('home')}}">Home<i class="ti-arrow-right"></i></a></li>
                        <li class="active"><a href="javascript:void(0);">Payment</a></li>
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
            <div class="col-lg-6 offset-lg-3 col-12 contact-us">
                <div class="login-form form-main">
                    <h4>Checkout - Payment</h4>
                    <div class="body mt-3">
                        <form action="{{route('checkout.payment')}}" class="form" id="checkout-form" method="POST">
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
                                        // $cart = Helper::getAllProductFromCart();
                                        $shippingPrice = \App\Models\Shipping::where('id', $shipping)->pluck('price')->first();
                                        $cartTotal = Helper::totalCartPrice();
                                        $total = $cartTotal + $shippingPrice;
                                    @endphp
                                    <div class="form-group">
                                        <label for="amount">Amount:</label>
                                        <input type="text" class="form-control py-2 px-3" value="{{ceil($total)}}" name="amount" id="amount" readonly>
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
                                    <!-- Used to display form errors. -->
                                    <div class="text-danger mb-3" id="card-errors" role="alert"></div>
                                </div>
                                <div class="col-12">
                                    <div class="form-group login-btn">
                                        <button type="button"  class="btn btn-primary" id="pay-btn">
                                            <i class="fa fa-spinner fa-spin spinner d-none"></i> Payment
                                        </button>
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
           
            //initialize stripe
            var stripe = Stripe('{{ env('STRIPE_KEY') }}')
            var elements = stripe.elements();
            //create stripe card element
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
            //mount card element to the DOM

            cardElement.mount('#card-element');
            
            // Add an event listener to the form to submit the payment when the form is submitted.
            $("#pay-btn").click(function(event){
                event.preventDefault();
                var dataRes = null;
                // Clear previous error messages
                $('#card-errors').text('');

                if (validateForm()) {              
                    $("#pay-btn").prop('disabled' , true);
                    $('.spinner').removeClass('d-none');
                    //create paymentmethod with card and validate the card details
                    stripe.createPaymentMethod({
                        type:  'card',
                        card: cardElement,
                    }).then(function(result){
                        if(result.error){
                            getErrorMessage(result);
                        }else{
                            let paymentMethodId = result.paymentMethod.id;

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
                                        // Ensure the response contains client_secret and order information
                                        if (data && data.client_secret) {
                                                // Use the client_secret to confirm the card payment
                                                stripe.confirmCardPayment(data.client_secret, {
                                                payment_method: {
                                                    card: cardElement 
                                                }
                                            }).then(function(result) {
                                                if (result.error) {
                                                    // Handle payment error
                                                    handlePaymentError(result, data);
                                                    
                                                }else {
                                                    if (result.paymentIntent.status === 'succeeded') {
                                                        handlePaymentSuccess(data.order);
                                                    }
                                                }
                                            });
                                        }else{
                                            console.error('Invalid response:', data);
                                            $('#card-errors').text('Unexpected response from server.');
                                            swal.fire({
                                                title: "Error",
                                                text: 'Unexpected response from server.',
                                                icon: "error",
                                            })
                                        }
                                    },
                                    error: function(jqXHR, textStatus, errorThrown) {
                                        // Handle error response
                                        console.error('AJAX error:', textStatus, errorThrown);
                                        $('#card-errors').text('An error occurred while processing your payment. Please try again.');
                                        swal.fire({
                                            title: 'Oops',
                                            text: 'An error occurred while processing your payment. Please try again.',
                                            icon: "error",
                                        })
                                        $('#pay-btn').prop('disabled', false);
                                        $('.spinner').addClass('d-none');
                                    }
                                });
                        }
                    })
                }
            });

            //handle payment success
            function handlePaymentSuccess(order) {
                swal.fire({
                    title: "Success!",
                    text: "Your payment has been processed successfully.",
                    icon: "success",
                }).then(() => {
                    const dataToSend = encodeURIComponent(JSON.stringify(order));
                    window.location.href = '/payment-success?data=' + dataToSend;
                });
            }

            //handle payment error
            function handlePaymentError(result, data){
                // Show error to your customer (e.g., insufficient funds)
                $('#card-errors').text(result.error.message);
                swal.fire({
                    title: "Error",
                    text: result.error.message,
                    icon: "error",
                })
                $('#pay-btn').prop('disabled', false);
                $('.spinner').addClass('d-none');

                //delete created order if there is an error in payment
                $.ajax({
                    url: `{{ route('delete.order.user', '') }}/${data.order.id}`,
                    method: 'DELETE',
                    data: { json: true },
                    headers:{
                        'X-CSRF-TOKEN': '{{csrf_token()}}'
                    },
                    success: function(data) {
                        console.log('data', data);
                    },
                    error: function(xhr, status, error) {
                        console.error(xhr, status, error);
                    }
                });
            };

            // Function to get the error message based on the error code
            function getErrorMessage(result) {
                console.log("error", result.error);
                let errorMessage;
                switch (result.error.code) {
                    case 'card_declined':
                        errorMessage = 'Your card was declined. Please check your card details or use a different card.';
                        break;
                    case 'insufficient_funds':
                        errorMessage = 'There are insufficient funds in your account. Please add funds or use a different card.';
                        break;
                    case 'invalid_card':
                        errorMessage = 'The card details you entered are invalid. Please check the card number, expiration date, and CVV.';
                        break;
                    // Add other specific cases as needed
                    case 'incomplete_number':
                        errorMessage = 'Your card number is incomplete, Please enter a valid card number.';
                        break;
                    default:
                        errorMessage = result.error.message;
                }
                $('#card-errors').text(errorMessage);
                swal.fire({
                    title: "Error",
                    text: errorMessage,
                    icon: "error",
                });
                $("#pay-btn").prop('disabled' , false);
                $('.spinner').addClass('d-none');
            }


            //validate form
            function validateForm() {
                // Get values from inputs
                const email = $('input[name="email"]').val().trim();
                const amount = $('input[name="amount"]').val().trim();

                // Email validation
                const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                if (!emailPattern.test(email)) {
                    $('#card-errors').text('Please enter a valid email address.');
                    swal.fire({
                        title: 'Oops',
                        text: 'Please enter a valid email address',
                        icon: "error",
                    })
                    return false;
                }

                // Amount validation
                if (isNaN(amount) || amount <= 0) {
                    $('#card-errors').text('Please enter a valid amount greater than zero.');
                    swal.fire({
                        title: 'Oops',
                        text: 'Please enter a valid amount greater than zero',
                        icon: "error",
                    })
                    return false;
                }

                // Card element validation
                // This will trigger Stripe's internal validation
                return true;
            }

        })
	</script>
@endpush