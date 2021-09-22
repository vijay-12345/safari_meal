<?php 
namespace App\Http\Controllers\Api;

use DB, Request, Config, Exception;
use App\Restaurent,App\helpers;
use App\Area, App\Product, App\OptionItem, App\Order, App\User;
use App\CartDb;
use App\Setting;

class CartController extends \App\Http\Controllers\Controller
{
	protected $handler;
	
	public function __construct($handler = 'App\CartDb') {	
		$this->handler = new $handler();
	}
	
	
	public function addToCart()
	{
		try {
			$inputs = Request::all();		
			$restaurantproduct = Product::lang()->where('id',$inputs['prodid'])->first();
			//$restaurantproduct = Product::where('id',$inputs['prodid'])->first();
			// print_r(json_encode($restaurantproduct));
			// die;	
			
			$cart = array();
			if(!(empty($inputs['cart_token']))) {
				$cart = $this->handler->read($inputs);	
			}
			
			if($restaurantproduct == null){
				return $this->apiResponse(['data'=>$cart,'message'=>'Product Not Found']);		
			}

			$count = empty($cart) ? 0: count($cart);
			
			foreach($cart as $key=>$val) {
				if($val['prodid'] == $inputs['prodid']) {
					$count = $key;	
				}
			}

			if($restaurantproduct && $inputs['quantity'] > 0)
			{
				$cart[$count]['prodid'] = $inputs['prodid'];
				$cart[$count]['name'] = $restaurantproduct['name'];
				$cart[$count]['quantity'] = $inputs['quantity'];				
				$cart[$count]['product_type'] = $restaurantproduct['product_type'];				
				$cart[$count]['restaurant_id'] = $restaurantproduct['restaurant_id'];				
				$cart[$count]['description'] = $restaurantproduct['description'];				
				$cart[$count]['cost'] = $restaurantproduct['cost'];
				$cart[$count]['addons'] = array();
				if(!empty($inputs['addons'])) {
					foreach ($inputs['addons'] as $key=>$val)
					{
						try {
							$OptionItem	 = OptionItem::where('id',$val)->first();
							$ProductOptions	= DB::table('product_options')->where(['product_id'=> $inputs['prodid'], 'option_item_id'=> $OptionItem->id])->first();
							$cart[$count]['addons'][$key]['id']			=	$val;
							$cart[$count]['addons'][$key]['name']		=	$OptionItem->item_name;
							$cart[$count]['addons'][$key]['quantity']	=	$inputs['quantity'];
							$cart[$count]['addons'][$key]['cost']		=	$ProductOptions->price;			
						}
						catch(Exception $e) {
							return $this->apiResponse(['error'=>'Addon\'s details not found'],true);
						}
					}
				}
				$this->data	= $this->handler->write($cart,$inputs);
				return $this->apiResponse(['data'=>$this->data]);
			}		
		} catch(Exception $e) {
			return $this->apiResponse(['error'=>$e->getMessage()],true);
		}
	}


	public function getCart()
	{
		try {
			$inputs = Request::all();
			if( empty( $inputs['userid'] ) && empty( $inputs['cart_token'] ) ) {
				return $this->apiResponse(['error' => 'Please enter userid or cart token to get cart details'], true);		
			}
			$this->data	= $this->handler->read($inputs);
			return $this->apiResponse(['data'=>$this->data]);
		} catch( Exception $e ) {			
			return $this->apiResponse(['error'=>$e->getMessage()],true);
		}
	}


