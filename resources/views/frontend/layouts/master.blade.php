<!DOCTYPE html>
<html lang="zxx">
<head>
	@include('frontend.layouts.head')	
</head>
<body class="js">
	@include('sweetalert::alert')
	
	<!-- Preloader -->
	<div class="preloader">
		<div class="preloader-inner">
			<div class="preloader-icon">
				{{-- <span></span>
				<span></span> --}}
				@php
					$settings=DB::table('settings')->get();
					
				@endphp
				<img src="@foreach($settings as $data) {{$data->logo}} @endforeach" alt="logo">
			</div>
		</div>
	</div>
	<!-- End Preloader -->
	
	@include('frontend.layouts.notification')
	<!-- Header -->
	@include('frontend.layouts.header')
	<!--/ End Header -->
	@yield('main-content')
	
	@include('frontend.layouts.footer')

</body>
</html>