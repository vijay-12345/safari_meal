<?php 

namespace App\Http\Controllers\Api;

use DB, Session, Request, View, Redirect, Exception, Config;
use App\Order, App\Restaurent, App\User, App\OrderStatus;
use App\Traits\StaticPushNotify;

class DriverController extends \App\Http\Controllers\Controller 
{
        use StaticPushNotify;

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

				$order_q = Order::select('order.ship_json', 'order.id','order.driver_id','order.date','order.time','order.asap','order.amount','order.discount','order.order_type','order.shipping_charge','order.order_number','order.status','order.remark','order.created_at','order.ship_add1','order.ship_add2','order.ship_city','order.ship_zip','order.ship_mobile','order.ship_lat','order.ship_long','u.first_name','u.last_name',
					'u.profile_image' ,'u.contact_number', 'coupon.coupon_code','coupon.coupon_value','restaurant.name as restaurant_name','restaurant.longitude as restaurant_longitude','restaurant.latitude as restaurant_latitude','restaurant.rating as restaurant_rating','restaurant.floor as restaurant_floor','restaurant.street as restaurant_street','restaurant.company as restaurant_company','restaurant.area_id as restaurant_area','restaurant.city_id as restaurant_city')
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

				//notify 
				// $order_id = User::w
				// $orders = Order::where('id','=',$order_id)->first();
    //             $ordernum = $orders['order_number'];
    //             $this->notifyRestaurant($ordernum);

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

			// if($inputs['status'] == 5)
			// {
			// 	$this->notifyUserAboutOrderStatusDelivered($order, $inputs['status']);
			// }

			// if($inputs['status'] == 3)
			// {
			// 	$this->notifyUserAboutOrderStatusDriverAccepted($order, $inputs['status']);
			// }
			// if($inputs['status'] == 4)
			// {
			// 	$this->notifyUserAboutOrderStatusPickup($order, $inputs['status']);
			// }
			// if($inputs['status'] == 6)
			// {
			// 	$this->notifyUserAboutOrderStatusCancelled($order, $inputs['status']);
			// }
			//$this->notifyUserAboutOrderStatus($order, $inputs['status']);

			$order->save();
			$ordernum = $order['order_number'];
			$getOrder = Order::where('order_number', $ordernum)->first();   //sloved first
			if($inputs['status'] == 5)
			{
				$this->notifyUserAboutOrderStatusDelivered($order, $inputs['status']);
				$dataReturn = Order::notifyUserAboutOrderStatusDeliveredForRestaurant($getOrder->toArray(), $getOrder->id);
				$this->notifyUserAboutOrderStatusDelivered2($order, $inputs['status']);
			}

