@extends('frontend.layouts.master')

@section('title', 'MEW || Payment Success')

@section('main-content')
<div class="container" style="margin: 50px 0">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="alert alert-success" role="alert">
                <h4 class="alert-heading">Payment Success!</h4>
                <p class="mb-0">Your payment has been successfully processed.</p>
            </div>
        </div>
    </div>
</div>
@endsection
@push('scripts')
    <script>
        setTimeout(function() {
            window.location.href = "{{ route('home') }}";
        },2000)
    </script>
@endpush