<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class couponsController extends Controller
{
    public function addcoupon(Request $request){
    	return view('admin.coupons.add_coupons');
    }
}
