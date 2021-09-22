<?php 

namespace App;

use Illuminate\Database\Eloquent\Model;
use DB, Exception, Config;
use App\Cart, Lang, Auth, App\UserAddress, App\OrderItem, App\Driver, App\Coupon, App\Restaurent;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\StaticPushNotify;
use App\User;

class Order extends AppModel
{
	use SoftDeletes, StaticPushNotify;

	protected $table = 'order';
	
	protected $primaryKey = 'id';
	
	public $timestamps = true;

	public $fillable = ['id', 'date', 'time', 'asap', 'user_id','driver_id', 'amount', 'discount', 'order_type', 'shipping_charge', 'order_number', 'coupon_id', 'status', 'remark', 'created_at', 'updated_at', 'restaurant_id', 'packaging_fees', 'cgst', 'sgst', 'user_payment_method'];
    
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

	public function products(){			
		return $this->belongsToMany('App\Product','order_item','order_id','product_id')
		->withPivot('quantity', 'price');
	}

	public function items(){
		return $this->hasMany('App\OrderItem','order_id','id');
	}

	/*
	* Written by sandeep singh
	* Date : 23-5-17
	* relation with restaurant
	*/
	public function restaurant(){
		return $this->belongsTo('App\Restaurent','restaurant_id','id')->with('image')->with('area')->with('city')->with('state')->with('country');
	}
	
	public static function createOrder($requestArray, $cart)
	{	
		$order = array();
		$detail = array();
		$addons = array();
		$user = Auth::user();
		$order_number = 0;
		$request = $requestArray;
		$idforaddress='';

		$customerDataId = null;
		if (isset($requestArray['search_customer'])) {
			$customerContact = substr($requestArray['search_customer'],-10);
			$customerData = User::select('user.id','user.first_name','user.last_name','user.contact_number')
			->Where('user.contact_number', 'like', '%' . trim($customerContact) . '%')->first();
		
			$customerDataId = $customerData ? $customerData->id : null;
		}

		if(count($cart->getData())){

			$order['user_payment_method'] = 'WebCOD';
			$order['user_id'] = $customerDataId ? $customerDataId : $user->id;
			$order['amount'] = $cart->getTotal($request['order_type']);

			if(isset($request['order_type']) && $request['order_type'] == 'pickup'){
				$order['shipping_charge'] = 0;
			}else{
				$order['shipping_charge'] = $cart->deliveryCharges();
			}	

			$order['status'] = \Config::get('constants.order.noconfirm');
			if(array_key_exists('search_customer', $request))		// backend request
			{
				$order['restaurant_id'] = $request['restaurant_id'];
				$order['order_type'] = $request['order_type'];
				$order['remark'] = trim($request['remark']);

				if(isset($request['asap']) && $request['asap'] == 'soon'){
					$order['asap'] = 1;	
				}else{
					$order['asap'] = 0;
				}
				if(!empty($request['radiog_lite']))
					$idforaddress=$request['radiog_lite'];
				if(!empty($request['coupancode']))
				{
					$coupandetails = Coupon::where(['coupon_code'=>$request['coupancode']])->first();
					$order["coupon_details"]	= '';
					if(empty($coupandetails))
						$coupandetails=array();
					$order["coupon_details"]	= serialize($coupandetails);
				}
			}			
			else{
				foreach($request as $key=>$requestData){					
					if(!in_array($key, array('itemaddond','_token','coupan_id'))){
						if($key=='asap')
						{
							if($requestData=='soon')
								$requestData=1;
							else
								$requestData=0;
						}
						if($key =='coupon_id')
						{
								$coupandetails = Coupon::where(['id'=>$requestData])->first();
								$key	=	"coupon_details";
								if(empty($coupandetails))
									$coupandetails=array();
								$requestData	=	serialize($coupandetails);
						}
						$order[$key] = $requestData;							
					}							
				}
			}							
			while(true){
				$order_number = (intval(floor(microtime(true))));	
				$orderNumber	= Order::where('order_number',$order_number)->first();
				if(count($orderNumber)<=0)
					break;
			}
			$order['order_number'] = $order_number;
			$order['created_at'] = date('Y-m-d');
			if(isset($request['orderdatetime']) && !empty($request['orderdatetime'])){
				$orderdatetime = $request['orderdatetime'];
			}else{
				$orderdatetime = date('Y-m-d H:i:s');
			}
			$dt = new \DateTime($orderdatetime);
			$order['date'] = $dt->format('Y-m-d');
			$order['time'] = $dt->format('H:i:s');	

			$orderid = Order::insertGetId($order);

			if($orderid > 0){
				foreach($cart->getData() as $key => $orderitemdata){
					if(!in_array($key, array('other')))
					{
						$orderitems = array();						
						$orderitems[$key]['order_id'] = $orderid;					
						$orderitems[$key]['product_id'] = $orderitemdata['prodid'];
						$orderitems[$key]['product_name'] = $orderitemdata['name'];
						if(count($orderitemdata['itemaddond']) >0)						
							$orderitems[$key]['addons_list'] = self::getaddonsdetails($orderitemdata['itemaddond'],$orderitemdata['prodid']);
						$orderitems[$key]['description'] = $orderitemdata['description'];
						$orderitems[$key]['product_quantity'] = $orderitemdata['quantity'];
						$orderitems[$key]['product_unit_price'] = $orderitemdata['cost'];
						$orderitems[$key]['product_total_price'] = $orderitemdata['totalCost'];
						DB::table('order_item')->insert($orderitems);					
					}
				}					
			}

			if($idforaddress!= "") self::setCustomerAdderss($idforaddress,$orderid);

			return $order_number;										
		}			
		return false;										
	}

