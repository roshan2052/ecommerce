<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;

use Auth;
use Session;
use Image;
use App\Category;
use App\Product;
use App\ProductsAttribute;
use App\ProductsImage;
use DB;

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
            if(!empty($data['description'])){
                $product->description = $data['description'];
            }else{
                $product->description = '';
            }
            if(!empty($data['care'])){
                $product->care = $data['care'];
            }else{
                $product->care = '';
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
              if(empty($data['care'])){
                $data['care'] = '';
            }


            Product::where(['id'=>$id])->update(['category_id'=>$data['category_id'],'product_name'=>$data['product_name'],'product_code'=>$data['product_code'],'product_color'=>$data['product_color'],'description'=>$data['description'],'care'=>$data['care'],'price'=>$data['price'],
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
   /* public function addattributes(Request $request,$id=null){

        $productdetails = Product::with('attributes')->where(['id'=>$id])->first();
        $productdetails = json_decode(json_encode($productdetails));
        //echo "<pre>"; print_r($productdetails); die;
        if($request->isMethod('post')){
            $data = $request->all();
            // echo "<pre>"; print_r($data); die;
            foreach($data['sku'] as $key => $val){
                if(!empty($val)){
                    $attribute = new ProductsAttribute;
                    $attribute->product_id = $id;
                    $attribute->sku = $val;
                    $attribute->size = $data['size'][$key];
                    $attribute->price = $data['price'][$key];
                    $attribute->stock = $data['stock'][$key];
                    $attribute->save();
                }
            }
            return redirect('/admin/add-attribute/'.$id)->with('flash_message_success', 'Attribute added successfully');
        }
        return view ('admin.products.add_attributes')->with(compact('productdetails'));
    }*/
    public function addattributes(Request $request, $id = null){
        $productDetails = Product::with('attributes')->where(['id'=>$id])->first();
        // $productDetails = json_decode(json_encode($productDetails));
        // echo "<pre>"; print_r($productDetails); die;
        if($request->isMethod('post')){
            $data = $request->all();
            // echo "<pre>"; print_r($data); die;
            foreach($data['sku'] as $key => $val){
                if(!empty($val)){

                    // prevent duplicate SKU Check
                    $attrCountSKU = ProductsAttribute::where('sku', $val)->count();
                    if($attrCountSKU > 0){
                        return redirect('/admin/add-attribute/'.$id)->with('flash_message_error', 'SKU Already Exists');
                    }
                    // prevent duplicate Size Check
                    $attrCountSizes = ProductsAttribute::where(['product_id' => $id, 'size' => $data['size'][$key]])->count();
                    if($attrCountSizes > 0){
                        return redirect('/admin/add-attribute/'.$id)->with('flash_message_error', ' "'.$data['size'][$key].' Size Already Exists');
                    }


                    $attribute = new ProductsAttribute;
                    $attribute->product_id = $id;
                    $attribute->sku = $val;
                    $attribute->size = $data['size'][$key];
                    $attribute->price = $data['price'][$key];
                    $attribute->stock = $data['stock'][$key];
                    $attribute->save();
                }
            }
            return redirect('/admin/add-attribute/'.$id)->with('flash_message_success', 'Attribute added successfully');
        }
        return view ('admin.products.add_attributes')->with(compact('productDetails'));
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
    public function product($id = null){
        $productdetails = Product::with('attributes')->where('id',$id)->first();
        $categories = Category::with('categories')->where(['parent_id'=>0])->get();
        $productAltImages = ProductsImage::where('product_id',$id)->get();


        $relatedProducts = Product::where('id', '!=', $id)->where(['category_id' => $productdetails->category_id])->get();
        //dd($productAltImages);
        //echo "<pre>"; print_r($productAltImages); die;
        $total_stock = ProductsAttribute::where('product_id',$id)->sum('stock');

        return view('products.detail')->with(compact('productdetails','categories','productAltImages','total_stock','relatedProducts'));
    } 
    public function deleteAttribute($id = null){
        ProductsAttribute::where(['id' => $id])->delete();
        return redirect()->back()->with('flash_message_error', 'Attribute Deleted successfully');
    }
    public function getProductPrice(Request $request){
        //dd($request);
        $data = $request->all();
        //echo "<pre>"; print_r($data); die;
        $proArr = explode("-", $data['idSize']);
        ///echo $proArr[0]; echo $proArr[1]; die;
        $proAttr = ProductsAttribute::where(['product_id' => $proArr[0], 'size' => $proArr[1]])->first();
        echo $proAttr->price;
        echo "#";
        echo $proAttr->stock;
    }
    /*public function addImages(Request $request, $id = null){

        $productDetails = Product::with('attributes')->where(['id'=>$id])->first();
        // $productDetails = json_decode(json_encode($productDetails));
        // echo "<pre>"; print_r($productDetails); die;
        if($request->isMethod('post')){

        }
        return view ('admin.products.add_images')->with(compact('productDetails'));
    }*/
    public function addImages(Request $request, $id = null){
        $productDetails = Product::with('attributes')->where(['id'=>$id])->first();
        if($request->isMethod('post')){
            $data = $request->all();
            //echo "<pre>"; print_r($data); die;
            if($request->hasFile('image')){
                $files = $request->file('image');
                foreach($files as $file){
                    $image = new ProductsImage;
                    $extension = $file->getClientOriginalExtension();
                    $filename = rand(111,99999).'.'.$extension;
                    $large_image_path = 'images/backend_images/products/large/'.$filename;
                    $medium_image_path = 'images/backend_images/products/medium/'.$filename;
                    $small_image_path = 'images/backend_images/products/small/'.$filename;
                    Image::make($file)->save($large_image_path);
                    Image::make($file)->resize(600,600)->save($medium_image_path);
                    Image::make($file)->resize(300,300)->save($small_image_path);
                    $image->image = $filename;
                    $image->product_id = $data['product_id'];
                    $image->save();
                }
            }
            return redirect('admin/add-images/'.$id)->with('flash_message_success', 'Images has been added Successfully');
        }
        $productsImages = ProductsImage::where(['product_id' => $id])->get();

        return view ('admin.products.add_images')->with(compact('productDetails', 'productsImages'));
    }
    public function deleteAltImage($id = null){
        $productImage = ProductsImage::where(['id' => $id])->first();
        $large_image_path = 'images/backend_images/products/large/';
        $medium_image_path = 'images/backend_images/products/medium/';
        $small_image_path = 'images/backend_images/products/small/';
        if(file_exists($large_image_path.$productImage->image)){
            unlink($large_image_path.$productImage->image);
        }
        if(file_exists($medium_image_path.$productImage->image)){
            unlink($medium_image_path.$productImage->image);
        }
        if(file_exists($small_image_path.$productImage->image)){
            unlink($small_image_path.$productImage->image);
        }
        ProductsImage::where(['id' => $id])->delete();
        return redirect()->back()->with('flash_message_success', 'Product Alternate Image has been Deleted successfully');
    }
    public function editAttributes(Request $request, $id = null){
        if($request->isMethod('post')){
            $data = $request->all();
//            echo "<pre>"; print_r($data); die;
            foreach($data['idAttr'] as $key => $attr){
                ProductsAttribute::where(['id' => $data['idAttr'][$key]])->update(['price' => $data['price'][$key], 'stock' => $data['stock'][$key]]);
            }
            return redirect()->back()->with('flash_message_success', 'Products Attributes Updated Successfully');
        }
    }
    public function addtocart(Request $request){
        $data = $request->all();
        //$obj = json_decode (json_encode ($data), FALSE);
        //$obj = json_decode (json_encode ($data));
        //dd($obj);
        // echo "<pre>"; print_r($data); die;
        //echo "<pre>"; print_r($data); die;
        if(empty($data['user_email'])){
            $data['user_email'] = '';
        } 
        /*if(empty($data['session_id'])){
            $data['session_id']= str_random(40);
        }*/
        $session_id = Session::get('session_id');
        if(empty($session_id)){
            $session_id = str_random(40);
            Session::put('session_id',$session_id);
        }

        $sizeArr = explode("-",$data['size']);

        $countProducts = DB::table('cart')->where(['product_id' => $data['product_id'],'product_color' => $data['product_color'],'size' => $sizeArr[1], 'session_id' => $session_id])->count();

        if($countProducts > 0){
            return redirect()->back()->with('flash_message_error', 'Product Already Exists in the Cart');
        } else{

            $getSKU = ProductsAttribute::select('sku')->where(['product_id' => $data['product_id'], 'size' => $sizeArr[1]])->first();

            DB::table('cart')->insert(['product_id' => $data['product_id'],'product_name' => $data['product_name'],'product_code' =>$getSKU->sku,'product_color' => $data['product_color'],'price'=>$data['price'],'size'=>$sizeArr[1],'quantity'=>$data['quantity'],'user_email'=>$data['user_email'],'session_id' =>$session_id]);
        }
        return redirect('cart')->with('flash_message_success','product has been added in cart');
    }
    public function cart(){
        $session_id = Session::get('session_id');
        //echo "<pre>"; print_r($session_id); die;
        $usercart = DB::table('cart')->where(['session_id'=>$session_id])->get();
        //echo "<pre>"; print_r($usercart); die;
        foreach($usercart as $key => $product){
            $productDetails = Product::where('id', $product->product_id)->first();
            $usercart[$key]->image = $productDetails->image;
        }
        //echo "<pre>"; print_r($usercart); die;
        return view('products.cart')->with(compact('usercart'));
    }
    public function deleteCartProduct($id = null){

        DB::table('cart')->where('id', $id)->delete();
        return redirect('cart')->with('flash_message_error', 'product has been Deleted Successfully from cart');
    }
    public function updateCartQuantity($id = null, $quantity = null){
        $getCartDetails = DB::table('cart')->where('id', $id)->first();
        $getAttributeStock = ProductsAttribute::where('sku', $getCartDetails->product_code)->first();
        // dd($getAttributeStock);
        //echo "<pre>"; print_r($getAttributeStock); 
        //echo $getAttributeStock['stock']; 
        $updated_quantity = $getCartDetails->quantity+$quantity;
        //echo "<pre>"; print_r($updated_quantity); die;


        if($getAttributeStock['stock'] >= $updated_quantity){
        DB::table('cart')->where('id', $id)->increment('quantity', $quantity);
            return redirect('cart')->with('flash_message_success', 'Product Quantity has been Updated');
        }else{
            return redirect('cart')->with('flash_message_success', 'required quantity is not available');
        }
    }

}
