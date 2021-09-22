<?php 

namespace App;

use Illuminate\Database\Eloquent\Model;
use DB, Exception, Config;
use App\Cart, Lang, Auth, App\UserAddress, App\OrderItem, App\Driver, App\Coupon, App\Restaurent;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\StaticPushNotify;
use App\User;
use App\Setting;

class Order extends AppModel
{
	use SoftDeletes, StaticPushNotify;

	protected $table = 'order';
	
	protected $primaryKey = 'id';
	
	public $timestamps = true;

	public $fillable = ['id', 'date', 'time', 'asap', 'user_id','driver_id', 'amount', 'discount', 'commission', 'order_type', 'shipping_charge', 'order_number', 'coupon_id', 'status', 'remark', 'created_at', 'updated_at', 'restaurant_id', 'packaging_fees', 'cgst', 'sgst', 'user_payment_method'];
    
    protected $dates = ['deleted_at'];	
	
	protected $hidden = [];	
	
	public function driver()
	{
		return $this->belongsTo('App\User','driver_id','id');
	}

	public function customer()
	{
		return $this->belongsTo('App\User','user_id','id');
	}
	
	public function createOrderItems($cart)
	{
		return $this->hasOne('App\Order','id','order_id');
	}
	
	/*public function city()
	{
		return $this->hasOne('App\City','id','city_id');
	}*/
	
	public function products() {	
		return $this->belongsToMany('App\Product','order_item','order_id','product_id')
		->withPivot('quantity', 'price');
	}
	//before image add
	// public function items() {
	// 	return $this->hasMany('App\OrderItem','order_id','id');
	// }

	public function items() {
		return $this->hasMany('App\OrderItem','order_id','id')->with('image');
	}

	// public function items2() {
	// 	return $this->hasMany('App\OrderItem','order_id','id')->with('imageForItem');
	// }

	/*
	* Written by sandeep singh
	* Date : 23-5-17
	* relation with restaurant
	*/
	public function restaurant() {
		return $this->belongsTo('App\Restaurent','restaurant_id','id')->with('image')->with('area')->with('city')->with('state')->with('country');
	}
	
	public static function createOrder($requestArray, $cart)
	{
		$order = $detail = $addons = array();
		$user = Auth::user();
		$order_number = 0;
		$request = $requestArray;
		$idforaddress = "";
		
		$customerDataId = null;
		if (isset($requestArray['search_customer'])) {
			$customerContact = substr($requestArray['search_customer'], -10);
			$customerData = User::select('user.id','user.first_name','user.last_name','user.contact_number')
					->Where('user.contact_number', 'like', '%' . trim($customerContact) . '%')->first();
			
			$customerDataId = $customerData ? $customerData->id : null;
		}

		if(count($cart->getData())) {

			$order['user_payment_method'] = 'WebCOD';
			$order['user_id'] = $customerDataId ? $customerDataId : $user->id;
			$order['amount'] = $cart->getTotal($request['order_type']);
			$order['discount'] = $cart->getcoupandiscountvalue();

			$subtotal      = $cart->getSubTotal();
			$order['cgst'] = $cart->getCGST()/100 * $subtotal;
			$order['sgst'] = $cart->getSGST()/100 * $subtotal;
			$order['packaging_fees'] = $cart->getPackagingFees();
			$order['commission'] = $cart->getAdminCommission()/100 * $subtotal;

			if(isset($request['order_type']) && $request['order_type'] == 'pickup') {
				$order['shipping_charge'] = 0;
			} else {
				$order['shipping_charge'] = $cart->deliveryCharges();
			}

			$order['status'] = \Config::get('constants.order.noconfirm');
			
			if(array_key_exists('search_customer', $request))	// backend request
			{
				$order['restaurant_id'] = $request['restaurant_id'];
				$order['order_type'] = $request['order_type'];
				$order['remark'] = trim($request['remark']);

				if(isset($request['asap']) && $request['asap'] == 'soon') $order['asap'] = 1;	
				else $order['asap'] = 0;

				if(!empty($request['radiog_lite'])) $idforaddress = $request['radiog_lite'];
				
				if(!empty($request['coupancode'])) {
					$coupandetails = Coupon::where(['coupon_code'=> $request['coupancode']])->first();
					$order["coupon_details"] = '';
					
					if(empty($coupandetails)) $coupandetails = array();

					$order["coupon_details"] = serialize($coupandetails);
				}
			} else {
				foreach($request as $key => $requestData) {				
					if(!in_array($key, array('itemaddond','_token','coupan_id'))) {
						if($key == 'asap') {
							if($requestData == 'soon') $requestData = 1;
							else $requestData = 0;
						}
						if($key =='coupon_id') {
							$coupandetails = Coupon::where(['id' => $requestData])->first();
							$key = "coupon_details";
							if(empty($coupandetails)) $coupandetails = array();
							$requestData = serialize($coupandetails);
						}
						$order[$key] = $requestData;							
					}
				}
			}
			while(true) {
				$order_number = (intval(floor(microtime(true))));	
				$orderNumber  = Order::where('order_number',$order_number)->first();
				if(count($orderNumber) <= 0) break;
			}
			
			$order['order_number'] = $order_number;
			$order['created_at'] = date('Y-m-d H:i:s');
			if(isset($request['date']) && isset($request['time']) && !empty($request['date']) && !empty($request['time'])) {
				$orderdatetime = $request['date'].' '.$request['time'];
			} else if(isset($request['orderdatetime']) && !empty($request['orderdatetime'])) {
				$orderdatetime = $request['orderdatetime'];
			} else {
				$orderdatetime = date('Y-m-d H:i:s');
			}
			
			$dt = new \DateTime($orderdatetime);
			$order['date'] = $dt->format('Y-m-d');
			$order['time'] = $dt->format('H:i:s');
			
			$orderid = Order::insertGetId($order);
			
			if($orderid > 0) {
				foreach($cart->getData() as $key => $orderitemdata) {
					if(!in_array($key, array('other'))) {
						$orderitems = array();
						$orderitems[$key]['order_id'] = $orderid;					
						$orderitems[$key]['product_id'] = $orderitemdata['prodid'];
						$orderitems[$key]['product_name'] = $orderitemdata['name'];
						if(count($orderitemdata['itemaddond']) > 0)		
							$orderitems[$key]['addons_list'] = self::getaddonsdetails($orderitemdata['itemaddond'], $orderitemdata['prodid']);
						$orderitems[$key]['description'] = $orderitemdata['description'];
						$orderitems[$key]['product_quantity'] = $orderitemdata['quantity'];
						$orderitems[$key]['product_unit_price'] = $orderitemdata['cost'];
						$orderitems[$key]['product_total_price'] = $orderitemdata['totalCost'];

						DB::table('order_item')->insert($orderitems);					
					}
				}
			}
			
			if($idforaddress != "") self::setCustomerAdderss($idforaddress, $orderid);
			
			return $order_number;										
		}			
		return false;										
	}


