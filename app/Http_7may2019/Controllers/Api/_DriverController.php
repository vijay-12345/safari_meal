<?php 

namespace App\Http\Controllers\Api;

use DB, Session, Request, View, Redirect, Exception, Config;
use App\Order, App\Restaurent, App\User, App\OrderStatus;

class DriverController extends \App\Http\Controllers\Controller 
{
	public function __construct() {
		//$this->middleware('auth.api', ['except' => ['login']]);
	}

	/**
	 * Driver order api.
	 *
	 * @request
	 * POST /v1/OrderList 
	 *	{
	 * 		"long": "77.026638","lat":"29.459400","status":"1","driver_id":"12" 
	 *	}
	 * open_for_driver = 2
	 * response 
	 *
	 */		
	public function orderList()
	{
		$inputs = Request::all();
		
		try
		{
			if(isset($inputs['status']))
			{
				$radius = isset( $inputs['radius'] ) ? $inputs['radius'] : Config::get('constants.driver.radius');
				
				// Get driver details
				$driver = User::select('id','role_id','first_name','last_name','status')->where('id',$inputs['driver_id'])->first();
				if( ! $driver ) {
					throw new Exception('Driver does not exist.', 1);
				}
				
				$restaurantidarray = [];
				if(isset($inputs['long']) && isset($inputs['lat']))
				{
					$restaurantids = Restaurent::select('id', DB::raw('POW(69.1 * (latitude - '. $inputs['lat'] .'), 2) + POW(69.1 * ('. $inputs['long'] .' - longitude) * COS(latitude / 57.3), 2) AS distance'))
						->having('distance','<=',$radius)
						->get();

					foreach($restaurantids as $restaurantid){
						$restaurantidarray[] = $restaurantid->id;
					}

					// Update driver details
					$driver->last_lat = $inputs['lat'];
					$driver->last_long = $inputs['long'];
					$driver->save();
				}

				$order_q = Order::select('order.ship_json', 'order.id','order.driver_id','order.date','order.time','order.asap','order.amount','order.discount','order.order_type','order.shipping_charge','order.order_number','order.status','order.remark','order.created_at','order.ship_add1','order.ship_add2','order.ship_city','order.ship_zip','order.ship_mobile','order.ship_lat','order.ship_long','u.first_name','u.last_name', 'u.contact_number', 'coupon.coupon_code','coupon.coupon_value','restaurant.name as restaurant_name','restaurant.longitude as restaurant_longitude','restaurant.latitude as restaurant_latitude','restaurant.rating as restaurant_rating','restaurant.floor as restaurant_floor','restaurant.street as restaurant_street','restaurant.company as restaurant_company','restaurant.area_id as restaurant_area','restaurant.city_id as restaurant_city')
							->where('order.status',$inputs['status'])												
						 	->leftJoin('user as u','order.user_id','=','u.id')
						 	->leftJoin('coupon','order.coupon_id','=','coupon.id')
						 	->leftJoin('restaurant','order.restaurant_id','=','restaurant.id');
						 	
				if(count($restaurantidarray) > 0){
					$order_q = $order_q->whereIn('order.restaurant_id',$restaurantidarray);
				}
				
				$orders = $order_q->get();
				foreach($orders as $key => $order)
				{
					$order->ship_json = json_decode($order->ship_json);
					if( isset($order->ship_json->address_json) && is_string($order->ship_json->address_json) ) {
						$orders[$key]->ship_json->address_json = json_decode( $order->ship_json->address_json );
					}

					$orders[$key] = $order;
				}

				return $this->apiResponse(["data"=>$orders]);		

			}else{
				return $this->apiResponse(["error"=>"Undefined: status!"],true);
			}
		}catch(Exception $e){
			return $this->apiResponse(["message"=>$e->getMessage()],true);
		}


	}
	/**
	 * Driver's update.
	 *
	 * @request
	 * POST /v1/Update 
	 *	{
	 * 		"driver_id":"12" 
	 *	}	 * 
	 * response 
	 * provided fiew information
	 *
	 */
	public function update()
	{
		$inputs = Request::all();

		if(isset($inputs['driver_id'])){
			try{
			    $user = User::where(['id'=>$inputs['driver_id'],'role_id'=>Config::get('constants.user.driver')])->first();
			    if($user == null){
			    	return $this->apiResponse(["error"=>'Driver Id is wrong!'],true);
			    }			  
			    $user->fill($inputs)->save();
				$driver = User::select('id','role_id','first_name','last_name','status')->where('id',$inputs['driver_id'])->first();
				return $this->apiResponse(["data"=>$driver,"message"=>"Successfully updated"]);
			}catch(Exception $e){
				return $this->apiResponse(["error"=>$e->getMessage()],true);
			}

			
		}else{
			return $this->apiResponse(["error"=>trans('Undefined: driver_id')],true);
		}
	}

