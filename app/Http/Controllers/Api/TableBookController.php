<?php

namespace App\Http\Controllers\Api;

use DB, Config, Session, Request, View, Redirect, Exception;
use App\Restaurent, App\helpers, App\Product, App\Coupon, App\City, App\Area, App\User, App\Review, App\Table,App\Cart;
use App\Traits\CalculateDileveryCharge;
use App\Setting;

class TableBookController extends \App\Http\Controllers\Controller 
{
	use CalculateDileveryCharge;

	public function __construct() {
		//$this->middleware('auth.api', ['except' => ['login']]);
	}

	/**
	 * Show the application dashboard to the user.
	 *
	 * @return Response
	 * POST /v1/RestarantList 
	 *	{
	 * 		"area_name": "Baded B.O" 
	 *	}
	 */		
	public function bookTable()
	{	
		$inputs = Request::all();
		if(isset($inputs['customer_id'])){
		    if(isset($inputs['restaurant_id'])){
        		if(isset($inputs['customer_name'])){
        			if(isset($inputs['customer_contact'])){
        				if(isset($inputs['total_person'])){
        					if(isset($inputs['book_date']) || isset($inputs['book_time'])){
        						$ordernum = Table::createOrder($inputs, New Cart());
        						if($ordernum == true) {
        							return $this->apiResponse(['message' => trans('admin.successfully.updated')]);
        					    }
        					}
        					else{
        						return $this->apiResponse(["error"=>"Field Booking date and time are required!"],true);
        					}
        				}
        				else{
        					return $this->apiResponse(["error"=>"Field No of guest are required!"],true);
        				}
        			}
        			else{
        				return $this->apiResponse(["error"=>"Field Customer contact number are required!"],true);
        			}
        		}
        		else{
        			return $this->apiResponse(["error"=>"Field Customer name are required!"],true);
        		}
		    }	
        	else{
    			return $this->apiResponse(["error"=>"Field Restaurent id are required!"],true);
    		}	
		}
		else{
			return $this->apiResponse(["error"=>"Please Login first"],true);
		}	
	}

	

}