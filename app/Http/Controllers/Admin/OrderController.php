<?php

namespace App\Http\Controllers\Admin;
use Auth;
use Session;
use DB;
use \Exception;
use Illuminate\Http\Request;
use App\Restaurent;
use App\User;
use App\Driver;
use App\Order;
use App\Setting;
use App\Cart, App\Coupon;
use Illuminate\Pagination\Paginator;

class OrderController extends \App\Http\Controllers\Controller {

	/*
	|--------------------------------------------------------------------------
	| Welcome Controller
	|--------------------------------------------------------------------------
	|
	| This controller renders the "marketing page" for the application and
	| is configured to only allow guests. Like most of the other sample
	| controllers, you are free to modify or remove it as you desire.
	|
	*/

	/**
	 * Create a new controller instance.
	 *
	 * @return void
	 */
	public function __construct()
	{
		$this->middleware('auth');
	}

	/**
	 * Show the application welcome screen to the user.
	 *
	 * @return Response
	 */
	public function index(Request $request)
	{
		//clear session first time	
		if ($request->isMethod('get') && count($request->all()) == 0){	
			Session::forget('filter');			
		}

		/* 
			filter
		*/
		if($request->has('sorting')) {
			$filter = ['sort'=>$request->input('sorting'),'field'=>$request->input('field')];
		} else {
			$filter = ['sort'=>'desc','field'=>'order_number'];
		}
		$filter['paginate_sort'] = $filter['sort'];
		
		if( $request->has('status') ) {
			session(['filter.status' => $request->input('status')]);
		} else {
			session(['filter.status' => '']);
		}

		if($request->has('paginate_limit')) {
			session(['filter.paginate_limit' => $request->input('paginate_limit')]);
			session(['filter.restaurant' => $request->input('restaurant')]);			
			session(['filter.search' => trim($request->input('search'))]);
			//handle when blank/space inpute in search
			if($request->input('search'))
				if(session('filter.search')==null)
				{
					$data = null;
					return view('admin.order.index',['data'=>$data,'filter'=>$filter]);
				}
		} else if(!Session::has('filter.paginate_limit')) {
			session(['filter.paginate_limit' => 10]);
		}

		if($request->has('date')) {
			session(['filter.date' => $request->input('date')]);			
		} else if(!Session::has('filter.date')){
			session(['filter.date' => date('Y-m-d')]);
		}

		if($request->has('custom_date')) {
			session(['filter.custom_date' => $request->input('custom_date')]);	
			session(['filter.date' => 'all']);		
		} else {
			session(['filter.custom_date' => '']);	
		}
		
		/*
		* fetch data
		*/
		$data = [];
		//end of chart
		$order_q = Order::where('order.status','!=',\Config::get('constants.order.noconfirm'))
			->leftjoin('restaurant', 'order.restaurant_id', '=', 'restaurant.id')
			->leftjoin('user as driver', 'order.driver_id', '=', 'driver.id')
			->leftjoin('user', 'order.user_id', '=', 'user.id')
			->select('order.*','restaurant.name as restaurant_name',DB::raw('CONCAT(driver.first_name, " ", driver.last_name) AS driver_name'),'user.first_name as customer_first_name','user.last_name as customer_last_name')
		    ->orderBy($filter['field'], $filter['sort']);
	    // pr(session()->all());

		if($filter['field'] == 'date') {
			$order_q = $order_q->orderBy('time', $filter['sort']);
		}
		if(Session::has('filter.restaurant') && session('filter.restaurant') !='') {
			$order_q->where('restaurant.id', session('filter.restaurant'));
		}
		if(Session::has('access.role') && Session::get('access.role') !='admin') {		
			$order_q->whereIn('order.restaurant_id', Session::get('access.restaurant_ids'));
		}

		if( Session::get('filter.status') == 'open' ) {
			$order_q->where('order.status', '!=', \Config::get('constants.order.delivered'));
		}

		if( Session::get('filter.status') == 'closed' ) {
			$order_q->where('order.status', \Config::get('constants.order.delivered'));
		}

		if(Session::has('filter.search') && session('filter.search') !='') {
			$order_q->where('order.order_number', 'like', '%'.session('filter.search'));
			session(['filter.custom_date' => '']);
			session(['filter.date' => 'all']);
		} else {
			if(Session::has('filter.date') && session('filter.date') !='all') {
				$order_q->whereDate('order.date','=', session('filter.date'));
			} else if(Session::has('filter.custom_date') && session('filter.custom_date') !='') {
				$order_q->whereDate('order.date','=', session('filter.custom_date'));
			}
		}
		// print_r($order_q->toSql());exit;
		$data['data'] = $order_q->whereNull('order.deleted_at')->paginate(session('filter.paginate_limit'));
		//---------------------End of fetch data--------------------------
		
		if($filter['sort'] == 'asc') $filter['sort'] = 'desc';
		
		else $filter['sort'] = 'asc';
		
        if($request->ajax()) {
            $viewHtml =  view('admin.order.ajaxorder',['data'=>$data,'filter'=>$filter]);
        	echo $viewHtml;die;
        } else {
        	return view('admin.order.index',['data'=>$data,'filter'=>$filter]);
        }	
	}
	
	
	public function add(Request $request)
	{
		try {
			// echo "good morning";
			// die;
			$data = [];
			$cart = new Cart;
			$restaurantIdsHaveProduct = Restaurent::getRestaurantIdsHaveProduct();

			if(Session::get('access.role') != 'admin') {			
				$data['restaurant_data'] = Restaurent::whereIn('id',Session::get('access.restaurant_ids'))->where('status',1)->whereIn('id',$restaurantIdsHaveProduct)->lists('name','id');
			} else {
				$data['restaurant_data'] = Restaurent::where('status',1)->whereIn('id',$restaurantIdsHaveProduct)->lists('name','id');
			}
			// prd($data['restaurant_data']);
			
			$input = $request->all();
			
			if($request->isMethod('post')) {
				
				$this->validate($request, ['search_customer' => 'required'
					]);
				// prd($input);
				
				if(!empty($request['coupancode'])) {
					$coupanResponse = Coupon::couponisvalid($request['coupancode']);
					
					if(!is_numeric($coupanResponse)) {
						Session::flash('flash_message', $coupanResponse);				     
						
						$cart->clearcartOthercoupan();
						$cart->clearcart();
						return redirect()->back();
					}
				}
				
				// $cart->setdeliveryCharges($input['check_delivery_charge']);
				// ddd($input);
				
			    $ordernum = Order::createOrder($input, New Cart());
		    	
			    if($ordernum == false) {
					return redirect()->back()->withErrors(trans('Cart don\'t have product. Please add and again try'));;
			    }

			    //notify driver
			    $this->notifyDriver($ordernum);
			    
				Session::put('flash_message', trans('admin.successfully.added')." Order No.:: ".$ordernum);
				
				return redirect()->back();

			} else {
				$cart->clearcart();
			}
		}
		catch(\Exception $e) {
			//Session::flash('flash_message', trans(" Order not created because of ".$e->getMessage()));				     
			return redirect()->back();
			exit;		
		}	

		return view('admin.order.add',['data' => $data]);
	}