	public function clear(){		
		try {
			$inputs = Request::all();		
			if(empty($inputs['userid']) && empty($inputs['cart_token']))
				return $this->apiResponse(['error'=>'Please enter userid or cart token to get cart details'],true);		
			$this->handler->clearCart($inputs);
			return $this->apiResponse(['data'=>'Cart has been successfully empty']);
		} catch(Exception $e) {
			return $this->apiResponse(['error'=>$e->getMessage()],true);
		}
	}

	
	// Place new or update existing order
	public function addOrder() {

		try {
			$inputs = Request::all();
			
			if( isset($inputs['order_id']) && $inputs['order_id']) {
				$response = Order::updateApiOrder( $inputs );
				if( $response === true ) {
					$ordernum = $inputs['order_id'];
				} else {
					throw new Exception($response, 1);
				}
			} else {
				
				$ordernum = Order::createApiOrder($inputs);
				
				//notify driver
				// $ordernum = 1564996258;
				$this->notifyDriver($ordernum);
				//notify user vijayanand
				$this->notifyUser($ordernum);
        		$getOrder = Order::where('order_number', $ordernum)->first();   //sloved first
        		//notify restaurant
				$dataReturn = Order::notifyUserAboutOrderStatusUserNewOrderForRestaurant($getOrder->toArray(), $getOrder->id);

			}
			
			$order = Order::where('order_number', $ordernum)->with('items')->with('customer')->with('driver')->with('restaurant')->first();
			
			if(!empty($order)) $order = $order->toArray();
			
			$adminData = User::select('first_name','last_name','contact_number','email')
				->where('role_id', Config::get('constants.user.super_admin'))
				->get();
			
			$data = array_merge($order, array(
				'order_status_type' => Config::get('constants.order_status_label.weborapp'),
				'message' => trans('admin.successfully.added'),
				'order_number' => (string) $ordernum,
				'admin' => $adminData
			));

			//$returnObject = $this->apiResponse(["data" => $data]);
			return $this->apiResponse(["data" => $data]);


			// print_r($returnObject);
			// die;

			//$this->notifyUser($returnObject);
			// print_r($ordernum);
			// die;

			// print_r($data);
			// die;
			// return $this->apiResponse(["data" => $data]);

		} catch(Exception $e) {
			return $this->apiResponse(['error' => $e->getMessage()], true);
		}
	}


	/**
	*	Send notification to driver
	**/
	// public function notifyDriver( $orderNo )
	// {
	// 	$order = Order::where('order_number', $orderNo)->first();
		
		


	// 	$restaurant = Restaurent::where('id', $order->restaurant_id)->first();
	// 	// print_r($restaurant);
	// 	// die;
	// 	$getOrder = Order::where('order_number', $orderNo)->first();
	// 	$dataReturn = Order::sendOrderNotification($getOrder->toArray(), $getOrder->id);


		
		

	// 	// if($restaurant->confirm_order == 1) return false;


	// 	// $orderStatus = 1;
	// 	// $setting     = Setting::getSetting();

	// 	// if($setting) $orderStatus = $setting->order_status;
		
	// 	// if($restaurant->confirm_order == 2 && $orderStatus == 2 && $order->order_type == 'delivery') {
	// 	// 	//update order status for drivers
	// 	// 	Order::where('order_number', $orderNo)->update(['status' => 2]);

	// 	// 	$getOrder = Order::where('order_number', $orderNo)->first();
	// 	//     $tempId   = json_decode($getOrder->ship_json);
	// 	//     $getOrder->delivery_address_id = $tempId->id;
	    	
	// 	//     // Notify driver
	// 	//     if ($getOrder) $dataReturn = Order::sendOrderNotification($getOrder->toArray(), $getOrder->id);
		    
	// 	// } else {
	// 	// 	//update order status for confirmed
	// 	// 	Order::where('order_number', $orderNo)->update(['status' => 7]);
			
	// 	// }

	// 	return true;
 //    }



 //    public function notifyDriver( $orderNo )
	// {

	// 	$order = Order::where('order_number', $orderNo)->first();

	// 	$restaurant = Restaurent::where('id', $order->restaurant_id)->first();
	// 	$getOrder = Order::where('order_number', $orderNo)->first();
	// 	$dataReturn = Order::sendOrderNotificationForUser($getOrder->toArray(), $getOrder->id);

	// 	// print_r($restaurant);
	// 	// die;

	// 	if($restaurant->confirm_order == 1) return false;

	// 	$orderStatus = 1;
	// 	$setting     = Setting::getSetting();

	// 	if($setting) $orderStatus = $setting->order_status;
		
	// 	if($restaurant->confirm_order == 2 && $orderStatus == 2 && $order->order_type == 'delivery') {
	// 		//update order status for drivers
	// 		Order::where('order_number', $orderNo)->update(['status' => 2]);

	// 		$getOrder = Order::where('order_number', $orderNo)->first();
	// 	    $tempId   = json_decode($getOrder->ship_json);
	// 	    $getOrder->delivery_address_id = $tempId->id;
	    	
	// 	    // Notify driver
		   

	// 	    if ($getOrder) $dataReturn = Order::sendOrderNotification($getOrder->toArray(), $getOrder->id);

	// 	    //if ($getOrder) {$dataReturn = Order::sendOrderNotificationForUser($getOrder->toArray(), $getOrder->id);
		    	
		    


		    
	// 	} else {
	// 		//update order status for confirmed
	// 		Order::where('order_number', $orderNo)->update(['status' => 7]);
			
