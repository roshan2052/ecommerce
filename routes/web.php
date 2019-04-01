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

Route::group(['middleware' => ['auth']],function(){
	Route::get('/admin/dashboard','AdminController@dashboard');
	Route::get('/admin/settings','AdminController@settings');
	Route::get('/admin/check_pwd','AdminController@chkpassword');
});


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
// listing products
Route::get('/products/{url}','productsController@products');

//products attributes
Route::match(['get','post'],'/admin/add-attribute/{id}','productsController@addattributes');

Auth::routes();
Route::get('/home', 'HomeController@index')->name('home');