	public static function setCustomerAdderss($useraddressid, $orderid) {

		$customer = UserAddress::select('*')->Where('user_address.id', $useraddressid)->first();
 		$cities   = City::select('name')->where('id', $customer->city_id)->first();
 		$area     = Area::select('name')->where('id', $customer->area_id)->first();
 		$user     = User::select('contact_number')->where('id', $customer->user_id)->first();					
 		
		$order				= Order::find($orderid);
		$order->status 		= \Config::get('constants.order.pending');	
		$order->ship_add1 	= $customer->first_address;
		$order->ship_add2 	= $customer->second_address;
		$order->ship_lat 	= $customer->latitude;
		$order->ship_long	= $customer->longitude;
		$order->ship_city 	= $cities->name;
		$order->ship_zip 	= $customer->zip;
		$order->ship_mobile = $user->contact_number;
		$order->ship_json   = json_encode($customer->toArray());
		$order->save();	

		$deliveryTo = '';
		if ($customer->first_address) {
			$deliveryTo = $customer->first_address;
		} else if ($customer->second_address) {
			$deliveryTo = $customer->first_address;
		}
		return $deliveryTo;
	}

	private static function getaddonsdetails($addonsIdList,$product_id)
	{
		$productoptions = DB::table('product_options')
	   		->leftJoin('option_item', 'product_options.option_item_id', '=', 'option_item.id')
	   		->leftJoin('option_group', 'option_group.id', '=', 'option_item.option_group_id')
			->orderBy('option_group_id','ASC')	
			->select('product_options.*','option_item.*','option_group.*')	
			->whereIn('product_options.option_item_id',$addonsIdList)
			->where('product_options.product_id',$product_id)
			->get();
    	
    	return serialize($productoptions);
	}

	public function getOrderFullDetailsByOrderNumber($ordernumber)
	{
		$order = self::where('order_number', $ordernumber)->first();
		return $this->getorderfulldetails($order->id);
	}

	public static function getorderfulldetails($orderid)
	{
		$ArrayReturn = array();
		$ArrayReturn['order'] = $ArrayReturn['Driver']=$ArrayReturn['orderItems']=array();
		$ArrayReturn['order'] = self::where('id',$orderid)->first();
		$ArrayReturn['Driver'] = Driver::where('id',$ArrayReturn['order']->driver_id)->first();
		$ArrayReturn['orderItems'] = (new OrderItem())->orderitemlist($orderid);
		return $ArrayReturn;
	}

	public static function deleteOrder($orderid)
	{
		$order		= self::where('id', $orderid)->first();
		$orderItems	= (new OrderItem())->orderitemlist($orderid);
		$message	= 'Order number :'.$order->number." successfully deleted";
		$order->delete();
		$orderItems->delete();
		return $message;
	}
	

