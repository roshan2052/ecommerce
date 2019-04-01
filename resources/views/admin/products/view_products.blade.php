@extends('layouts.adminlayout.admin_design')
@section('content')

<div id="content">
  <div id="content-header">
    <div id="breadcrumb"> <a href="index.html" title="Go to Home" class="tip-bottom"><i class="icon-home"></i> Home</a> <a href="#">Products</a> <a href="#" class="current">View Products</a> </div>
    <h1>Products</h1>
  
    @if(Session::has('flash_message_success')) 
        <div class="alert alert-success alert-block">
            <button type="button" class="close" data-dismiss="alert">×</button> 
                <strong>{!! session('flash_message_success') !!}</strong>
        </div> 
    @endif
    @if(Session::has('flash_message_error')) 
        <div class="alert alert-error alert-block">
            <button type="button" class="close" data-dismiss="alert">×</button> 
                <strong>{!! session('flash_message_error') !!}</strong>
        </div> 
    @endif
  </div>
  <div class="container-fluid">
    <hr>
    <div class="row-fluid">
      <div class="span12">
        <div class="widget-box">
          <div class="widget-title"> <span class="icon"><i class="icon-th"></i></span>
            <h5>Views Products</h5>
          </div>
          <div class="widget-content nopadding">
            <table class="table table-bordered data-table">
              <thead>
                <tr>
                  <th>Product ID</th>
                  <th>Category ID</th>
                  <th>Category Name</th>
                  <th>Product Name</th>
                  <th>Product Code</th>
                  <th>Product Color</th>
                  <th>Price</th>
                  <th>Image</th>
                  <th>Actions</th>
                </tr>
              </thead>
              <tbody>
              	@foreach($products as $product)
                <tr class="gradeX">
                  <td>{{ $product->id }}</td>
                  <td>{{ $product->category_id }}</td>
                  <td>{{ $product->category_name }}</td>
                  <td>{{ $product->product_name }}</td>
                  <td>{{ $product->product_code }}</td>
                  <td>{{ $product->product_color }}</td>
                  <td>{{ $product->price }}</td>
                  <td>
                    @if(!empty($product->image))
                    <img style="height:100px;width:100px;" src="{{ asset('/images/backend_images/products/small/'.$product->image )}}">
                    @endif
                  </td>
                  <td class="center"> 
                    <a href="#myModal{{$product->id}}" data-toggle="modal" class="btn btn-success btn-mini-center">View</a>
                    <a href="{{url ('/admin/edit-product/'.$product->id) }}" class="btn btn-primary btn-mini-center">Edit</a>
                    <a href="{{url ('/admin/add-attribute/'.$product->id) }}" class="btn btn-primary btn-mini-center">Add</a>
                    <a rel="{{ $product->id }}" rel1="delete_product" class="btn btn-danger btn-mini-deleteRecord" href="{{url ('/admin/delete-product/'.$product->id) }}" >Delete</a>
                  </td>
                </tr>

                  <div id="myModal{{$product->id}}" class="modal hide">
                    <div class="modal-header">
                      <button data-dismiss="modal" class="close" type="button">×</button>
                      <h3>{{ $product->product_name }} Full Details</h3>
                    </div>
                    <div class="modal-body">
                      <p>product ID: {{$product->id}} </p>
                      <p>Category ID: {{$product->category_id}} </p>
                      <p>Category Name: {{$product->category_name}} </p>
                      <p>product code: {{$product->product_code}} </p>
                      <p>product color: {{$product->product_color}} </p>
                      <p>price: {{$product->price}} </p>
                      <p>Fabric: </p>
                      <p>material: </p>
                      <p>description: {{$product->description}} </p>
                    </div>
                  </div>

                @endforeach
              </tbody>
            </table>
          </divc
        </div>
      </div>
    </div>
  </div>
</div>

@endsection