	/**
	*	Send notification to driver
	**/
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

	public function edit($id) {

		try {
			$data = [];		
			if(isset($_SERVER['HTTP_REFERER'])) {
				preg_match("/[^\/]+$/", $_SERVER['HTTP_REFERER'], $matches);		
				if($matches[0] =='dashboard') {
					$data['referer'] = $matches[0]; 
				} elseif(Session::has('referer')) {
					$data['referer'] = Session::pull('referer');			
				}
			} elseif(Session::has('referer')) {
				$data['referer'] = Session::pull('referer');			
			}
			
			$restaurantIdsHaveProduct = Restaurent::getRestaurantIdsHaveProduct();
			if(Session::get('access.role') != 'admin') {			
				$data['restaurant_data'] = Restaurent::whereIn('id', Session::get('access.restaurant_ids'))->where('status',1)->whereIn('id',$restaurantIdsHaveProduct)->lists('name','id');
			} else {
				$data['restaurant_data'] = Restaurent::where('status',1)->whereIn('id',$restaurantIdsHaveProduct)->lists('name','id');
			}

			$order_q = Order::leftjoin('restaurant', 'order.restaurant_id', '=', 'restaurant.id')
			->leftjoin('user as driver', 'order.driver_id', '=', 'driver.id')	
			->select('order.*','restaurant.name as restaurant_name',DB::raw('CONCAT(driver.first_name, " ", driver.last_name) AS driver_name'))
			->where('order.id',$id);

			if(Session::get('access.role') != 'admin') {			
				$order_q->where('restaurant.id', Session::get('access.restaurant_ids'));
			}

			$data['order'] = $order_q->first();

			if(!$data['order']) throw new \Exception("No record was found.", 404);

			$orderdetails = (new ORDER())->getorderfulldetails($data['order']->id);
			
			/** Order Address fix **/
			if($data['order']->order_type != 'pickup') {
	    		$shipJson = json_decode($data['order']->ship_json);
	    		$shipJson->address_json = json_decode($shipJson->address_json);
	    		$orderdetails['order']->ship_add1 = $data['order']->ship_add1 = $shipJson->first_address;
	    		$orderdetails['order']->ship_city = $data['order']->ship_city = $shipJson->address_json?$shipJson->address_json->city:'';
	    		$orderdetails['order']->zip = $data['order']->zip = $shipJson->zip;
			}
			
			//$request['contactNumber']=substr($data['order']->ship_mobile,-10);
			
			/*if($request['contactNumber']) {
		 		$customer = User::select('user.id','user.first_name','user.last_name','user.contact_number')
		 		->Where('user.contact_number', 'like', '%' . trim($request['contactNumber']) . '%')->first();
		 	}else {*/
	 		$customer = User::select('user.id','user.first_name','user.last_name','user.contact_number')
	 		                        ->Where('user.id', $data['order']->user_id)->first();	 
		 	//}
 			
	 		return view('admin.order.edit',['data'=>$data,'type'=>'edit','customer'=>$customer, 'orderdetails'=>$orderdetails]);

	 	} catch (\Exception $e) {
	 		echo $e->getMessage();die;
            return redirect()->back();
        }

	}


	public function update($id, Request $request){
	    $order = Order::findOrFail($id);
	    $input = $request->all();
    	
	    if (isset($input['radiog_lite'])) Order::setCustomerAdderss($input['radiog_lite'],$input['order_id']);

	    $order->status=$input['status'];
	    $order->save();
	    Session::flash('flash_message', trans('admin.successfully.updated'));
	    
	    if(!empty($input['referer'])){
			Session::put('referer',$input['referer']);
	    }
	    return redirect()->back();
	}


	public function create()
	{
		$request = Request::all(); 
		print_r($request);exit;
	}


	public function delete($id, Request $request){		
		$order = Order::findOrFail($id);
		$order->delete();		
		Session::flash('flash_message', trans('admin.successfully.deleted'));
		return redirect($request->segment(2).'/order');
	}	


	public function getOrdersCount()
	{
		$ordersCount = 0;

		if (Auth::check() && Auth::user()->role_id == 6) {
			$ordersCount = Order::Where('status', 1)
		                    ->whereIn("restaurant_id", Session::get('access.restaurant_ids'))->whereNull('deleted_at')->count();
		} else {
			$ordersCount = Order::where('status', '1')->whereNull('deleted_at')->count();
		}

		return $ordersCount;
	}

}