	public static function createApiOrder($requestArray)
	{
		$order = $detail = $addons = array();
		$order_number = 0;
		$request = $requestArray;

		if(count($request['order']['item']))
		{
			$restaurant = Restaurent::find($request['order']['restaurant_id']);
			if(!$restaurant) return false;

			$order['user_payment_method'] = $request['order']['user_payment_method'];
			$order['user_id'] = $request['user_id'];
			$order['sgst'] = $request['order']['sgst'];
			$order['cgst'] = $request['order']['cgst'];
			$order['packaging_fees'] = $request['order']['packaging_fees'];
			$order['amount'] = $request['order']['total'];
			$order['discount'] = $request['order']['discount'];
			$order['commission'] = ($restaurant->admin_commission/100) * $request['order']['sub_total'];

			if(isset($request['order']['delivery_type']) && $request['order']['delivery_type'] == 'is_pickup') {
				$order['shipping_charge'] = 0;
			} else {
				$order['shipping_charge'] = $request['order']['delivery_fee'];
			}
			
			$order['status'] = \Config::get('constants.order.pending');	
			$order['restaurant_id'] = $request['order']['restaurant_id'];
			$order['order_type'] = $request['order']['delivery_type'];
			if(isset($request['order']['datetime']) && !empty($request['order']['datetime'])){
				$orderdatetime = $request['order']['datetime'];
			} else {
				$orderdatetime = date('Y-m-d H:i:s');
			}
			$dt = new \DateTime($orderdatetime);
			$order['date'] = $dt->format('Y-m-d');
			$order['time'] = $dt->format('H:i:s');
			
			if(!empty($request['order']['coupancode'])) {
				$order["coupon_details"] = $request['order']['coupancode'];
			}
			
			while(true) {
				$order_number = (intval(floor(microtime(true))));	
				$orderNumber  = Order::where('order_number',$order_number)->first();
				if( count( $orderNumber ) <= 0 ) {
					break;
				}
			}

			$order['order_number'] = $order_number;
			$order['created_at'] = date('Y-m-d H:i:s');
			// shiping address
			if(isset($request['order']['delivery_address_id'])) {
				$delivery_address_id = $request['order']['delivery_address_id'];
				$customer = UserAddress::select('*')->Where('user_address.id', $delivery_address_id)->first();
				if(!empty($customer)) {
					$order['ship_json'] = json_encode($customer->toArray());
				}				
			}
			
			$orderid = Order::insertGetId($order);
			
			// if (isset($request['order']['delivery_address_id'])) {
				// Send push notification to driver from here for admin...
				// $order['delivery_address_id'] = $request['order']['delivery_address_id'];
				// if ($orderid) self::sendOrderNotification($order, $orderid);
			// }
			
			if($orderid > 0) {
				foreach($request['order']['item'] as $key => $orderitemdata){					
					$orderitems = array();						
					$orderitems[$key]['order_id'] = $orderid;					
					$orderitems[$key]['product_id'] = $orderitemdata['product_id'];
					$orderitems[$key]['product_name'] = $orderitemdata['product_name'];
					if(count($orderitemdata['addons']) >0)						
						$orderitems[$key]['addons_list'] = serialize(($orderitemdata['addons']));
					$orderitems[$key]['product_quantity'] = $orderitemdata['product_quantity'];
					$orderitems[$key]['product_unit_price'] = $orderitemdata['product_unit_price'];
					$orderitems[$key]['product_total_price'] = $orderitemdata['product_total_price'];
					DB::table('order_item')->insert($orderitems);					
				}	
			}
			return $order_number;										
		}
		return false;										
	}


	// Update existing order
	public static function updateApiOrder($request)
	{
		try {
			// Check if order exists?
			$order = Order::where('order_number', $request['order_id'])->first();
			if( ! $order ) {
				throw new Exception('Order not found.', 1);
			}

			// Check if user is owner of order
			if( $order->user_id != $request['user_id'] ) {
				throw new Exception('You are not permitted to edit this order.', 1);
			}

			// Check if order is cancelled
			if( $order->status == Config::get('constants.order.cancelled') ) {
				throw new Exception('Cancelled order cann\'t be edited.', 1);
			}

			if( $order->status > Config::get('constants.order.pending') ) {
				throw new Exception('Approved orders cann\'t be edited.', 1);
			}

			// Check if cart is empty?
			if( count( $request['order']['item']) == 0 ) {
				throw new Exception('Add one or more items in cart to place an order.', 1);
			}

			if( isset( $request['order']['delivery_type'] ) && $request['order']['delivery_type'] == 'is_pickup' ) {
				$order->shipping_charge = 0;
			} else {
				$order->shipping_charge = $request['order']['delivery_fee'];
			}
			
			$order->restaurant_id = $request['order']['restaurant_id'];
			$order->order_type = $request['order']['delivery_type'];

			$restaurant = Restaurent::find($request['order']['restaurant_id']);
			if(!$restaurant) return false;

			// Order date and time
			if( isset( $request['order']['datetime'] ) && !empty( $request['order']['datetime'] ) ) {
				$orderdatetime = $request['order']['datetime'];
			} else {
				$orderdatetime = date('Y-m-d H:i:s');
			}

			$dt = new \DateTime($orderdatetime);
			$order->date = $dt->format('Y-m-d');
			$order->time = $dt->format('H:i:s');

			// Coupon details
			if( ! empty( $request['order']['coupancode'] ) ) {
				$order->coupon_details = $request['order']['coupancode'];
			}

			// Shipping address
			if( isset( $request['order']['delivery_address_id'] ) ) 
			{
				$customer = UserAddress::select('*')->Where('user_address.id', $request['order']['delivery_address_id'])->first();
				if( ! empty( $customer ) ) {
					$order->ship_json = json_encode( $customer->toArray() );
				}
			}
			
			// Delete existing order items
			OrderItem::where('order_id', $order->id)->delete();
			
			// Save new order items
			$orderitems = array();
			foreach($request['order']['item'] as $key => $orderitemdata)
			{
				$orderitems[$key]['order_id'] = $order->id;
				$orderitems[$key]['product_id'] = $orderitemdata['product_id'];
				$orderitems[$key]['product_name'] = $orderitemdata['product_name'];
				
				if(count($orderitemdata['addons']) > 0) {
					$orderitems[$key]['addons_list'] = serialize(($orderitemdata['addons']));
				}
				
				$orderitems[$key]['product_quantity'] = $orderitemdata['product_quantity'];
				$orderitems[$key]['product_unit_price'] = $orderitemdata['product_unit_price'];
				$orderitems[$key]['product_total_price'] = $orderitemdata['product_total_price'];
			}
			
			DB::table('order_item')->insert($orderitems);
			
			$order->sgst = $request['order']['sgst'];
			$order->cgst = $request['order']['cgst'];
			$order->discount   = $request['order']['discount'];
			$order->commission = ($restaurant->admin_commission/100) * $request['order']['sub_total'];
			$order->packaging_fees = $request['order']['packaging_fees'];
			$order->amount = $request['order']['total'];
			$order->created_at = date('Y-m-d H:i:s');
			$order->user_payment_method = isset($request['order']['user_payment_method']) ? $request['order']['user_payment_method']:NULL;
			$order->save();

			return true;

		} catch( Exception $e ) {
			return $e->getMessage();
		}								
	}


