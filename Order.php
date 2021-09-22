<?php namespace App;
use Illuminate\Database\Eloquent\Model;
use DB, Exception, Config;
use App\Cart, Lang, Auth, App\UserAddress, App\OrderItem, App\Driver, App\Coupon, App\Restaurent;
use Illuminate\Database\Eloquent\SoftDeletes;
class Order extends AppModel
{
	protected $table = 'order';
	
	protected $primaryKey = 'id';
	
	public $timestamps = true;

	public $fillable = ['id', 'date', 'time', 'asap', 'user_id','driver_id', 'amount', 'discount', 'order_type', 'shipping_charge', 'order_number', 'coupon_id', 'status', 'remark', 'created_at', 'updated_at', 'restaurant_id'];
    use SoftDeletes;
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
		if(count($cart->getData())){
			$order['user_id'] = $user->id;				
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
			if($idforaddress!= "")
				self::setCustomerAdderss($idforaddress,$orderid);
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
		$order->ship_long	= $customer->longitude	;
		$order->ship_city 	= $cities->name;
		$order->ship_zip 	= $customer->zip;
		$order->ship_mobile = $user->contact_number;
		$order->save();	
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
	/*
     * Added by sandeep singh
     * Date : 17-5-17
     * save order data from api
   "delivery_type":"is_pickup",
    "Delivery_address_id":"2",
    "Sub_total_before_discount":123,
    "Discount":11,
    "Sub_total":112,
    "Delivery_fee":10,
    "Total":122,
     "Coupancode":"off10"     
	*/	
	public static function createApiOrder($requestArray)
	{	
		$order = array();
		$detail = array();
		$addons = array();		
		$order_number = 0;
		$request = $requestArray;	

		if(count($request['order']['item'])){
			$order['user_id'] = $request['user_id'];				
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
			while(true){
				$order_number = (intval(floor(microtime(true))));	
				$orderNumber  = Order::where('order_number',$order_number)->first();
				if(count($orderNumber)<=0)
					break;
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
			//prd($order);		
			$orderid = Order::insertGetId($order);			
			//$orderid = 10000;
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
					//prd($orderitems);
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
			
			$order->amount = $request['order']['total'];
			$order->created_at = date('Y-m-d');
			$order->save();

			return true;

		} catch( Exception $e ) {
			return $e->getMessage();
		}								
	}
}
