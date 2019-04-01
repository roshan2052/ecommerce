<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Auth;
use Session;
use App\Product;
class AdminController extends Controller
{
    public function login(Request $request){
    	if($request->isMethod('post')){
    		$data= $request->input();
    		if(Auth::attempt(['email'=>$data['email'],'password'=>$data['password'],'admin'=>'1']))
            {
                // echo "success"; die;
                //Session::put('adminSession',$data['email']);
                 return redirect('admin/dashboard');
    		}
            else
            {
            return redirect('/admin')->with('flash_message_error','invalid Username or Password');
    		}
    	}
    	return view('admin.admin_login');
    }
    public function dashboard(){
        /*if(Session::has('adminSession')){
            // perform all dashboard tasks
        }else{
            return redirect('/admin')->with('flash_message_error','please login to access');
        } */
        return view('admin.dashboard');
    }
    public function logout(){
        session::flush();
        return redirect('/admin')->with('flash_message_success','logout successfully');
    }
    public function settings(){
       return view('admin.settings');
    }
    
    /*public function chkpassword(Request $request){
        $data = $request->all();
        $current_password = $data['current_pwd']
        $check_password = User::where(['admin'=>'1'])->first();
        if(Hash::check($current_password,$check_password->password)){
            echo "true"; die;
        }else{
            echo "false"; die;
        }*/
    
}