	/**
	* Send notificationn to user and drivers about the created order.
	* So that the drivers can accept the delivery type orders, and user gets notified about their order.
	*
	* @param Array $order
	* @param int $orderId
	*/

	public static function sendOrderNotification(Array $order, $orderId)
	{
		try {
			if ($order['order_type'] == 'pickup') return;
			
			self::prepareNotificationMessage( $order );

			$user = new User;

			$restaurant = Restaurent::find($order['restaurant_id']);

			// Get nearby drivers and notify them only.
	 		$user = $user->select('user.*', DB::raw('POW(69.1 * (last_lat - '. $restaurant->latitude .'), 2) + POW(69.1 * ('. $restaurant->longitude .' - last_long) * COS(last_lat / 57.3), 2) AS distance'))->having('distance', '<=', 4000);

	 		$deviceTokenToDrivers = $user->where([
	    						  		'status' => 1,
	    						  		'role_id' => \Config::get('constants.user.driver')
	    						  	])
	 								->where('device_token', '!=', '')
	 								->lists('device_token');
	 		// echo '<pre>';print_r($deviceTokenToDrivers);die;
			
	 		//Driver app server key
	 		StaticPushNotify::setAndroidApiAccessKey('AIzaSyDMNP1KHjvIrhW-TCp3v9AJ0qAHtHdcS7g');
	 		//StaticPushNotify::setAndroidApiAccessKey('AAAAjATXluQ:APA91bE2SNheQDjWYxFj9UpuBV9SQJgknljQh_Smp9qFYHmN4jSWsn-YKwl15b2Bqyupyb33SISan9wzOWPCfnaO8nUZcQ_jPJOqvWdLc1fMD0lcqNj9s2c_rXOA1OBImVkRYn58ScIw');
	 		//StaticPushNotify::setAndroidApiAccessKey('AAAAiJLW_vs:APA91bFZSZ-d9ll2njulL8Iv5bfhyhzltGq24stljgUhQAQ4AKlzYpsiPLHHSx7uuV1BP0pZPHYVYV3E74juyB1fhdLpCvTKlV2j2BWI91ednIdKi0H7S7tvvSy2XH3kJ1iO7ds1S8-2lbnqeCTkJqb4Ij6Un4hOow');
	    	//StaticPushNotify::setAndroidApiAccessKey('AAAAO2SkPtU:APA91bFnA_zOOkumy8WnumyO-NSdcW2cspg9LI8-P0CYwCZ8b5ro8r7oWnWC8S9OVxpETHs9WCwxCD3IU5w3B4FKn4M1vi2mG4-XMab_OJ4364cm90L9holenJUyvMmFTFL-S538qvqB');
	 		
			// Notify drivers for the created orders
	    	if ($deviceTokenToDrivers) {

	    		StaticPushNotify::setReceivers($deviceTokenToDrivers);
	    		

		    	$notificationResponse = StaticPushNotify::notify(true);

		    	return $notificationResponse;
	    	}			
		} catch (Exception $e) {
			dd($e->getMessage());
		}
    	return;
	}



						// public static function sendOrderNotification(Array $order, $orderId)
						// {
						// 	try {

								
						// 		if ($order['order_type'] == 'pickup') return;

						// 		self::prepareNotificaPickuptionMessage( $order );

						// 		$user = new User;

						// 		$usernotify = Order::where('id','=',$orderId)->first();


						// 		$usertoken = User::select('device_token')->where('id','=',$usernotify['user_id'])->get();

						// 		self::prepareNotificaPickuptionMessageForUser( $order );


					 
						// 		$restaurant = Restaurent::find($order['restaurant_id']);

						// 		// Get nearby drivers and notify them only.
						//  		$user = $user->select('user.*', DB::raw('POW(69.1 * (last_lat - '. $restaurant->latitude .'), 2) + POW(69.1 * ('. $restaurant->longitude .' - last_long) * COS(last_lat / 57.3), 2) AS distance'))->having('distance', '<=', 4000);

						//  		$deviceTokenToDrivers = $user->where([
						//     						  		'status' => 1,
						//     						  		'role_id' => \Config::get('constants.user.driver')
						//     						  	])
						//  								->where('device_token', '!=', '')
						//  								->lists('device_token');

						//  		// print_r($deviceTokenToDrivers);
						//  		// die;
						//  		// echo '<pre>';print_r($deviceTokenToDrivers);die;
								
						//  		//Driver app server key
						//  		StaticPushNotify::setAndroidApiAccessKey('AIzaSyDMNP1KHjvIrhW-TCp3v9AJ0qAHtHdcS7g');

						//  		// StaticPushNotify::setAndroidApiAccessKey('AAAAiJLW_vs:APA91bFZSZ-d9ll2njulL8Iv5bfhyhzltGq24stljgUhQAQ4AKlzYpsiPLHHSx7uuV1BP0pZPHYVYV3E74juyB1fhdLpCvTKlV2j2BWI91ednIdKi0H7S7tvvSy2XH3kJ1iO7ds1S8-2lbnqeCTkJqb4Ij6Un4hOow');
						//     	//StaticPushNotify::setAndroidApiAccessKey('AAAAO2SkPtU:APA91bFnA_zOOkumy8WnumyO-NSdcW2cspg9LI8-P0CYwCZ8b5ro8r7oWnWC8S9OVxpETHs9WCwxCD3IU5w3B4FKn4M1vi2mG4-XMab_OJ4364cm90L9holenJUyvMmFTFL-S538qvqB');
						 		
