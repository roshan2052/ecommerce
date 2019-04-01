<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;

use Auth;
use Session;
use Image;
use App\Category;
use App\Product;

class productsController extends Controller
{
    public function addproduct(Request $request){
       //dd($request);
        if($request->isMethod('post')){
            $data= $request->all();
            //echo "pre"; print_r($data); die;
            if(empty($data['category_id'])){
                 return redirect()->back()->with('flash_message_error','under category is missing');
            }
            $product = new Product;
            $product->category_id = $data['category_id'];
            $product->product_name = $data['product_name'];
            $product->product_code = $data['product_code'];
            $product->product_color = $data['product_color'];
            if(empty($data['description'])){
                $product->description = $data['description'];
            }else{
                $product->description = '';
            }
            //$product->description = $data['description'];
            $product->price = $data['price'];
            //upload image
            if($request->hasFile('image')){
                 $image_tmp = Input::file('image');
                if($image_tmp->isValid()){

                    $extension = $image_tmp->getClientOriginalExtension();
                    $filename = rand(111,99999).'.'.$extension;
                    $large_image_path = 'images/backend_images/products/large/'.$filename;
                    $medium_image_path = 'images/backend_images/products/medium/'.$filename;
                    $small_image_path = 'images/backend_images/products/small/'.$filename;
                    //Resize images
                    Image::make($image_tmp)->save($large_image_path);
                    Image::make($image_tmp)->resize(600,600)->save($medium_image_path);
                    Image::make($image_tmp)->resize(300,300)->save($small_image_path);
                    //store image name in products table
                    $product->image = $filename;

                }
            }

            $product->save();
            return redirect('/admin/view-product')->with('flash_message_success','product has been added successfully'); 
        }
        // Categories dropdown start
    	$categories = Category::where(['parent_id'=>0])->get();
    	$categories_dropdown = "<option value'' selected disabled>Select</option>";
    	foreach($categories as $cat){
    		$categories_dropdown .= "<option value'".$cat->id."'>".$cat->name."</option>";
    		$sub_categories = Category::where(['parent_id'=>$cat->id])->get();
    		foreach( $sub_categories as $sub_cat){
    			$categories_dropdown .= "<option value='".$sub_cat->id."'>&nbsp--&nbsp;".$sub_cat->name.
    			"</option>";
    		}
    	} 
        // categories dropdown ends
    	return view('admin.products.add_products')->with(compact('categories_dropdown'));
    }
    public function viewproduct(){
        $products = Product::get();
        //dd($products);
        $products = json_decode(json_encode($products));
        foreach($products as  $key=> $val){
            $category_name = Category::where(['id'=>$val->category_id])->first();
           // dd($category_name);
            //dd(is_object($category_name));
            //var_dump($category_name);
            $products[$key]->category_name = $category_name['name'];
           // dd($products);
        }
        //echo "<pre>"; print_r($products); die;
        return view('admin.products.view_products')->with(compact('products'));
    }
    public function editproduct(Request $request,$id=null){
        if($request->isMethod('post')){
            $data= $request->all();
            //echo "<pre>"; print_r($data); die;

            //upload image
            if($request->hasFile('image')){
                 $image_tmp = Input::file('image');
                if($image_tmp->isValid()){
                    $extension = $image_tmp->getClientOriginalExtension();
                    $filename = rand(111,99999).'.'.$extension;
                    $large_image_path = 'images/backend_images/products/large/'.$filename;
                    $medium_image_path = 'images/backend_images/products/medium/'.$filename;
                    $small_image_path = 'images/backend_images/products/small/'.$filename;
                    //Resize images
                    Image::make($image_tmp)->save($large_image_path);
                    Image::make($image_tmp)->resize(600,600)->save($medium_image_path);
                    Image::make($image_tmp)->resize(300,300)->save($small_image_path);
                }
            }else{
                $filename = $data['current_image'];
            }

             if(empty($data['description'])){
                $data['description'] = '';
            }


            Product::where(['id'=>$id])->update(['category_id'=>$data['category_id'],'product_name'=>$data['product_name'],'product_code'=>$data['product_code'],'product_color'=>$data['product_color'],'description'=>$data['description'],'price'=>$data['price'],
                'image'=>$filename
        ]);
            return redirect('/admin/view-product')->with('flash_message_success','product has been updated successfully');
        }

        $productdetails = Product::where(['id'=>$id])->first();
        // Categories dropdown start
        $categories = Category::where(['parent_id'=>0])->get();
        $categories_dropdown = "<option value'' selected disabled>Select</option>";
        foreach($categories as $cat){
            if($cat->id==$productdetails->category_id){
                $selected = "selected";
            }else{
                $selected="";
            }
            $categories_dropdown .= "<option value'".$cat->id."'".$selected.">".$cat->name."</option>";
            $sub_categories = Category::where(['parent_id'=>$cat->id])->get();
            foreach( $sub_categories as $sub_cat){
                 if($sub_cat->id==$productdetails->category_id){
                    $selected="selected";
                }else{
                    $selected="";
                }
                $categories_dropdown .= "<option value='".$sub_cat->id."' ".$selected.">&nbsp--&nbsp;".$sub_cat->name.
                "</option>";
            }
        } 
        // categories dropdown ends
        return view('admin.products.edit_product')->with(compact('productdetails','categories_dropdown'));
    }
    public function deleteproductimage($id=null){
         Product::where(['id'=>$id])->update(['image'=>'']);
         return redirect()->back()->with('flash_message_error','Product image has been deleted successfully');

    }
    public function deleteproduct($id=null){
        Product::where(['id'=>$id])->delete();
        return redirect()->back()->with('flash_message_error','Product has been deleted successfully');
    }
    public function addattributes(Request $request,$id=null){
        //dd($request);
        $productdetails = Product::where(['id'=>$id])->first();
        //dd($productdetails);
        return view('admin.products.add_attributes')->with(compact('productdetails'));
    }
    public function products($url = null){

        $categories = Category::with('categories')->where(['parent_id'=>0])->get();
        $countcategorydetails = Category::where(['url'=>$url,'status'=>1])->count();
        if( $countcategorydetails==0){
            echo " <h2 style='color:red;' align='center'> The Products are out of Stock</h2>"; die;
        }



        $categorydetails = Category::where(['url'=>$url])->first();
       // $countcategorydetails = Category::where(['url'=>$url,'status'=>1])->count();
        //dd($categorydetails);
        //echo "<pre>"; print_r($categorydetails); die;
        $productsAll = Product::where(['category_id'=>$categorydetails->id])->get();
        return view('products.listing')->with(compact('categories','categorydetails','productsAll'));
    }
}
