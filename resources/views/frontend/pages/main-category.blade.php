@extends('frontend.layouts.master')

@section('title', 'MEW || Categories')

@section('main-content')

<!-- Breadcrumbs -->
<div class="breadcrumbs">
    <div class="container">
        <div class="row">
            <div class="col-12">
                <div class="bread-inner">
                    <ul class="bread-list">
                        <li><a href="{{route('home')}}">Home<i class="ti-arrow-right"></i></a></li>
                        <li class="active"><a href="javascript:void(0);">Categories</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- End Breadcrumbs -->
<div class="container my-5">
    <div class="row">
        <!-- categories list -->
        <div class="col-lg-3 category-div">
            <div class="accordion" id="accordionExample">
                <div class="accordion-title">
                    <h5 class="my-3 text-center">Categories</h5>
                </div>
                @foreach($categories as $cat)
                    @if (count($cat->subCategories) > 0)
                        <div class="card">
                            <div class="card-header text-center cursor-pointer" id="heading{{$loop->iteration}}" data-toggle="collapse" data-target="#collapse{{$loop->iteration}}" aria-expanded="false" aria-controls="collapse{{$loop->iteration}}">
                                {{$cat->name}}
                            </div>

                            <div id="collapse{{$loop->iteration}}" class="collapse" aria-labelledby="heading{{$loop->iteration}}" data-parent="#accordionExample">
                            <div class="card-body">
                                <ul class="sub-categories">
                                    @foreach ($cat->subCategories as $subCategory)
                                        <li class="sub-category">
                                            <a href="{{route('products', $subCategory->slug)}}">{{$subCategory->title}}</a>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                            </div>
                        </div>
                    @endif    
                @endforeach
            </div>
        </div>
        <!-- categories list end -->
        <div class="col-lg-9 col-12 categories">
            <h4 class="my-3">Categories</h4>
            <div class="row">
                @foreach ($categoriesList as $cat)
                    <div class="col-md-8 col-lg-6 col-xl-4 mt-4">
                        <div class="card text-black">
                            <div class="image-div">
                                @if($cat->photo)
                                    <a href="{{route('sub-categories', $cat->id)}}"><img src="{{$cat->photo}}" class="img-fluid" class="card-img-top" alt="{{$cat->photo}}"></a>
                                @else
                                    <img src="{{asset('backend/img/thumbnail-default.jpg')}}" class="card-img-top" style="max-width:80px" alt="avatar.png">
                                @endif
                            </div>
                            <div class="card-body">
                                <div class="text-center">
                                    <a href="{{route('sub-categories', $cat->id)}}"><h5 class="card-title">{{$cat->name}}</h5></a>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
            {{$categoriesList->links()}}
        </div>
    </div>
</div>

@endsection