						// 		// Notify drivers for the created orders
						//     	if ($deviceTokenToDrivers) {

						//     		StaticPushNotify::setReceivers($deviceTokenToDrivers);

						// 	    	$notificationResponse = StaticPushNotify::notify(true);

						// 	    	return $notificationResponse;
						//     	}

						// 	} catch (Exception $e) {
						// 		dd($e->getMessage());
						// 	}
					 //    	return;
						// }

						// public static function sendOrderNotification(Array $order, $orderId)
						// {
						// 	try {
						// 		if ($order['order_type'] == 'pickup') return;

						// 		self::prepareNotificationMessage( $order );

						// 		$user = new User;

						// 		$restaurant = Restaurent::find($order['restaurant_id']);

						// 		// Get nearby drivers and notify them only.
						//  		$user = $user->select('user.*', DB::raw('POW(69.1 * (last_lat - '. $restaurant->latitude .'), 2) + POW(69.1 * ('. $restaurant->longitude .' - last_long) * COS(last_lat / 57.3), 2) AS distance'))->having('distance', '<=', 4000);

						//  		$deviceTokenToDrivers = $user->where([
						//     						  		'status' => 1,
						//     						  		'role_id' => \Config::get('constants.user.driver')
						//     						  	])
						//  								->where('device_token', '!=', '')
						//  								->lists('device_token');
						//  		// echo '<pre>';print_r($deviceTokenToDrivers);die;
								
						//  		//Driver app server key
						//  		StaticPushNotify::setAndroidApiAccessKey('AAAAjATXluQ:APA91bE2SNheQDjWYxFj9UpuBV9SQJgknljQh_Smp9qFYHmN4jSWsn-YKwl15b2Bqyupyb33SISan9wzOWPCfnaO8nUZcQ_jPJOqvWdLc1fMD0lcqNj9s2c_rXOA1OBImVkRYn58ScIw');
						//  		//StaticPushNotify::setAndroidApiAccessKey('AAAAiJLW_vs:APA91bFZSZ-d9ll2njulL8Iv5bfhyhzltGq24stljgUhQAQ4AKlzYpsiPLHHSx7uuV1BP0pZPHYVYV3E74juyB1fhdLpCvTKlV2j2BWI91ednIdKi0H7S7tvvSy2XH3kJ1iO7ds1S8-2lbnqeCTkJqb4Ij6Un4hOow');
						//     	//StaticPushNotify::setAndroidApiAccessKey('AAAAO2SkPtU:APA91bFnA_zOOkumy8WnumyO-NSdcW2cspg9LI8-P0CYwCZ8b5ro8r7oWnWC8S9OVxpETHs9WCwxCD3IU5w3B4FKn4M1vi2mG4-XMab_OJ4364cm90L9holenJUyvMmFTFL-S538qvqB');
						 		
						// 		// Notify drivers for the created orders
						//     	if ($deviceTokenToDrivers) {

						//     		StaticPushNotify::setReceivers($deviceTokenToDrivers);

						// 	    	$notificationResponse = StaticPushNotify::notify(true);

						// 	    	return $notificationResponse;
						//     	}			
						// 	} catch (Exception $e) {
						// 		dd($e->getMessage());
						// 	}
					 //    	return;
						// }


	public static function sendOrderNotificationForUser(Array $order, $orderId)
	{
		try {
			self::prepareNotificationMessageForUser( $order );
			
			// echo "good";
			// die;

			$user = new User;
			$usernotify = Order::where('id','=',$orderId)->first();
			$usertoken = User::where('id','=',$usernotify['user_id'])->first();
			$deviceTokenToDrivers = $usertoken['device_token'];
			
// 			print_r("user  deviceTokenToDrivers".$deviceTokenToDrivers);
// 			die;

			//self::prepareNotificaPickuptionMessageForUser( $order );


 
			// $restaurant = Restaurent::find($order['restaurant_id']);

			// // Get nearby drivers and notify them only.
	 	// 	$user = $user->select('user.*', DB::raw('POW(69.1 * (last_lat - '. $restaurant->latitude .'), 2) + POW(69.1 * ('. $restaurant->longitude .' - last_long) * COS(last_lat / 57.3), 2) AS distance'))->having('distance', '<=', 4000);

	 	// 	$deviceTokenToDrivers = $user->where([
	  //   						  		'status' => 1,
	  //   						  		'role_id' => \Config::get('constants.user.driver')
	  //   						  	])
	 	// 							->where('device_token', '!=', '')
	 	// 							->lists('device_token');

	 		// print_r($deviceTokenToDrivers);
	 		// die;
	 		// echo '<pre>';print_r($deviceTokenToDrivers);die;
			
	 		//Driver app server key
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

		} catch (Exception $e) {
			dd($e->getMessage());
		}
    	return;
	}
    
    //notify on restaurant app
	public static function sendOrderNotificationForRestaurant2(Array $order, $orderId)
	{
		try {

 			
			self::prepareNotificationMessageForRestaurant2( $order );

			$usernotify = Order::where('id','=',$orderId)->first();
         
			$usernotify3 = Restaurent::where('id','=',$usernotify['restaurant_id'])->first();
			
			$usernotify4 = User::where('id','=',$usernotify3['owner_id'])->first();
			
			$deviceTokenToDrivers = $usernotify4['device_token'];
			

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

		} catch (Exception $e) {
			dd($e->getMessage());
		}
    	return;
	}