	public static function setCustomerAdderss($useraddressid,$orderid)
	{
		$customer = UserAddress::select('*')->Where('user_address.id', $useraddressid)->first();
 		$cities = City::select('name')->where('id',$customer->city_id)->first();
 		$area = Area::select('name')->where('id',$customer->area_id)->first();
 		$user=User::select('contact_number')->where('id',$customer->user_id)->first();					

		$order				= Order::find($orderid);
		$order->status 		= \Config::get('constants.order.pending');	
		$order->ship_add1 	= $customer->first_address;
		$order->ship_add2 	= $customer->second_address;
		$order->ship_lat 	= $customer->latitude;
		$order->ship_long	= $customer->longitude;
		$order->ship_city 	= $cities->name;
		$order->ship_zip 	= $customer->zip;
		$order->ship_mobile = $user->contact_number;
		$order->ship_json = json_encode($customer->toArray());
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
		$order = self::where('order_number',$ordernumber)->first();
		return $this->getorderfulldetails($order->id);
	}

	public static function getorderfulldetails($orderid)
	{
		$ArrayReturn= array();
		$ArrayReturn['order']= $ArrayReturn['Driver']=$ArrayReturn['orderItems']=array();
		$ArrayReturn['order']= self::where('id',$orderid)->first();
		$ArrayReturn['Driver']= Driver::where('id',$ArrayReturn['order']->driver_id)->first();
		$ArrayReturn['orderItems']=(new OrderItem())->orderitemlist($orderid);
		return $ArrayReturn;
	}

	public static function deleteOrder($orderid)
	{
		$order		= self::where('id',$orderid)->first();
		$orderItems	=(new OrderItem())->orderitemlist($orderid);
		$message	= 'Order number :'.$order->number." successfully deleted";
		$order->delete();
		$orderItems->delete();
		return $message;
	}
	

