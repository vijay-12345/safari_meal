<?php

namespace App\Http\Controllers\Api\Restaurant;

use DB, Config, Redirect, Exception;
use App\Restaurent;
use App\helpers;
use App\Product, App\Coupon, App\City, App\User;
use App\Setting;
use App\Order;
use Illuminate\Http\Request;

class RestaurantController extends \App\Http\Controllers\Controller 
{
	public function __construct() {

	}

	/*
	* Order History API
	*/
	public function orderList(Request $request) {
		
		$input = $request->all();
		
		try {
			
			if( ! empty($input['limit'])) $limit = $input['limit'];

			else $limit = 50;

			if($request->has('sorting')) {
				$filter = ['sort'=>$request->input('sorting'),'field'=>$request->input('field')];
			} else {
				$filter = ['sort'=>'desc','field'=>'created_at'];
			}
			
			if( empty($input['user_id']) ) return $this->apiResponse(["error"=>'Please provide user id'], true );

			$user = User::where('role_id', \Config::get('constants.user.restaurant'))->find($input['user_id']);

			if( ! $user ) return $this->apiResponse(["error"=>'User is not a restaurant owner'], true );

			$restauantOwner = Restaurent::select('id','owner_id')->where('owner_id', $input['user_id'])->first();

			$order = Order::where('order.status','!=',\Config::get('constants.order.noconfirm'))
							->leftjoin('restaurant', 'order.restaurant_id', '=', 'restaurant.id')
							->leftjoin('user as driver', 'order.driver_id', '=', 'driver.id')
							->leftjoin('user', 'order.user_id', '=', 'user.id')		
							->select('order.*','restaurant.name as restaurant_name', DB::raw('CONCAT(driver.first_name, " ", driver.last_name) AS driver_name'),'user.first_name as customer_first_name','user.last_name as customer_last_name')
						    ->orderBy($filter['field'], $filter['sort']);

		    if($request->input('date') && $request->input('date') != '' && $request->input('date') != 'all') {
				$order->whereDate('order.date','=', $request->input('date'));
			}
			
		    $order = $order->whereNull('order.deleted_at')->paginate($limit);
		    
		    if(!$order->isEmpty()) {
		    	$order = $order->toArray();
		    	$order = array_merge($order, ['order_status_type'=>\Config::get('constants.order_status_label.admin')]);
		    } else $order = null;
		    
	    	return $this->apiResponse([ 'data' => $order ]);

		} catch(\Exception $e) {
			return $this->apiResponse(["error"=>$e->getMessage()], true);
		}

	}

}