	public static function sendOrderNotificationForRestaurant(Array $order, $orderId)
	{
		try {

 			
			self::prepareNotificationMessageForRestaurant( $order );
			
			// echo "good";
			// die;

			$user = new User;

			$usernotify = Order::where('id','=',$orderId)->first();


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

		} catch (Exception $e) {
			dd($e->getMessage());
		}
    	return;
	}


	public static function notifyUserAboutOrderStatusPickup(Array $order, $orderId)
	{
		try{
		self::prepareNotificationMessageForPickup( $order );
			
			// echo "good";
			// die;

			$user = new User;

			$usernotify = Order::where('id','=',$orderId)->first();


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
	    catch (Exception $e) {
			dd($e->getMessage());
		}
    	return;
	}

	public static function notifyUserAboutOrderStatusPickupForRestaurant( Array $order, $orderId)
	{
	    try{
		self::prepareNotificationMessageForPickupForRestaurant( $order );
			
            $usernotify = Order::where('id','=',$orderId)->first();
         
			$usernotify3 = Restaurent::where('id','=',$usernotify['restaurant_id'])->first();
			
			$usernotify4 = User::where('id','=',$usernotify3['owner_id'])->first();
			
			$deviceTokenToDrivers = $usernotify4['device_token'];
			
			

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
	    catch (Exception $e) {
			dd($e->getMessage());
		}
    	return;
	}


	public static function notifyUserAboutOrderStatusDelivered(Array $order, $orderId)
	{
		try{
			// echo "good3";
			// die;
			
			self::prepareNotificationMessageForDelivered( $order );
			// echo "good4";
			// die;
			

			$user = new User;

			$usernotify = Order::where('id','=',$orderId)->first();


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

		    	// print_r($deviceTokenToDrivers);
		    	// die;
		    	return $notificationResponse;
	    	}
	    }
	    catch (Exception $e) {
			dd($e->getMessage());
		}
    	return;	
	}


	public static function notifyUserAboutOrderStatusDeliveredForRestaurant( Array $order, $orderId)
	{
	    try{
		self::prepareNotificationMessageForDeliveredForRestaurant( $order );
			
            $usernotify = Order::where('id','=',$orderId)->first();
         
			$usernotify3 = Restaurent::where('id','=',$usernotify['restaurant_id'])->first();
			
			$usernotify4 = User::where('id','=',$usernotify3['owner_id'])->first();
			
			$deviceTokenToDrivers = $usernotify4['device_token'];
			
			
			

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
	    catch (Exception $e) {
			dd($e->getMessage());
		}
    	return;
	}


	public static function notifyUserAboutOrderStatusDriverAccepted(Array $order, $orderId)
	{
		try{
		self::prepareNotificationMessageForDriverAccepted( $order );
			
			

			$user = new User;

			$usernotify = Order::where('id','=',$orderId)->first();


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
	    catch (Exception $e) {
			dd($e->getMessage());
		}
    	return;	
	}


	public static function notifyUserAboutOrderStatusDriverAcceptedForRestaurant(Array $order, $orderId)
	{
	    try{
		self::prepareNotificationMessageForDriverAcceptedForRestaurant( $order );
			
			$usernotify = Order::where('id','=',$orderId)->first();
         
			$usernotify3 = Restaurent::where('id','=',$usernotify['restaurant_id'])->first();
			
			$usernotify4 = User::where('id','=',$usernotify3['owner_id'])->first();
			
			$deviceTokenToDrivers = $usernotify4['device_token'];

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
	    catch (Exception $e) {
			dd($e->getMessage());
		}
    	return;
	}

	public static function notifyUserAboutOrderStatusCancelled(Array $order, $orderId)
	{
		try{
		self::prepareNotificationMessageForCancelled( $order );
			
			// echo "good";
			// die;

			$user = new User;

			$usernotify = Order::where('id','=',$orderId)->first();


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
	    catch (Exception $e) {
			dd($e->getMessage());
		}
    	return;
	}


	public static function notifyUserAboutOrderStatusCancelledForRestaurant(Array $order, $orderId)
	{
	    try{
	        
		self::prepareNotificationMessageForCancelledForRestaurant( $order );
			
			

			
            $usernotify = Order::where('id','=',$orderId)->first();
         
			$usernotify3 = Restaurent::where('id','=',$usernotify['restaurant_id'])->first();
			
			$usernotify4 = User::where('id','=',$usernotify3['owner_id'])->first();
			
			$deviceTokenToDrivers = $usernotify4['device_token'];
			
			
			

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
	    catch (Exception $e) {
			dd($e->getMessage());
		}
    	return;	
	}


	public static function notifyUserAboutOrderStatusCancelledByRestaurantToUser(Array $order, $orderId)
	{
	    try{
	        
		self::prepareNotificationMessageForCancelledByRestaurantToUser( $order );
			
			

			
            $ordernotify = Order::where('id','=',$orderId)->first();
			
			$usernotify = User::where('id','=',$ordernotify['user_id'])->first();
			
			$deviceTokenToDrivers = $usernotify['device_token'];
			
			
			

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
	    catch (Exception $e) {
			dd($e->getMessage());
		}
    	return;	
	}


	public static function notifyUserAboutOrderStatusUserNewOrderForRestaurant(Array $order, $orderId)
	{
	    try{
	        
		self::prepareNotificationMessageForUserNewOrderForRestaurant( $order );
			
			

			
            $usernotify = Order::where('id','=',$orderId)->first();
         
			$usernotify3 = Restaurent::where('id','=',$usernotify['restaurant_id'])->first();
			
			$usernotify4 = User::where('id','=',$usernotify3['owner_id'])->first();
			
			$deviceTokenToDrivers = $usernotify4['device_token'];
			
			
			

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
	    catch (Exception $e) {
			dd($e->getMessage());
		}
    	return;
		
	}



	
	public static function prepareNotificationMessage( $order )
	{
		try {
			
			$deliveryAddress = '';
			$address = UserAddress::select('*')->Where('user_address.id', $order['delivery_address_id'])->first();

			if ($address->first_address) $deliveryAddress = $address->first_address;
			else if ($address->second_address) $deliveryAddress = $address->second_address;

			$restaurant = Restaurent::find($order['restaurant_id']);

			$restaurantAddress = '';
			if ($restaurant) $restaurantAddress = $restaurant->name . ', ' . $restaurant->street;

			StaticPushNotify::$driverMessages = [
				'title'				=> 'New order created.',
				'message' 			=> 'Pickup from ' . $restaurantAddress . ' and drop at '. $deliveryAddress .'.',
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


	


	public static function prepareNotificationMessageForUser( $order )
	{
		try {
			 
			// $deliveryAddress = '';
			// $address = UserAddress::select('*')->Where('user_address.id', $order['delivery_address_id'])->first();

			// if ($address->first_address) $deliveryAddress = $address->first_address;
			// else if ($address->second_address) $deliveryAddress = $address->second_address;

			   $restaurant = Restaurent::find($order['restaurant_id']);

			  


			// $restaurantAddress = '';
			// if ($restaurant) $restaurantAddress = $restaurant->name . ', ' . $restaurant->street;

			$restaurantName = Restaurent::where('id','=',$order['restaurant_id'])->first();

			StaticPushNotify::$driverMessages = [
				'title'				=> 'Order Placed successfully.',
				//'message' 			=> 'Pickup from ' . $restaurantAddress . ' and drop at '. $deliveryAddress .'.',
				//'message' 			=> 'order no is' .$order['id']. 'and  restaurant name is' .$restaurantName['name'].'.',
				'body' 			=> 'Your order on ' .$restaurant->name. ' has been placed successfully. Your Order Number is '.$order['order_number'],
				'subtitle'			=> 'New order created.',
				'tickerText'		=> 'Ticker text here...Ticker text here...Ticker text here',
				'vibrate'			=> 1,
				'sound'				=> 1,
				'largeIcon'			=> 'large_icon',
				'smallIcon'			=> 'small_icon',
				'type'				=> 'customer',
				'notificationID'	=> uniqid()
			];

		} catch (Exception $e) {
			dd($e->getMessage());
		}
	}


	public static function prepareNotificationMessageForRestaurant( $order )
	{
		try {
			 
			// $deliveryAddress = '';
			// $address = UserAddress::select('*')->Where('user_address.id', $order['delivery_address_id'])->first();

			// if ($address->first_address) $deliveryAddress = $address->first_address;
			// else if ($address->second_address) $deliveryAddress = $address->second_address;

			//   $restaurant = Restaurent::find($order['restaurant_id']);
			  


			// $restaurantAddress = '';
			// if ($restaurant) $restaurantAddress = $restaurant->name . ', ' . $restaurant->street;

			$restaurantName = Restaurent::where('id','=',$order['restaurant_id'])->first();

			StaticPushNotify::$driverMessages = [
				'title'				=> 'Order Confirmed successfully .',
				//'title'				=> 'Order Confirmed by Restaurant',
				//'message' 			=> 'Pickup from ' . $restaurantAddress . ' and drop at '. $deliveryAddress .'.',
				'body' 			=> 'Your Order with id '.$order['order_number'].' is confirmed by Restaurant.',
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

	public static function prepareNotificationMessageForRestaurant2( $order )
	{
		try {
			 
			// $deliveryAddress = '';
			// $address = UserAddress::select('*')->Where('user_address.id', $order['delivery_address_id'])->first();

			// if ($address->first_address) $deliveryAddress = $address->first_address;
			// else if ($address->second_address) $deliveryAddress = $address->second_address;

			//   $restaurant = Restaurent::find($order['restaurant_id']);
			  


			// $restaurantAddress = '';
			// if ($restaurant) $restaurantAddress = $restaurant->name . ', ' . $restaurant->street;

			$restaurantName = Restaurent::where('id','=',$order['restaurant_id'])->first();

			StaticPushNotify::$driverMessages = [
				'title'				=> 'Order Confirmed by Restaurant.',
				//'message' 			=> 'Pickup from ' . $restaurantAddress . ' and drop at '. $deliveryAddress .'.',
				//'body' 			=> 'order no is' .$order['id']. 'and  restaurant name is' .$restaurantName['name'].'.',
				'body' 			=> 'Order '.$order['order_number'].' is confirmed',
				'subtitle'			=> 'Order Confirmed by Restaurant.',
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
				'title'				=> 'Order Dispatched from Restaurant',
				//'message' 			=> 'Pickup from ' . $restaurantAddress . ' and drop at '. $deliveryAddress .'.',
				'body' 			=> 'Mr.'.$driverdetail['first_name'].' '.$driverdetail['last_name']. ' picked your order '.$order['order_number'].' from the restaurant.',
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

	public static function prepareNotificationMessageForPickupForRestaurant( $order )
	{
		try {
		    
		  //   $data = Order::where('order_number','=',$order['order_number'])->first();
			 //$driverid = $data['driver_id'];

			 //$driverdetail = User::where('id','=',$driverid)->first();
			 
			 
			 //echo $driverdetail['first_name'];
			 //die;
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
				'title'				=> 'Order is dispatched',
				//'message' 			=> 'Pickup from ' . $restaurantAddress . ' and drop at '. $deliveryAddress .'.',
				//'body' 			=> 'Mr. '.$driverdetail['first_name'].' '.$driverdetail['last_name']. ' picked your order '.$order['order_number'].' from the restaurant.',
				'body' 			=> 'Order '.$order['order_number'].' is dispatched.',
				'subtitle'			=> 'Order is dispatched',
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
				'body' 			=> 'Your order '.$order['order_number'].' has been successfully delivered.',
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

	public static function prepareNotificationMessageForDeliveredForRestaurant( $order )
	{
		try {
		    
		    StaticPushNotify::$driverMessages = [
				//'title'				=> 'Order is delivered.',
				'title'				=> 'Order is Delivered',
				//'message' 			=> 'Pickup from ' . $restaurantAddress . ' and drop at '. $deliveryAddress .'.',
				'body' 			=> 'Order '.$order['order_number'].' is delivered.',
				'subtitle'			=> 'Order is Delivered',
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

	public static function prepareNotificationMessageForDriverAcceptedForRestaurant( $order )
	{
		try {
			 // $orderNo = $order->order_number;
		     
		     
		  //    $data = Order::where('order_number','=',$orderNo)->first();
			 // $driverid = $data['driver_id'];

			 // $driverdetail = User::where('id','=',$driverid)->first();
			$driverid = $order['driver_id'];
			$driverdetail = User::where('id','=',$driverid)->first();

			StaticPushNotify::$driverMessages = [
				//'title'				=> 'Order Driver Assign.',
				//'title'				=> 'Delivery Man Assigned',
				'title'				=> 'Order is accepted by driver',
				//'message' 			=> 'Pickup from ' . $restaurantAddress . ' and drop at '. $deliveryAddress .'.',
				//'body' 			=>  'Mr. '.$driverdetail['first_name'].' '.$driverdetail['last_name'].' is the  for order '.$order['order_number'].'.',
				'body' 	    =>  'Order is '.$order['order_number']. ' is accepted by Mr. '.$driverdetail['first_name'].' '.$driverdetail['last_name'].'.',
				//'body' 			=>  'Order '.$order['order_number']. ' is accepted by driver.',
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


	public static function prepareNotificationMessageForCancelledForRestaurant( $order )
	{
		try {
		  //    $orderNo = $order->order_number;
			 // $data = Order::where('order_number','=',$orderNo)->first();
			 // $userid = $data['user_id'];

			 // $userdetail = User::where('id','=',$userid)->first();
			// $userid = $order['user_id'];
			// $userdetail = User::where('id','=',$userid)->first();

			$restaurant_detail = Restaurent::where('id','=',$order['restaurant_id'])->first();


			StaticPushNotify::$driverMessages = [
				//'title'				=> 'Order Driver Assign.',
				'title'				=> 'Order Cancelled',
				//'message' 			=> 'Pickup from ' . $restaurantAddress . ' and drop at '. $deliveryAddress .'.',
				//'body' 			=>  'Order '.$order['order_number'].' is cancelled.',
				'body'			=>  'Order '.$order['order_number'].' is cancelled by '.$restaurant_detail['name'].'.',
				'subtitle'			=> 'Order Cancelled',
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

	public static function prepareNotificationMessageForCancelledByRestaurantToUser( $order )
	{
		try {
		  //    $orderNo = $order->order_number;
			 // $data = Order::where('order_number','=',$orderNo)->first();
			 // $userid = $data['user_id'];

			 // $userdetail = User::where('id','=',$userid)->first();
			// $userid = $order['user_id'];
			// $userdetail = User::where('id','=',$userid)->first();

			$restaurant_detail = Restaurent::where('id','=',$order['restaurant_id'])->first();


			StaticPushNotify::$driverMessages = [
				//'title'				=> 'Order Driver Assign.',
				'title'				=> 'Order Cancelled',
				//'message' 			=> 'Pickup from ' . $restaurantAddress . ' and drop at '. $deliveryAddress .'.',
				//'body' 			=>  'Order '.$order['order_number'].' is cancelled.',
				'body'			=>  'Order '.$order['order_number'].' is cancelled by '.$restaurant_detail['name'].'.',
				'subtitle'			=> 'Order Cancelled',
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


	public static function prepareNotificationMessageForUserNewOrderForRestaurant( $order )
	{
		try {
		  //  $orderNo = $order->order_number;
			 //$data = Order::where('order_number','=',$orderNo)->first();
			 //$userid = $data['user_id'];

			 //$driverdetail = User::where('id','=',$userid)->first();
            
		  //  $orderNo = $order->order_number;
			 //$data = Order::where('order_number','=',$orderNo)->first();
			 //$userid = $data['user_id'];
			 //$userdetail = User::where('id','=',$userid)->first();
			$userid = $order['user_id'];
			$userdetail = User::where('id','=',$userid)->first();

			StaticPushNotify::$driverMessages = [
				//'title'				=> 'Order Driver Assign.',
				'title'				=> 'New Order Received',
				//'message' 			=> 'Pickup from ' . $restaurantAddress . ' and drop at '. $deliveryAddress .'.',
				//'body' 			=>  'Order '.$order['order_number'].' received',
			    'body' 		=>  'New order received from Mr. '.$userdetail['first_name'].' '.$userdetail['last_name'].'.',
				'subtitle'			=> 'New Order Received',
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