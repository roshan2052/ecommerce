<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

//Route::get('/admin', 'AdminController@login');
Route::match(['get','post'],'/admin','AdminController@login');

Route::get('/','IndexController@index');

Route::get('/admin/dashboard','AdminController@dashboard');

Route::get('/logout','AdminController@logout');

Route::match(['get','post'],'/add-cart','productsController@addtocart');

Route::match(['get','post'],'/cart','productsController@cart');

//delete product from cart
Route::get('/cart/delete-product/{id}','productsController@deleteCartProduct');

//get product attributes price
Route::get('/get-product-price','productsController@getProductPrice');

//update quantity in cart
Route::get('/cart/update-quantity/{id}/{quantity}','productsController@updateCartQuantity');


Route::group(['middleware' => ['auth']], function(){
    Route::get('/admin/dashboard', 'AdminController@dashboard');
    Route::any('/admin/settings', 'AdminController@settings');
    Route::get('/admin/check-pwd', 'AdminController@chkPassword');
    Route::match(['get','post'], '/admin/update-pwd', 'AdminController@updatePassword');

// categories routes
Route::match(['get','post'],'/admin/add-category','CategoryController@category');
Route::match(['get','post'],'/admin/edit-category/{id}','CategoryController@editcategory');
Route::match(['get','post'],'/admin/delete-category/{id}','CategoryController@deletecategory');
Route::get('/admin/view-category', 'CategoryController@viewcategories');

// products routes
Route::match(['get','post'],'/admin/add-product','productsController@addproduct');
Route::get('/admin/view-product','productsController@viewproduct');
Route::match(['get','post'],'/admin/edit-product/{id}','productsController@editproduct');
Route::get('/admin/delete-productimage/{id}','productsController@deleteproductimage');
Route::get('/admin/delete-product/{id}','productsController@deleteproduct');
Route::get('/admin/delete-alt-image/{id}', 'ProductsController@deleteAltImage');


// listing products
Route::get('/products/{url}','productsController@products');
//product details page
Route::get('/product/{id}','productsController@product');


//products attributes routes
Route::match(['get','post'],'/admin/add-attribute/{id}','productsController@addattributes');
Route::match(['get','post'],'/admin/edit-attribute/{id}','productsController@editAttributes');
Route::match(['get','post'],'/admin/add-images/{id}','productsController@addImages');
Route::get('admin/delete-attribute/{id}','productsController@deleteAttribute');

//coupon routes
Route::match(['get','post'],'/admin/add-coupon','couponsController@addCoupon');

});


Auth::routes();
Route::get('/home', 'HomeController@index')->name('home');





