@extends('layouts.frontlayout.front_design')
@section('content')

<section>
	<div class="container">
		<div class="row">
            @if(Session::has('flash_message_error')) 
                <div class="alert alert-error alert-block" style="background: #f44242">
                    <button type="button" class="close" data-dismiss="alert">×</button> 
                        <strong>{!! session('flash_message_error') !!}</strong>
                </div> 
            @endif
			<div class="col-sm-3">
				@include('layouts.frontlayout.front_sidebar')
			</div>
			<div class="col-sm-9 padding-right">
				<div class="product-details"><!--product-details-->
					<div class="col-sm-5">
						<div class="view-product">
                            <div class="easyzoom easyzoom--overlay easyzoom--with-thumbnails">
                            <a href="{{ asset('images/backend_images/products/large/'.$productdetails->image)}}">
							<img style="width:300px;"class="mainImage" src="{{ asset('images/backend_images/products/medium/'.$productdetails->image)}}" alt="" />
                            </a>
							<h3>ZOOM</h3>
                            </div>
						</div>
						<div id="similar-product" class="carousel slide" data-ride="carousel">
                                <!-- Wrapper for slides -->
                                <div class="carousel-inner" >
                                    <div class="item active thumbnails">
                                        <a href="{{asset('images/backend_images/products/large/'.$productdetails->image)}}" data-standard="{{asset('images/backend_images/products/small/'.$productdetails->image)}}">
                                                <img class="changeImage" style="width:80px;"
                                                class="mainImage" src="{{asset('images/backend_images/products/small/'.$productdetails->image)}}" alt="" >
                                        </a>
                                        @foreach($productAltImages as $altImage)
                                            <a href="{{asset('images/backend_images/products/large/'.$altImage->image)}}" data-standard="{{asset('images/backend_images/products/small/'.$altImage->image)}}">
                                                <img class="changeImage" src="{{asset('images/backend_images/products/small/'.$altImage->image)}}" alt="" style="width:80px;">
                                            </a>
                                         @endforeach
                                    </div>

                                </div>
                        </div>
					</div>
					<div class="col-sm-7">
                        <form action="{{url('add-cart')}}" name="addtocartForm" method="post" id="addtoCart"> @csrf
                            <input type="hidden" name="product_id" value="{{$productdetails->id}}">
                            <input type="hidden" name="product_name" value="{{$productdetails->product_name}}">
                            <input type="hidden" name="product_code" value="{{$productdetails->product_code}}">
                            <input type="hidden" name="product_color" value="{{$productdetails->product_color}}">
                            <input type="hidden" id="price" name="price" value="{{$productdetails->price}}">
						<div class="product-information"><!--/product-information-->
							<img src="images/product-details/new.jpg" class="newarrival" alt="" />
							<h2>{{$productdetails->product_name}}</h2>
							<p>Code: {{$productdetails->product_code}}</p>
							<p>
                                <select id="selSize" name="size" style="width: 150px;">
                                    <option value="">Select Size</option>
                                    @foreach($productdetails->attributes as $sizes)
                                        <option value="{{$productdetails->id}}-{{$sizes->size}}">{{$sizes->size}}</option>
                                    @endforeach
                                </select>
                            </p>
							<img src="images/product-details/rating.png" alt="" />
							<span>
								<span id="getPrice">NPR {{$productdetails->price}}</span>
								<label>Quantity:</label>
								<input type="text" name="quantity" value="2" />
                                    @if($total_stock > 0)
                                    <button type="submit" class="btn btn-fefault cart cartButton" id="cartButton">
                                        <i class="fa fa-shopping-cart"></i>
                                        Add to cart
                                    </button>
                                    @endif
							</span>
    						<p><b>Availability:</b> <span id="availability" class="availability">@if($total_stock > 0) In Stock @else Out of Stock @endif</span></p>
                                <p><b>Condition:</b> New</p>
                                <a href=""><img src="images/product-details/share.png" class="share img-responsive"  alt="" /></a>
						</div><!--/product-information-->
					</div>
				</div><!--/product-details-->
				
				<div class="category-tab shop-details-tab"><!--category-tab-->
					<div class="col-sm-12">
						<ul class="nav nav-tabs">
							<li class="active"><a href="#description" data-toggle="tab">Description</a></li>
							<li><a href="#care" data-toggle="tab">Material & Care</a></li>
							<li><a href="#delivery" data-toggle="tab">Delivery Options</a></li>
						</ul>
					</div>

					<div class="tab-content">
						<div class="tab-pane fade active in" id="description" >
							<div class="col-sm-12">
								<p>{{$productdetails->description}}</p>
							</div>
					    </div>
						
						<div class="tab-pane fade" id="care" >
							<div class="col-sm-12">
								<p>{{$productdetails->care}}</p>
							</div>
						</div>
						
						<div class="tab-pane fade" id="delivery" >
							<div class="col-sm-12">
								<p>100% original products</p>
							</div>
						</div>
					</div>

				</div><!--/category-tab-->

                <div class="recommended_items"><!--recommended_items-->
                    <h2 class="title text-center">recommended items</h2>

                    <div id="recommended-item-carousel" class="carousel slide" data-ride="carousel">
                        <div class="carousel-inner">
                            <?php $count = 1; ?>
                            @foreach($relatedProducts->chunk(3) as $chunk)
                            <div <?php if($count == 1){ ?> class="item active" <?php } else { ?> class="item" <?php } ?>>
                                @foreach($chunk as $item)
                                <div class="col-sm-4">
                                    <div class="product-image-wrapper">
                                        <div class="single-products">
                                            <div class="productinfo text-center">
                                                <img style="" src="{{asset('images/backend_images/products/small/'.$item->image)}}" alt="" />
                                                <h2>Rs. {{$item->price}}</h2>
                                                <p>{{$item->product_name}}</p> 
                                                <a href="{{url('/product/'.$item->id)}}"> 
                                                <button type="button" class="btn btn-default add-to-cart"><i class="fa fa-shopping-cart"></i>Add to cart</button>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                               @endforeach
                            </div>
                                <?php $count++ ?>
                                @endforeach

                        </div>
                        <a class="left recommended-item-control" href="#recommended-item-carousel" data-slide="prev">
                            <i class="fa fa-angle-left"></i>
                        </a>
                        <a class="right recommended-item-control" href="#recommended-item-carousel" data-slide="next">
                            <i class="fa fa-angle-right"></i>
                        </a>
                    </div>
                </div><!--/recommended_items-->

			</div>
		</div>
	</div>
</section> 

@endsection