	public static function createApiOrder($requestArray)
	{	
		$order = array();
		$detail = array();
		$addons = array();		
		$order_number = 0;
		$request = $requestArray;	

		if(count($request['order']['item']))
		{
			$order['user_payment_method'] = $request['order']['user_payment_method'];
			$order['user_id'] = $request['user_id'];
			$order['sgst'] = $request['order']['sgst'];
			$order['cgst'] = $request['order']['cgst'];
			$order['packaging_fees'] = $request['order']['packaging_fees'];
			$order['amount'] = $request['order']['total'];

			if(isset($request['order']['delivery_type']) && $request['order']['delivery_type'] == 'is_pickup'){
				$order['shipping_charge'] = 0;
			}else{
				$order['shipping_charge'] = $request['order']['delivery_fee'];
			}	
											
			$order['status'] = \Config::get('constants.order.pending');	
			$order['restaurant_id'] = $request['order']['restaurant_id'];
			$order['order_type'] = $request['order']['delivery_type'];
			if(isset($request['order']['datetime']) && !empty($request['order']['datetime'])){
				$orderdatetime = $request['order']['datetime'];
			}else{
				$orderdatetime = date('Y-m-d H:i:s');
			}
			$dt = new \DateTime($orderdatetime);
			$order['date'] = $dt->format('Y-m-d');
			$order['time'] = $dt->format('H:i:s');
						
			if(!empty($request['order']['coupancode']))
			{
				$order["coupon_details"]	= $request['order']['coupancode'];
			}
			
			while(true)
			{
				$order_number = (intval(floor(microtime(true))));	
				$orderNumber  = Order::where('order_number',$order_number)->first();
				if( count( $orderNumber ) <= 0 ) {
					break;
				}
			}

			$order['order_number'] = $order_number;
			$order['created_at'] = date('Y-m-d');
			// shiping address
			if(isset($request['order']['delivery_address_id'])){
				$delivery_address_id=$request['order']['delivery_address_id'];
				$customer = UserAddress::select('*')->Where('user_address.id', $delivery_address_id)->first();
				
				if(!empty($customer)){
					$order['ship_json'] = json_encode($customer->toArray());
				}				
			}			

			$orderid = Order::insertGetId($order);

			if (isset($request['order']['delivery_address_id'])) {
				// Send push notification to driver from here for admin...
				$order['delivery_address_id'] = $request['order']['delivery_address_id'];

				if ($orderid) self::sendOrderNotification($order, $orderid);
			}


			if($orderid > 0){
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
		try
		{
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
			if( count( $request['order']['item'] ) == 0 ) {
				throw new Exception('Add one or more items in cart to place an order.', 1);
			}

			if( isset( $request['order']['delivery_type'] ) && $request['order']['delivery_type'] == 'is_pickup' ) {
				$order->shipping_charge = 0;
			} else {
				$order->shipping_charge = $request['order']['delivery_fee'];
			}
			
			$order->restaurant_id = $request['order']['restaurant_id'];
			$order->order_type = $request['order']['delivery_type'];

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
								
				if(count($orderitemdata['addons']) >0) {
					$orderitems[$key]['addons_list'] = serialize(($orderitemdata['addons']));
				}

				$orderitems[$key]['product_quantity'] = $orderitemdata['product_quantity'];
				$orderitems[$key]['product_unit_price'] = $orderitemdata['product_unit_price'];
				$orderitems[$key]['product_total_price'] = $orderitemdata['product_total_price'];
			}

			DB::table('order_item')->insert($orderitems);
			
			$order->sgst = $request['order']['sgst'];
			$order->cgst = $request['order']['cgst'];
			$order->packaging_fees = $request['order']['packaging_fees'];
			$order->amount = $request['order']['total'];
			$order->created_at = date('Y-m-d');
			$order->user_payment_method = isset($request['order']['user_payment_method']) ? $request['order']['user_payment_method'] : NULL;
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
		try 
		{
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
	    	StaticPushNotify::setAndroidApiAccessKey('AAAAO2SkPtU:APA91bFnA_zOOkumy8WnumyO-NSdcW2cspg9LI8-P0CYwCZ8b5ro8r7oWnWC8S9OVxpETHs9WCwxCD3IU5w3B4FKn4M1vi2mG4-XMab_OJ4364cm90L9holenJUyvMmFTFL-S538qvqB');

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


	public static function prepareNotificationMessage( $order )
	{
		try 
		{
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
}