	// 		.

	// 	}

	// 	return true;
 //    }


    	public function notifyDriver( $orderNo )
	{
		$order = Order::where('order_number', $orderNo)->first();

		$restaurant = Restaurent::where('id', $order->restaurant_id)->first();

		if($restaurant->confirm_order == 1) return false;

		$orderStatus = 1;
		$setting     = Setting::getSetting();
    
		if($setting) $orderStatus = $setting->order_status;
		
		if($restaurant->confirm_order == 2 && $orderStatus == 2 && $order->order_type == 'delivery') {
			//update order status for drivers
			Order::where('order_number', $orderNo)->update(['status' => 2]);

			$getOrder = Order::where('order_number', $orderNo)->first();
		    $tempId   = json_decode($getOrder->ship_json);
		    $getOrder->delivery_address_id = $tempId->id;
	    	
		    // Notify driver
		    if ($getOrder) $dataReturn = Order::sendOrderNotification($getOrder->toArray(), $getOrder->id);
		    
		} else {
			//update order status for confirmed
			Order::where('order_number', $orderNo)->update(['status' => 7]);
			
		}

		return true;
    }


    public function notifyUser( $orderNo )
	{

		//$order = Order::where('order_number', $orderNo)->first();
		$getOrder = Order::where('order_number', $orderNo)->first();
		if ($getOrder) $dataReturn = Order::sendOrderNotificationForUser($getOrder->toArray(), $getOrder->id);
		return true;
		
// 		 echo "good";
// 		die;
//         echo "good morning";
// 		die;
//		if ($getOrder) $dataReturn = Order::sendOrderNotificationForUser($getOrder->toArray(), $getOrder->id);
// 		return true;
		
	
		
// 		echo "good moring1";
// 		die;
// 		echo "good";
// 		die;
		

// 		$restaurant = Restaurent::where('id', $order->restaurant_id)->first();
// 		// print_r($restaurant);
// 		// die;

// 		if($restaurant->confirm_order == 1) return false;

// 		$orderStatus = 1;
// 		$setting     = Setting::getSetting();

// 		if($setting) $orderStatus = $setting->order_status;
		
// 		if($restaurant->confirm_order == 2 && $orderStatus == 2 && $order->order_type == 'delivery') {
// 			//update order status for drivers
// 			Order::where('order_number', $orderNo)->update(['status' => 2]);

// 			$getOrder = Order::where('order_number', $orderNo)->first();
// 		    $tempId   = json_decode($getOrder->ship_json);
// 		    $getOrder->delivery_address_id = $tempId->id;
	    	
// 		    // Notify driver
		    
// 		    //if ($getOrder) $dataReturn = Order::sendOrderNotification($getOrder->toArray(), $getOrder->id);

// 		    if ($getOrder) $dataReturn = Order::sendOrderNotificationForUser($getOrder->toArray(), $getOrder->id);


		    
// 		} else {
// 			//update order status for confirmed
// 			Order::where('order_number', $orderNo)->update(['status' => 7]);
			
// 		}

// 		return true;

    }


	// Cancel Order
	public function cancelOrder()
	{
		try {
			$params = Request::all();
			// Check if user exists
			$user = User::find( $params['user_id'] );
			if( ! $user ) throw new Exception('User does not exist.', 1);
			

			// Check if order exists
			$order = Order::where('order_number', $params['order_id'])->first();
			if( ! $order ) throw new Exception('Order does not exist.', 1);
			
			// Check if user is owner of order
			if( $order->user_id != $params['user_id'] ) {
				throw new Exception('You are not permittted to cancel this order.', 1);
			}

			// Check if order is already cancelled
			if( $order->status == Config::get('constants.order.cancelled') ) {
				throw new Exception('Order is already cancelled..', 1);
			}

			// Check if order is already cancelled
			if( $order->status > (int) Config::get('constants.order.pending') ) {
				throw new Exception('Order can not be cancelled.', 1);
			}

			// Finally, Cancel order
			$order->status = Config::get('constants.order.cancelled');
			$order->save();

			//notify restaurant
			$getOrder = $order;
			if ($getOrder) $dataReturn = Order::notifyUserAboutOrderStatusCancelledForRestaurant($getOrder->toArray(), $getOrder->id);

			// Send success response
			return $this->apiResponse();

		} catch(Exception $e) {
			return $this->apiResponse(['error' => $e->getMessage()], true);	
		}
	}

}