	/**
	 * Order's status update.
	 *
	 * @request
	 * POST /v1/OrderUpdate 
	 *	{
	 * 		"driver_id":"12",
	 *		"order_id":"10",
	 *		"status":3	 
	 *	}
	 *
	 */
	public function orderStatusUpdate()
	{
		$inputs = Request::all();

		try
		{
			// Check if driver exists
		    $user = User::where([
		    	'id' => $inputs['driver_id'],
		    	'role_id' => Config::get('constants.user.driver')
	    	])->first();

		    if( !$user ) {
		    	throw new Exception('Driver not found.', 1);
		    }

		    if($user->status != 1) {
		    	throw new Exception('Driver is inactive.', 1);
		    }

		    // Check if order is valid
		    $order = Order::where('id', $inputs['order_id'])->first();
		    if( !$order ) {
		    	throw new Exception('Order not found.', 1);
		    }

		    // Check if order is already accepted by another driver
		    if( $order->driver_id && $order->driver_id != $inputs['driver_id'] ) {
		    	throw new Exception('Order already acepted by another driver.', 1);
		    }

		    $message = null;
			switch( $inputs['status'] )
			{
				case Config::get('constants.order.cancelled'):

					$orderStatus = new OrderStatus;
					$orderStatus->order_id = $inputs['order_id'];
					$orderStatus->user_id = $inputs['driver_id'];
					$orderStatus->remark = $inputs['remark'];
					$orderStatus->save();

			    	$message = 'Order cancelled successfully!';		
					break;

				case Config::get('constants.order.accepted_by_driver'):
					$message = 'Order accepted successfully!';
					break;

				case Config::get('constants.order.dispatched'):
					$message = 'Order picked up successfully!';
					break;

				case Config::get('constants.order.delivered'):
					$message = 'Order delivered successfully!';
					break;

				default:
					$message = 'Order updated successfully.';
					break;
			}

			// Update order status
			if( $inputs['status'] == Config::get('constants.order.cancelled') ) 
			{
				$order->status = Config::get('constants.order.open_for_drivers');
				$order->driver_id = 0;
			} else  {
				$order->status = $inputs['status'];
				$order->driver_id = $inputs['driver_id'];				
			}

			$order->save();

			return $this->apiResponse(['message' => $message]);

		} catch(Exception $e) {
			return $this->apiResponse(['error' => $e->getMessage()], true);
		}
	}

	/**
	 * Completed/Accepted Order list on base driver id.
	 *
	 * @request
	 * POST /v1/orderByDriverId 
	 *	{
	 * 		"driver_id":"12" 
	 *	}	 * 
	 * response 
	 * provided fiew information
	 *
	 */
	public function orderByDriverId()
	{
		$inputs = Request::all();

		if(isset($inputs['driver_id']) && isset($inputs['status'])){
			try{
			    $user = User::where(['id'=>$inputs['driver_id'],'role_id'=>Config::get('constants.user.driver')])->first();
			    if($user == null){
			    	return $this->apiResponse(["error"=>'Driver Id is wrong!'],true);
			    }
				$order_q = Order::select('order.ship_json','order.id','order.driver_id','order.date','order.time','order.asap','order.amount','order.discount','order.order_type','order.shipping_charge','order.order_number','order.status','order.remark','order.created_at','order.ship_add1','order.ship_add2','order.ship_city','order.ship_zip','order.ship_mobile','order.ship_lat','order.ship_long','u.first_name','u.last_name','u.contact_number', 'coupon.coupon_code','coupon.coupon_value','restaurant.name as restaurant_name','restaurant.longitude as restaurant_longitude','restaurant.latitude as restaurant_latitude','restaurant.rating as restaurant_rating','restaurant.floor as restaurant_floor','restaurant.street as restaurant_street','restaurant.company as restaurant_company','restaurant.area_id as restaurant_area','restaurant.city_id as restaurant_city')
							->where(['order.status'=>$inputs['status'],'order.driver_id'=>$inputs['driver_id']])												
						 	->join('user as u','order.user_id','=','u.id')
						 	->leftJoin('coupon','order.coupon_id','=','coupon.id')
						 	->leftJoin('restaurant','order.restaurant_id','=','restaurant.id')
						 	->orderBy('order.id', 'DESC');						 	
				if(isset($inputs['date_from']) && isset($inputs['date_to'])){
					$order_q->where('order.date','>=',date('Y-m-d',strtotime($inputs['date_from'])))
					->where('order.date','<=',date('Y-m-d',strtotime($inputs['date_to'])));	
				}
				
				$orders = $order_q->get();
				if( count($orders) <= 0 ){
					return $this->apiResponse(["error"=>'No record found', 'data' => []],false);
				}
				
				foreach($orders as $key => $order) {
					$order['ship_json'] = json_decode($order['ship_json']);
					if( is_string($order->ship_json->address_json) ) {
						$orders[$key]->ship_json->address_json = json_decode( $order->ship_json->address_json );
					}
					$orders[$key] = $order;
				}
				
				return $this->apiResponse(["data"=>$orders]);		

				 
			
			}catch(Exception $e){
				return $this->apiResponse(["error"=>$e->getMessage()],true);
			}

			
		}else{
			return $this->apiResponse(["error"=>trans('Undefined: driver_id and status')],true);
		}
	}		 	
}