			if($inputs['status'] == 4)
			{
				$this->notifyUserAboutOrderStatusPickup($order, $inputs['status']);
				$dataReturn = Order::notifyUserAboutOrderStatusPickupForRestaurant($getOrder->toArray(), $getOrder->id);
				$this->notifyUserAboutOrderStatusPickup2($order, $inputs['status']);
			}
			if($inputs['status'] == 3)
			{
				$this->notifyUserAboutOrderStatusDriverAccepted($order, $inputs['status']);
				$dataReturn = Order::notifyUserAboutOrderStatusDriverAcceptedForRestaurant($getOrder->toArray(), $getOrder->id);
				$this->notifyUserAboutOrderStatusDriverAccepted2($order, $inputs['status']);
			}
			if($inputs['status'] == 6)
			{
				$this->notifyUserAboutOrderStatusCancelled($order, $inputs['status']);
			}
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
			}
			catch(Exception $e){
				return $this->apiResponse(["error"=>$e->getMessage()],true);
			}

		}else{
			return $this->apiResponse(["error"=>trans('Undefined: driver_id and status')],true);
		}
	}


	public function notifyUserAboutOrderStatus( $order, $status )
	{
		// Send push notification to driver from here for admin...
    	$deviceTokenToCustomer = User::where([
    						  		'id' 		=> $order->user_id,
    						  		'status' 	=> 1, 
    						  		'role_id' 	=> \Config::get('constants.user.customer')
    						  	])
    						 	->lists('device_token');
    	$customerMessage = '';
    	if ($status) {
    		if ($status == 3) $customerMessage = 'Driver is on it\'s way to the restaurant.';
    		else if ($status == 4) $customerMessage = 'Your order has been picked up by the driver.';
    		else if ($status == 5) $customerMessage = 'Your order has been delivered.';
    	}
    	StaticPushNotify::$customerMessages = [
			'title'				=> 'Order #' . $order->order_number,
			'message' 			=> $customerMessage,
			'subtitle'			=> 'Your order has placed. Enjoy your meal.',
			'tickerText'		=> 'Ticker text here...Ticker text here...Ticker text here',
			'vibrate'			=> 1,
			'sound'				=> 1,
			'largeIcon'			=> 'large_icon',
			'smallIcon'			=> 'small_icon',
			'type'				=> 'customer',
			'notificationID'	=> uniqid()
		];
		// Notify drivers for the created orders
    	if ($customerMessage && $deviceTokenToCustomer) {
    		StaticPushNotify::setReceivers($deviceTokenToCustomer);
    		StaticPushNotify::setAndroidApiAccessKey('AAAA_Gf9Xig:APA91bEf9bcQGtgjzVOx8LkE2_JgWGxopUbuTsIWqh5AlZdv0O_l4VjKTIhJ1bl7R1kcRJLRn-BQpbJLRD2Q-c_RL_wktWBC1L2NxfBwxKscvcgmLFGAltzvqFxQB9wiHdBCWFtQ5R19');

    		// StaticPushNotify::setReceivers(['c0qByHmZPh8:APA91bFzk30NczATA7-GfTK4JHqEgLAzILQWwFPRygdwrkOXO_UKnY_5gtJDNfsBpm6woUbaWQrpApwYgCVURGkAp0yMVdPTMCqCZwKZlQPckRjS0LFN_yOw9aZiH5oJuTnjRE05JkaF']);
	    	$deviceTokenToCustomer = StaticPushNotify::notify();
    	}
    	return;
	}

	public function notifyUserAboutOrderStatusPickup( $order, $status )
	{
		self::prepareNotificationMessageForPickup( $order );
			
		$user = new User;
		$usernotify = Order::where('id','=',$order['id'])->first();
		$usertoken = User::where('id','=',$usernotify['user_id'])->first();
		$deviceTokenToDrivers = $usertoken['device_token'];

 		StaticPushNotify::setAndroidApiAccessKey('AIzaSyDMNP1KHjvIrhW-TCp3v9AJ0qAHtHdcS7g');

 		// StaticPushNotify::setAndroidApiAccessKey('AAAAiJLW_vs:APA91bFZSZ-d9ll2njulL8Iv5bfhyhzltGq24stljgUhQAQ4AKlzYpsiPLHHSx7uuV1BP0pZPHYVYV3E74juyB1fhdLpCvTKlV2j2BWI91ednIdKi0H7S7tvvSy2XH3kJ1iO7ds1S8-2lbnqeCTkJqb4Ij6Un4hOow');
    	//StaticPushNotify::setAndroidApiAccessKey('AAAAO2SkPtU:APA91bFnA_zOOkumy8WnumyO-NSdcW2cspg9LI8-P0CYwCZ8b5ro8r7oWnWC8S9OVxpETHs9WCwxCD3IU5w3B4FKn4M1vi2mG4-XMab_OJ4364cm90L9holenJUyvMmFTFL-S538qvqB');
 		
		// Notify drivers for the created orders
    	if ($deviceTokenToDrivers) {    
            // echo "good eveving";
            // die;
            $final =[];
            array_push($final,$deviceTokenToDrivers);
            // print_r($final);
            // die;
    		StaticPushNotify::setReceivers($final);
	    	$notificationResponse = StaticPushNotify::notify(true);
	    	return $notificationResponse;
    	}
							// Send push notification to driver from here for admin...
					    	// $deviceTokenToCustomer = User::where([
					    	// 					  		'id' 		=> $order->user_id,
					    	// 					  		'status' 	=> 1, 
					    	// 					  		'role_id' 	=> \Config::get('constants.user.customer')
					    	// 					  	])
					    	// 					 	->lists('device_token');

					    	// $customerMessage = '';
					    	// if ($status) {
					    	// 	if ($status == 3) $customerMessage = 'Driver is on it\'s way to the restaurant.';
					    	// 	else if ($status == 4) $customerMessage = 'Your order has been picked up by the driver.';
					    	// 	else if ($status == 5) $customerMessage = 'Your order has been delivered.';
					    	// }

					  //   	StaticPushNotify::$customerMessages = [
							// 	// 'title'				=> 'Order #' . $order->order_number,
							// 	// 'message' 			=> $customerMessage,
							// 	'title'				=> 'Order is Pickup' ,
							// 	'message' 			=> ''
							// 	'subtitle'			=> 'Your order has placed. Enjoy your meal.',
							// 	'tickerText'		=> 'Ticker text here...Ticker text here...Ticker text here',
							// 	'vibrate'			=> 1,
							// 	'sound'				=> 1,
							// 	'largeIcon'			=> 'large_icon',
							// 	'smallIcon'			=> 'small_icon',
							// 	'type'				=> 'customer',
							// 	'notificationID'	=> uniqid()
							// ];

							// // Notify drivers for the created orders
					  //   	if ($customerMessage && $deviceTokenToCustomer) {

					  //   		StaticPushNotify::setReceivers($deviceTokenToCustomer);

					  //   		StaticPushNotify::setAndroidApiAccessKey('AAAA_Gf9Xig:APA91bEf9bcQGtgjzVOx8LkE2_JgWGxopUbuTsIWqh5AlZdv0O_l4VjKTIhJ1bl7R1kcRJLRn-BQpbJLRD2Q-c_RL_wktWBC1L2NxfBwxKscvcgmLFGAltzvqFxQB9wiHdBCWFtQ5R19');

					  //   		// StaticPushNotify::setReceivers(['c0qByHmZPh8:APA91bFzk30NczATA7-GfTK4JHqEgLAzILQWwFPRygdwrkOXO_UKnY_5gtJDNfsBpm6woUbaWQrpApwYgCVURGkAp0yMVdPTMCqCZwKZlQPckRjS0LFN_yOw9aZiH5oJuTnjRE05JkaF']);

						 //    	$deviceTokenToCustomer = StaticPushNotify::notify();

					  //   	}

					  //   	return;
	}

	public function notifyUserAboutOrderStatusPickup2( $order, $status )
	{
		self::prepareNotificationMessageForPickup2( $order );
			$user = new User;
			$usernotify = Order::where('id','=',$order['id'])->first();
			$usertoken = User::where('id','=',$usernotify['driver_id'])->first();
			$deviceTokenToDrivers = $usertoken['device_token'];
			
	 		StaticPushNotify::setAndroidApiAccessKey('AIzaSyDMNP1KHjvIrhW-TCp3v9AJ0qAHtHdcS7g');

	 		// StaticPushNotify::setAndroidApiAccessKey('AAAAiJLW_vs:APA91bFZSZ-d9ll2njulL8Iv5bfhyhzltGq24stljgUhQAQ4AKlzYpsiPLHHSx7uuV1BP0pZPHYVYV3E74juyB1fhdLpCvTKlV2j2BWI91ednIdKi0H7S7tvvSy2XH3kJ1iO7ds1S8-2lbnqeCTkJqb4Ij6Un4hOow');
	    	//StaticPushNotify::setAndroidApiAccessKey('AAAAO2SkPtU:APA91bFnA_zOOkumy8WnumyO-NSdcW2cspg9LI8-P0CYwCZ8b5ro8r7oWnWC8S9OVxpETHs9WCwxCD3IU5w3B4FKn4M1vi2mG4-XMab_OJ4364cm90L9holenJUyvMmFTFL-S538qvqB');
	 		
			// Notify drivers for the created orders
	    	if ($deviceTokenToDrivers) {  
                // echo "good eveving";
                // die;
                $final =[];
                array_push($final,$deviceTokenToDrivers);
                // print_r($final);
                // die;
	    		StaticPushNotify::setReceivers($final);
		    	$notificationResponse = StaticPushNotify::notify(true);
		    	return $notificationResponse;
	    	}
		
	}

	public function notifyUserAboutOrderStatusDelivered( $order, $status )
	{
		self::prepareNotificationMessageForDelivered( $order );
			$user = new User;
			$usernotify = Order::where('id','=',$order['id'])->first();
			$usertoken = User::where('id','=',$usernotify['user_id'])->first();
			$deviceTokenToDrivers = $usertoken['device_token'];
			
	 		StaticPushNotify::setAndroidApiAccessKey('AIzaSyDMNP1KHjvIrhW-TCp3v9AJ0qAHtHdcS7g');

	 		// StaticPushNotify::setAndroidApiAccessKey('AAAAiJLW_vs:APA91bFZSZ-d9ll2njulL8Iv5bfhyhzltGq24stljgUhQAQ4AKlzYpsiPLHHSx7uuV1BP0pZPHYVYV3E74juyB1fhdLpCvTKlV2j2BWI91ednIdKi0H7S7tvvSy2XH3kJ1iO7ds1S8-2lbnqeCTkJqb4Ij6Un4hOow');
	    	//StaticPushNotify::setAndroidApiAccessKey('AAAAO2SkPtU:APA91bFnA_zOOkumy8WnumyO-NSdcW2cspg9LI8-P0CYwCZ8b5ro8r7oWnWC8S9OVxpETHs9WCwxCD3IU5w3B4FKn4M1vi2mG4-XMab_OJ4364cm90L9holenJUyvMmFTFL-S538qvqB');
	 		
			// Notify drivers for the created orders
	    	if ($deviceTokenToDrivers) { 
                // echo "good eveving";
                // die;
                $final =[];
                array_push($final,$deviceTokenToDrivers);
                // print_r($final);
                // die;
	    		StaticPushNotify::setReceivers($final);
		    	$notificationResponse = StaticPushNotify::notify(true);
		    	return $notificationResponse;
	    	}
		
	}

	public function notifyUserAboutOrderStatusDelivered2( $order, $status )
	{
		self::prepareNotificationMessageForDelivered2( $order );
			$user = new User;
			$usernotify = Order::where('id','=',$order['id'])->first();
			$usertoken = User::where('id','=',$usernotify['driver_id'])->first();
			$deviceTokenToDrivers = $usertoken['device_token'];
			
	 		StaticPushNotify::setAndroidApiAccessKey('AIzaSyDMNP1KHjvIrhW-TCp3v9AJ0qAHtHdcS7g');

	 		// StaticPushNotify::setAndroidApiAccessKey('AAAAiJLW_vs:APA91bFZSZ-d9ll2njulL8Iv5bfhyhzltGq24stljgUhQAQ4AKlzYpsiPLHHSx7uuV1BP0pZPHYVYV3E74juyB1fhdLpCvTKlV2j2BWI91ednIdKi0H7S7tvvSy2XH3kJ1iO7ds1S8-2lbnqeCTkJqb4Ij6Un4hOow');
	    	//StaticPushNotify::setAndroidApiAccessKey('AAAAO2SkPtU:APA91bFnA_zOOkumy8WnumyO-NSdcW2cspg9LI8-P0CYwCZ8b5ro8r7oWnWC8S9OVxpETHs9WCwxCD3IU5w3B4FKn4M1vi2mG4-XMab_OJ4364cm90L9holenJUyvMmFTFL-S538qvqB');
	 		
			// Notify drivers for the created orders
	    	if ($deviceTokenToDrivers) {
                // echo "good eveving";
                // die;
                $final =[];
                array_push($final,$deviceTokenToDrivers);
                // print_r($final);
                // die;
	    		StaticPushNotify::setReceivers($final);
		    	$notificationResponse = StaticPushNotify::notify(true);
		    	return $notificationResponse;
	    	}
	}

	public function notifyUserAboutOrderStatusDriverAccepted( $order, $status )
	{
		self::prepareNotificationMessageForDriverAccepted( $order );
			$user = new User;
			$usernotify = Order::where('id','=',$order['id'])->first();
			$usertoken = User::where('id','=',$usernotify['user_id'])->first();
			$deviceTokenToDrivers = $usertoken['device_token'];
			
	 		StaticPushNotify::setAndroidApiAccessKey('AIzaSyDMNP1KHjvIrhW-TCp3v9AJ0qAHtHdcS7g');

	 		// StaticPushNotify::setAndroidApiAccessKey('AAAAiJLW_vs:APA91bFZSZ-d9ll2njulL8Iv5bfhyhzltGq24stljgUhQAQ4AKlzYpsiPLHHSx7uuV1BP0pZPHYVYV3E74juyB1fhdLpCvTKlV2j2BWI91ednIdKi0H7S7tvvSy2XH3kJ1iO7ds1S8-2lbnqeCTkJqb4Ij6Un4hOow');
	    	//StaticPushNotify::setAndroidApiAccessKey('AAAAO2SkPtU:APA91bFnA_zOOkumy8WnumyO-NSdcW2cspg9LI8-P0CYwCZ8b5ro8r7oWnWC8S9OVxpETHs9WCwxCD3IU5w3B4FKn4M1vi2mG4-XMab_OJ4364cm90L9holenJUyvMmFTFL-S538qvqB');
	 		
			// Notify drivers for the created orders
	    	if ($deviceTokenToDrivers) { 
                // echo "good eveving";
                // die;
                $final =[];
                array_push($final,$deviceTokenToDrivers);
                // print_r($final);
                // die;
	    		StaticPushNotify::setReceivers($final);
	    		$notificationResponse = StaticPushNotify::notify(true);
		    	return $notificationResponse;
	    	}
	}

	public function notifyUserAboutOrderStatusDriverAccepted2( $order, $status )
	{
		self::prepareNotificationMessageForDriverAccepted2( $order );
			$user = new User;
			$usernotify = Order::where('id','=',$order['id'])->first();
			$usertoken = User::where('id','=',$usernotify['driver_id'])->first();
			$deviceTokenToDrivers = $usertoken['device_token'];
			
	 		StaticPushNotify::setAndroidApiAccessKey('AIzaSyDMNP1KHjvIrhW-TCp3v9AJ0qAHtHdcS7g');

	 		// StaticPushNotify::setAndroidApiAccessKey('AAAAiJLW_vs:APA91bFZSZ-d9ll2njulL8Iv5bfhyhzltGq24stljgUhQAQ4AKlzYpsiPLHHSx7uuV1BP0pZPHYVYV3E74juyB1fhdLpCvTKlV2j2BWI91ednIdKi0H7S7tvvSy2XH3kJ1iO7ds1S8-2lbnqeCTkJqb4Ij6Un4hOow');
	    	//StaticPushNotify::setAndroidApiAccessKey('AAAAO2SkPtU:APA91bFnA_zOOkumy8WnumyO-NSdcW2cspg9LI8-P0CYwCZ8b5ro8r7oWnWC8S9OVxpETHs9WCwxCD3IU5w3B4FKn4M1vi2mG4-XMab_OJ4364cm90L9holenJUyvMmFTFL-S538qvqB');
	 		
			// Notify drivers for the created orders
	    	if ($deviceTokenToDrivers) { 
                // echo "good eveving";
                // die;
                $final =[];
                array_push($final,$deviceTokenToDrivers);
                // print_r($final);
                // die;
	    		StaticPushNotify::setReceivers($final);
		    	$notificationResponse = StaticPushNotify::notify(true);
		    	return $notificationResponse;
	    	}
	}

	public function notifyUserAboutOrderStatusCancelled( $order, $status )
	{
		self::prepareNotificationMessageForCancelled( $order );
			$user = new User;
			$usernotify = Order::where('id','=',$order['id'])->first();
			$usertoken = User::where('id','=',$usernotify['user_id'])->first();
			$deviceTokenToDrivers = $usertoken['device_token'];
			
	 		StaticPushNotify::setAndroidApiAccessKey('AIzaSyDMNP1KHjvIrhW-TCp3v9AJ0qAHtHdcS7g');

	 		// StaticPushNotify::setAndroidApiAccessKey('AAAAiJLW_vs:APA91bFZSZ-d9ll2njulL8Iv5bfhyhzltGq24stljgUhQAQ4AKlzYpsiPLHHSx7uuV1BP0pZPHYVYV3E74juyB1fhdLpCvTKlV2j2BWI91ednIdKi0H7S7tvvSy2XH3kJ1iO7ds1S8-2lbnqeCTkJqb4Ij6Un4hOow');
	    	//StaticPushNotify::setAndroidApiAccessKey('AAAAO2SkPtU:APA91bFnA_zOOkumy8WnumyO-NSdcW2cspg9LI8-P0CYwCZ8b5ro8r7oWnWC8S9OVxpETHs9WCwxCD3IU5w3B4FKn4M1vi2mG4-XMab_OJ4364cm90L9holenJUyvMmFTFL-S538qvqB');
	 		
			// Notify drivers for the created orders
	    	if ($deviceTokenToDrivers) { 
                // echo "good eveving";
                // die;
                $final =[];
                array_push($final,$deviceTokenToDrivers);
                // print_r($final);
                // die;
	    		StaticPushNotify::setReceivers($final);
		    	$notificationResponse = StaticPushNotify::notify(true);
		    	return $notificationResponse;
	    	}
	}


	public static function prepareNotificationMessageForPickup( $order )
	{
		try { 
			$data = Order::where('order_number','=',$order['order_number'])->first();
			$driverid = $data['driver_id'];
			$driverdetail = User::where('id','=',$driverid)->first();
			// $deliveryAddress = '';
			// $address = UserAddress::select('*')->Where('user_address.id', $order['delivery_address_id'])->first();

			// if ($address->first_address) $deliveryAddress = $address->first_address;
			// else if ($address->second_address) $deliveryAddress = $address->second_address;

			//   $restaurant = Restaurent::find($order['restaurant_id']);
			  
			// $restaurantAddress = '';
			// if ($restaurant) $restaurantAddress = $restaurant->name . ', ' . $restaurant->street;

			//$restaurantName = Restaurent::where('id','=',$order['restaurant_id'])->first();

			StaticPushNotify::$driverMessages = [
				//'title'				=> 'Order is Pickup.',
				'title'				=> 'Order Dispatched',
				//'message' 			=> 'Pickup from ' . $restaurantAddress . ' and drop at '. $deliveryAddress .'.',
				'body' 			=> 'Mr.'.$driverdetail['first_name'].' '.$driverdetail['last_name']. 'picked your order '.$order['order_number'].' from the restaurant.',
				//'message' 			=> 'pickup message',
				'subtitle'			=> 'New order created.',
				'tickerText'		=> 'Ticker text here...Ticker text here...Ticker text here',
				'vibrate'			=> 1,
				'sound'				=> 1,
				'largeIcon'			=> 'large_icon',
				'smallIcon'			=> 'small_icon',
				'type'				=> 'driver',
				'notificationID'	=> uniqid()
			];

		} catch (Exception $e) {
			dd($e->getMessage());
		}
	}

	public static function prepareNotificationMessageForPickup2( $order )
	{
		try {
		    $data = Order::where('order_number','=',$order['order_number'])->first();
			$driverid = $data['driver_id'];
			$driverdetail = User::where('id','=',$driverid)->first();
			// $deliveryAddress = '';
			// $address = UserAddress::select('*')->Where('user_address.id', $order['delivery_address_id'])->first();

			// if ($address->first_address) $deliveryAddress = $address->first_address;
			// else if ($address->second_address) $deliveryAddress = $address->second_address;

			//   $restaurant = Restaurent::find($order['restaurant_id']);

			// $restaurantAddress = '';
			// if ($restaurant) $restaurantAddress = $restaurant->name . ', ' . $restaurant->street;

			//$restaurantName = Restaurent::where('id','=',$order['restaurant_id'])->first();

			StaticPushNotify::$driverMessages = [
				//'title'				=> 'Order is Pickup.',
				'title'				=> 'Order Dispatched from Restaurant',
				//'message' 			=> 'Pickup from ' . $restaurantAddress . ' and drop at '. $deliveryAddress .'.',
				'body' 			=> 'Mr.'.$driverdetail['first_name'].' '.$driverdetail['last_name']. ' picked your order '.$order['order_number'].' from the restaurant.',
				//'message' 			=> 'pickup message',
				'subtitle'			=> 'Order Dispatched from Restaurant',
				'tickerText'		=> 'Ticker text here...Ticker text here...Ticker text here',
				'vibrate'			=> 1,
				'sound'				=> 1,
				'largeIcon'			=> 'large_icon',
				'smallIcon'			=> 'small_icon',
				'type'				=> 'driver',
				'notificationID'	=> uniqid()
			];

		} catch (Exception $e) {
			dd($e->getMessage());
		}
	}


	public static function prepareNotificationMessageForDelivered( $order )
	{
		try {
			StaticPushNotify::$driverMessages = [
				//'title'				=> 'Order is delivered.',
				'title'				=> 'Order Delivered Successfully',
				//'message' 			=> 'Pickup from ' . $restaurantAddress . ' and drop at '. $deliveryAddress .'.',
				'body' 			=> 'Your order '.$order['order_number'].' is successfully delivered to your location.',
				'subtitle'			=> 'New order created.',
				'tickerText'		=> 'Ticker text here...Ticker text here...Ticker text here',
				'vibrate'			=> 1,
				'sound'				=> 1,
				'largeIcon'			=> 'large_icon',
				'smallIcon'			=> 'small_icon',
				'type'				=> 'driver',
				'notificationID'	=> uniqid()
			];

		} catch (Exception $e) {
			dd($e->getMessage());
		}
	}

	public static function prepareNotificationMessageForDelivered2( $order )
	{
		try {
		    StaticPushNotify::$driverMessages = [
				//'title'				=> 'Order is delivered.',
				'title'				=> 'Order Delivered Successfully',
				//'message' 			=> 'Pickup from ' . $restaurantAddress . ' and drop at '. $deliveryAddress .'.',
				'body' 			=> 'Your order '.$order['order_number'].' has been successfully delivered.',
				'subtitle'			=> 'Order Delivered Successfully',
				'tickerText'		=> 'Ticker text here...Ticker text here...Ticker text here',
				'vibrate'			=> 1,
				'sound'				=> 1,
				'largeIcon'			=> 'large_icon',
				'smallIcon'			=> 'small_icon',
				'type'				=> 'driver',
				'notificationID'	=> uniqid()
			];
		} catch (Exception $e) {
			dd($e->getMessage());
		}
	}

	public static function prepareNotificationMessageForDriverAccepted( $order )
	{
		try {
			$data = Order::where('order_number','=',$order['order_number'])->first();
			$driverid = $data['driver_id'];
			$driverdetail = User::where('id','=',$driverid)->first();
			StaticPushNotify::$driverMessages = [
				//'title'				=> 'Order Driver Assign.',
				//'title'				=> 'Delivery Man Assigned',
				'title'				=> 'Order is accepted by driver',
				//'message' 			=> 'Pickup from ' . $restaurantAddress . ' and drop at '. $deliveryAddress .'.',
				'body' 			=>  'Mr.'.$driverdetail['first_name'].' '.$driverdetail['last_name'].' is your delivery man for order '.$order['order_number'].'.',
				'subtitle'			=> 'New order created.',
				'tickerText'		=> 'Ticker text here...Ticker text here...Ticker text here',
				'vibrate'			=> 1,
				'sound'				=> 1,
				'largeIcon'			=> 'large_icon',
				'smallIcon'			=> 'small_icon',
				'type'				=> 'driver',
				'notificationID'	=> uniqid()
			];

		} catch (Exception $e) {
			dd($e->getMessage());
		}
	}

	public static function prepareNotificationMessageForDriverAccepted2( $order )
	{
		try {
		    $data = Order::where('order_number','=',$order['order_number'])->first();
			$driverid = $data['driver_id'];
			$driverdetail = User::where('id','=',$driverid)->first();
			 
		  //   $data = Order::where('order_number','=',$order['order_number'])->first();
			 //$userid = $data['user_id'];

			 //$driverdetail = User::where('id','=',$userid)->first();
			// $deliveryAddress = '';
			// $address = UserAddress::select('*')->Where('user_address.id', $order['delivery_address_id'])->first();

			// if ($address->first_address) $deliveryAddress = $address->first_address;
			// else if ($address->second_address) $deliveryAddress = $address->second_address;

			//   $restaurant = Restaurent::find($order['restaurant_id']);
			// $restaurantAddress = '';
			// if ($restaurant) $restaurantAddress = $restaurant->name . ', ' . $restaurant->street;

			//$restaurantName = Restaurent::where('id','=',$order['restaurant_id'])->first();

			StaticPushNotify::$driverMessages = [
				//'title'				=> 'Order Driver Assign.',
				//'title'				=> 'Delivery Man Assigned',
				'title'				=> 'Order is accepted by driver',
				//'message' 			=> 'Pickup from ' . $restaurantAddress . ' and drop at '. $deliveryAddress .'.',
				'body' 			=>  'Mr. '.$driverdetail['first_name'].' '.$driverdetail['last_name'].' is your delivery man for order '.$order['order_number'].'.',
				'subtitle'			=> 'Order is accepted by driver',
				'tickerText'		=> 'Ticker text here...Ticker text here...Ticker text here',
				'vibrate'			=> 1,
				'sound'				=> 1,
				'largeIcon'			=> 'large_icon',
				'smallIcon'			=> 'small_icon',
				'type'				=> 'driver',
				'notificationID'	=> uniqid()
			];
		} catch (Exception $e) {
			dd($e->getMessage());
		}
	}

	public static function prepareNotificationMessageForCancelled( $order )
	{
		try {
			$data = Order::where('order_number','=',$order['order_number'])->first();
			$driverid = $data['driver_id'];
			$driverdetail = User::where('id','=',$driverid)->first();

			StaticPushNotify::$driverMessages = [
				//'title'				=> 'Order Driver Assign.',
				'title'				=> 'Order Cancelled',
				//'message' 			=> 'Pickup from ' . $restaurantAddress . ' and drop at '. $deliveryAddress .'.',
				'body' 			=>  'Your Order is Cancelled by ','Mr.'.$driverdetail['first_name'].' '.$driverdetail['last_name'].'.',
				'subtitle'			=> 'New order created.',
				'tickerText'		=> 'Ticker text here...Ticker text here...Ticker text here',
				'vibrate'			=> 1,
				'sound'				=> 1,
				'largeIcon'			=> 'large_icon',
				'smallIcon'			=> 'small_icon',
				'type'				=> 'driver',
				'notificationID'	=> uniqid()
			];

		} catch (Exception $e) {
			dd($e->getMessage());
		}
	}


}
