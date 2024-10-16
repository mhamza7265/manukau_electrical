<header class="header shop">
    <!-- Topbar -->
    <div class="topbar">
        <div class="container">
            <div class="d-flex justify-content-between align-items-center">
                <div class="">
                    <!-- Top Left -->
                    <div class="top-left">
                        <ul class="list-main">
                            @php
                                $settings=DB::table('settings')->get();
                                
                            @endphp
                            <li><i class="ti-headphone-alt"></i><a href="tel:@foreach($settings as $data) {{$data->phone}} @endforeach">@foreach($settings as $data) {{$data->phone}} @endforeach</a></li>
                            <li><i class="ti-email"></i><a href="mailto:@foreach($settings as $data) {{$data->email}} @endforeach">@foreach($settings as $data) {{$data->email}} @endforeach</a></li>
                        </ul>
                        <div class="logo-mobile mr-auto">
                            @php
                                $settings=DB::table('settings')->get();
                            @endphp                    
                            <a href="{{route('home')}}"><img src="@foreach($settings as $data) {{$data->logo}} @endforeach" alt="logo"></a>
                        </div>
                    </div>
                    
                    <!--/ End Top Left -->
                </div>
                <div class="">
                    <!-- Top Right -->
                    <div class="right-content d-flex align-items-center justify-content-around">
                        <ul class="list-main-auth">
                            <li><i class="ti-location-pin"></i> <a href="{{route('order.track')}}">Track Order</a></li>
                            {{-- <li><i class="ti-alarm-clock"></i> <a href="#">Daily deal</a></li> --}}
                            
                            @auth 
                                @if(Auth::user()->role=='admin')
                                    <li class="dash"><i class="ti-user"></i> <a href="{{route('admin')}}"  target="_blank">Dashboard</a></li>
                                @else 
                                    <li class="dash"><i class="ti-user"></i> <a href="{{route('user')}}"  target="_blank">Dashboard</a></li>
                                @endif
                                <li><i class="ti-power-off"></i> <a href="{{route('user.logout')}}">Logout</a></li>

                            @else
                                <li class="desktop-login-btn"><i class="ti-power-off"></i><a href="{{route('login.form')}}">Login /</a> <a href="{{route('register.form')}}">Register</a></li>
                                <li class="mobile-login-btn"><i class="ti-power-off"></i><a href="{{route('login.form')}}">Login</a></li>
                            @endauth
                        </ul>
                        <div class="right-bar ml-3">
                            <!-- Search Form -->
                            <div class="sinlge-bar shopping">
                                @php 
                                    $total_prod=0;
                                    $total_amount=0;
                                @endphp
                                @if(session('wishlist'))
                                    @foreach(session('wishlist') as $wishlist_items)
                                        @php
                                            $total_prod+=$wishlist_items['quantity'];
                                            $total_amount+=$wishlist_items['amount'];
                                        @endphp
                                    @endforeach
                                @endif
                                <a href="{{route('wishlist')}}" class="single-icon"><i class="fa fa-heart-o"></i> <span class="total-count {{Helper::wishlistCount() == 0 ? 'd-none' : ''}}">{{Helper::wishlistCount()}}</span></a>
                                <!-- Shopping Item -->
                                @auth
                                    <div class="shopping-item">
                                        <div class="dropdown-cart-header">
                                            <span>{{count(Helper::getAllProductFromWishlist())}} Items</span>
                                            <a href="{{route('wishlist')}}">View Wishlist</a>
                                        </div>
                                        <ul class="shopping-list">
                                            {{-- {{Helper::getAllProductFromCart()}} --}}
                                                @foreach(Helper::getAllProductFromWishlist() as $data)
                                                        @php
                                                            $photo=explode(',',$data->product['photo']);
                                                        @endphp
                                                        <li>
                                                            <a href="{{route('wishlist-delete',$data->id)}}" class="remove" title="Remove this item"><i class="fa fa-remove"></i></a>
                                                            <a class="cart-img" href="#"><img src="{{$photo[0]}}" alt="{{$photo[0]}}"></a>
                                                            <h4><a href="{{route('product-detail',$data->product['slug'])}}" target="_blank">{{$data->product['title']}}</a></h4>
                                                            <p class="quantity">{{$data->quantity}} x - <span class="amount">${{number_format($data->price,2)}}</span></p>
                                                        </li>
                                                @endforeach
                                        </ul>
                                        <div class="bottom">
                                            <div class="total">
                                                <span>Total</span>
                                                <span class="total-amount">${{number_format(Helper::totalWishlistPrice(),2)}}</span>
                                            </div>
                                            <a href="{{route('cart')}}" class="btn animate">Cart</a>
                                        </div>
                                    </div>
                                @endauth
                                <!--/ End Shopping Item -->
                            </div>
                            {{-- <div class="sinlge-bar">
                                <a href="{{route('wishlist')}}" class="single-icon"><i class="fa fa-heart-o" aria-hidden="true"></i></a>
                            </div> --}}
                            <div class="sinlge-bar shopping">
                                <a href="{{route('cart')}}" class="single-icon"><i class="ti-bag"></i> <span class="total-count {{Helper::cartCount() == 0 ? 'd-none' : ''}}">{{Helper::cartCount()}}</span></a>
                                <!-- Shopping Item -->
                                @auth
                                    <div class="shopping-item">
                                        <div class="dropdown-cart-header">
                                            <span>{{count(Helper::getAllProductFromCart())}} Items</span>
                                            <a href="{{route('cart')}}">View Cart</a>
                                        </div>
                                        <ul class="shopping-list">
                                            {{-- {{Helper::getAllProductFromCart()}} --}}
                                                @foreach(Helper::getAllProductFromCart() as $data)
                                                        @php
                                                            $photo=explode(',',$data->product['photo']);
                                                        @endphp
                                                        <li>
                                                            <a href="{{route('cart-delete',$data->id)}}" class="remove" title="Remove this item"><i class="fa fa-remove"></i></a>
                                                            <a class="cart-img" href="#"><img src="{{$photo[0]}}" alt="{{$photo[0]}}"></a>
                                                            <h4><a href="{{route('product-detail',$data->product['slug'])}}" target="_blank">{{$data->product['title']}}</a></h4>
                                                            <p class="quantity">{{$data->quantity}} x - <span class="amount">${{number_format($data->price,2)}}</span></p>
                                                        </li>
                                                @endforeach
                                        </ul>
                                        <div class="bottom">
                                            <div class="total">
                                                <span>Total</span>
                                                <span class="total-amount">${{number_format(Helper::totalCartPrice(),2)}}</span>
                                            </div>
                                            <a href="{{route('checkout')}}" class="btn animate">Checkout</a>
                                        </div>
                                    </div>
                                @endauth
                                <!--/ End Shopping Item -->
                            </div>
                        </div>
                    </div>
                    <!-- End Top Right -->
                </div>
            </div>
        </div>
    </div>
    <!-- End Topbar -->
    <div class="middle-inner">
        <div class="container-fluid">
            <div class="row align-items-center position-relative">
                <div class="col-lg-2 col-md-12 col-12 right-bar-mobile-div">
                    <div class="right-bar-mobile">
                        <!-- Search Form -->
                        <div class="sinlge-bar shopping">
                            @php 
                                $total_prod=0;
                                $total_amount=0;
                            @endphp
                           @if(session('wishlist'))
                                @foreach(session('wishlist') as $wishlist_items)
                                    @php
                                        $total_prod+=$wishlist_items['quantity'];
                                        $total_amount+=$wishlist_items['amount'];
                                    @endphp
                                @endforeach
                           @endif
                            <a href="{{route('wishlist')}}" class="single-icon"><i class="fa fa-heart-o"></i> <span class="total-count">{{Helper::wishlistCount()}}</span></a>
                            <!-- Shopping Item -->
                            @auth
                                <div class="shopping-item">
                                    <div class="dropdown-cart-header">
                                        <span>{{count(Helper::getAllProductFromWishlist())}} Items</span>
                                        <a href="{{route('wishlist')}}">View Wishlist</a>
                                    </div>
                                    <ul class="shopping-list">
                                            @foreach(Helper::getAllProductFromWishlist() as $data)
                                                    @php
                                                        $photo=explode(',',$data->product['photo']);
                                                    @endphp
                                                    <li>
                                                        <a href="{{route('wishlist-delete',$data->id)}}" class="remove" title="Remove this item"><i class="fa fa-remove"></i></a>
                                                        <a class="cart-img" href="#"><img src="{{$photo[0]}}" alt="{{$photo[0]}}"></a>
                                                        <h4><a href="{{route('product-detail',$data->product['slug'])}}" target="_blank">{{$data->product['title']}}</a></h4>
                                                        <p class="quantity">{{$data->quantity}} x - <span class="amount">${{number_format($data->price,2)}}</span></p>
                                                    </li>
                                            @endforeach
                                    </ul>
                                    <div class="bottom">
                                        <div class="total">
                                            <span>Total</span>
                                            <span class="total-amount">${{number_format(Helper::totalWishlistPrice(),2)}}</span>
                                        </div>
                                        <a href="{{route('cart')}}" class="btn animate">Cart</a>
                                    </div>
                                </div>
                            @endauth
                            <!--/ End Shopping Item -->
                        </div>
                        <div class="sinlge-bar shopping">
                            <a href="{{route('cart')}}" class="single-icon"><i class="ti-bag"></i> <span class="total-count">{{Helper::cartCount()}}</span></a>
                            <!-- Shopping Item -->
                            @auth
                                <div class="shopping-item">
                                    <div class="dropdown-cart-header">
                                        <span>{{count(Helper::getAllProductFromCart())}} Items</span>
                                        <a href="{{route('cart')}}">View Cart</a>
                                    </div>
                                    <ul class="shopping-list">
                                            @foreach(Helper::getAllProductFromCart() as $data)
                                                    @php
                                                        $photo=explode(',',$data->product['photo']);
                                                    @endphp
                                                    <li>
                                                        <a href="{{route('cart-delete',$data->id)}}" class="remove" title="Remove this item"><i class="fa fa-remove"></i></a>
                                                        <a class="cart-img" href="#"><img src="{{$photo[0]}}" alt="{{$photo[0]}}"></a>
                                                        <h4><a href="{{route('product-detail',$data->product['slug'])}}" target="_blank">{{$data->product['title']}}</a></h4>
                                                        <p class="quantity">{{$data->quantity}} x - <span class="amount">${{number_format($data->price,2)}}</span></p>
                                                    </li>
                                            @endforeach
                                    </ul>
                                    <div class="bottom">
                                        <div class="total">
                                            <span>Total</span>
                                            <span class="total-amount">${{number_format(Helper::totalCartPrice(),2)}}</span>
                                        </div>
                                        <a href="{{route('checkout')}}" class="btn animate">Checkout</a>
                                    </div>
                                </div>
                            @endauth
                            <!--/ End Shopping Item -->
                        </div>
                        <div class="hamburger">
                            <button class="hamburger-btn" id="hamburger-btn">
                                <svg width="800px" height="800px" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M4 18L20 18" stroke="#0F0957" stroke-width="2" stroke-linecap="round"/>
                                    <path d="M4 12L20 12" stroke="#0F0957" stroke-width="2" stroke-linecap="round"/>
                                    <path d="M4 6L20 6" stroke="#0F0957" stroke-width="2" stroke-linecap="round"/>
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-12 col-12">
                    <!-- Logo -->
                    <div class="logo w-max-content mx-auto">
                        @php
                            $settings=DB::table('settings')->get();
                        @endphp                    
                        <a href="{{route('home')}}"><img src="@foreach($settings as $data) {{$data->logo}} @endforeach" alt="logo"></a>
                    </div>
                    <!--/ End Logo -->
                    <!-- Search Form -->
                    {{-- <div class="search-top">
                        <div class="top-search"><a href="#0"><i class="ti-search"></i></a></div>
                        <!-- Search Form -->
                        <div class="search-top">
                            <form class="search-form">
                                <input type="text" placeholder="Search here..." name="search">
                                <button value="search" type="submit"><i class="ti-search"></i></button>
                            </form>
                        </div>
                        <!--/ End Search Form -->
                    </div> --}}
                    <!--/ End Search Form -->
                    {{-- <div class="search-top">
                        <div class="top-search"><a href="#0"><i class="ti-search"></i></a></div>
                        <!-- Search Form -->
                        <div class="search-top">
                            <form class="search-form">
                                <input type="text" placeholder="Search here..." name="search">
                                <button value="search" type="submit"><i class="ti-search"></i></button>
                            </form>
                        </div>
                        <!--/ End Search Form -->
                    </div> --}}
                    <div class="mobile-navigation">
                        <ul>
                            <li><a href="{{route('home')}}">Home</a></li>
                            <li><a href="{{route('about-us')}}">About Us</a></li>
                            <li><a href="{{route('product-grids')}}">Products</a></li>
                            <li><a href="{{route('categories')}}">Categories</a></li>
                            <li><a href="{{route('contact')}}">Contact Us</a></li>
                        </ul>
                    </div>
                </div>
                <div class="col-lg-7 col-md-12 col-12">
                    <div class="search-bar-top">
                        <div class="search-bar">
                            <select>
                                <option >All Categories</option>
                                @foreach(Helper::getAllCategory() as $cat)
                                    <option>{{$cat->title}}</option>
                                @endforeach
                            </select>
                            <form method="POST" action="{{route('product.search')}}">
                                @csrf
                                <input name="search" placeholder="Search Products Here....." type="search">
                                <button class="btnn" type="submit"><i class="ti-search"></i></button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Header Inner -->
    <div class="header-inner">
        <div class="container">
            <div class="cat-nav-head">
                <div class="row">
                    <div class="col-lg-12 col-12">
                        <div class="menu-area">
                            <!-- Main Menu -->
                            <nav class="navbar navbar-expand-lg">
                                <div class="navbar-collapse">	
                                    <div class="nav-inner mx-auto w-max-content float-0">	
                                        <ul class="nav main-menu menu navbar-nav">
                                            <li class="{{Request::path()=='home' ? 'active' : ''}}"><a href="{{route('home')}}">Home</a></li>
                                            <li class="{{Request::path()=='about-us' ? 'active' : ''}}"><a href="{{route('about-us')}}">About Us</a></li>
                                            <li class="@if(Request::path()=='product-grids'||Request::path()=='product-lists')  active  @endif"></li>
                                            <li class="{{(Request::path()=='product-grids' || Request::path()=='product-lists') ? 'active' : ''}}"><a href="{{route('product-grids')}}">Products</a><span class="new">New</span></li>	
                                            <li class="{{Request::path()=='categories' ? 'active' : ''}}"><a href="{{route('categories')}}">Categories</a></li>											
                                                {{-- {{Helper::getHeaderCategory()}} --}}
                                            {{-- <li class="{{Request::path()=='blog' ? 'active' : ''}}"><a href="{{route('blog')}}">Blog</a></li>									 --}}
                                               
                                            <li class="{{Request::path()=='contact' ? 'active' : ''}}"><a href="{{route('contact')}}">Contact Us</a></li>
                                        </ul>
                                    </div>
                                </div>
                            </nav>
                            <!--/ End Main Menu -->	
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!--/ End Header Inner -->
</header>
@push('scripts')
<script>
    $('#hamburger-btn').click(function(){
        console.log('hamburger')
        $('#hamburger-btn').toggleClass('active');
        $('.mobile-navigation').toggleClass('open'); 
    })
</script>
    
@endpush