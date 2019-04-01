<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Category;

class CategoryController extends Controller
{
    public function category(Request $request){
    	if($request->isMethod('post')){
    		//echo "<pre>"; print_r($data); die;
    		$data = $request->all();

            if(empty($data['status'])){
                $status=0;
            }else{
                $status=1;
            }

    		$category = new Category;
    		$category->name = $data['category_name'];
            $category->parent_id = $data['parent_id'];
    		$category->description = $data['description'];
    		$category->url = $data['url'];
            $category->status = $status;
    		$category->save();
    		return redirect('/admin/view-category')->with('flash_message_success','category added successfully');

    	}
        $levels = Category::where(['parent_id'=>0])->get();
            
    	return view('admin.categories.add_category')->with(compact('levels'));
    }
    public function viewcategories(){
    	$categories = Category::get();
    	return view('admin.categories.views_categories')->with(compact('categories'));
	}
    public function editcategory(Request $request,$id=null){
        if($request->isMethod('post')){
            $data = $request->all();
           //echo "<pre>"; print_r($data); die;
            //dd('$data');
            if(empty($data['status'])){
                $status=0;
            }else{
                $status=1;
            }
            Category::where(['id'=>$id])->update(['name'=>$data['category_name'],'description'=>$data['description'],'url'=>$data['url'],'status'=>$data['status']]);
            return redirect('/admin/view-category')->with('flash_message_success','category updated');
        } 
        $categoryDetails= Category::where(['id'=>$id])->first();
        $levels = Category::where(['parent_id'=>0])->get();
        return view('admin.categories.edit_category')->with(compact('categoryDetails','levels'));
    }
     public function deletecategory(Request $request,$id=null){
        if(!empty($id)){
            Category::where(['id'=>$id])->delete();
            return redirect()->back()->with('flash_message_success','category deleted');
        }
     }


}
