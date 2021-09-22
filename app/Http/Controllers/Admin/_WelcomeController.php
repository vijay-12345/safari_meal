<?php

namespace App\Http\Controllers\Admin;
use Auth;
use Session;
use DB;
use Illuminate\Http\Request;
use App\Restaurent,App\UserRestaurant;
use App\User;
use App\Driver;
use App\Order;
use Illuminate\Pagination\Paginator;
use App\Traits\StaticPushNotify;

class WelcomeController extends \App\Http\Controllers\Controller 
{
	use StaticPushNotify;

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
	{ echo 'hello'; exit;
		
		$this->middleware('auth');	

	}

	/**
	 * Show the application welcome screen to the user.
	 *
	 * @return Response
	 */
	public function index(Request $request)
	{				
		return view('admin.dashboard',['data'=>null,'filter'=>null]);
	}
	public function edit($id){

		$drivers = User::where('status',1)
		->where('role_id',\Config::get('constants.user.driver'))
		->select(DB::raw('CONCAT(first_name," ",last_name) AS name'), 'id')
		->lists('name','id');				
		$order_q = DB::table('order')		
		->leftjoin('restaurant', 'order.restaurant_id', '=', 'restaurant.id')
		->leftjoin('user as driver', 'order.driver_id', '=', 'driver.id')	
		->select('order.*','restaurant.name as restaurant_name',DB::raw('CONCAT(driver.first_name, " ", driver.last_name) AS driver_name'))
		->where('order.id',$id);
		$order = $order_q->first();
		$html = view('admin.ajaxedit',['order'=>$order,'drivers'=>$drivers,'type'=>'edit']);
		echo $html;
		exit();	

	}

	public function update(Request $request)
	{
		$prefix = $request->segment(2);

		if($request->input('action') == 'update') {
			Order::where('id',$request->input('id'))->update($request->only(['status', 'driver_id']));
		}
			
		$order_q = DB::table('order')
		->leftjoin('restaurant', 'order.restaurant_id', '=', 'restaurant.id')
		->leftjoin('user as driver', 'order.driver_id', '=', 'driver.id')
		->leftjoin('user', 'order.user_id', '=', 'user.id')		
		->select('order.*','restaurant.name as restaurant_name',DB::raw('CONCAT(driver.first_name, " ", driver.last_name) AS driver_name'),'user.first_name as customer_first_name','user.last_name as customer_last_name')
		->where('order.id',$request->input('id'));		

		$order = $order_q->first();

		// Send push notification to driver from here for admin...
    	$deviceTokenToCustomer = User::where([
    						  		'id' 		=> $order->user_id,
    						  		'status' 	=> 1, 
    						  		'role_id' 	=> \Config::get('constants.user.customer')
    						  	])
    						 	->lists('device_token');

    	$deliveryMsg = '';
    	if ($order->order_type == 'pickup') $deliveryMsg = 'Your order has been picked up.';
    	else if ($order->order_type == 'delivery') $deliveryMsg = 'Your order has been delivered.';

    	$customerMessage = '';
    	if ($request->has('status') && $request->get('status')) {

    		if ($request->get('status') == 3) $customerMessage = 'Driver is on it\'s way to the restaurant.';
    		else if ($request->get('status') == 4) $customerMessage = 'Your order has been picked up by the driver.';
    		else if ($request->get('status') == 5) $customerMessage = $deliveryMsg;

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

            StaticPushNotify::setAndroidApiAccessKey('AAAAiJLW_vs:APA91bFZSZ-d9ll2njulL8Iv5bfhyhzltGq24stljgUhQAQ4AKlzYpsiPLHHSx7uuV1BP0pZPHYVYV3E74juyB1fhdLpCvTKlV2j2BWI91ednIdKi0H7S7tvvSy2XH3kJ1iO7ds1S8-2lbnqeCTkJqb4Ij6Un4hOow');
    		//StaticPushNotify::setAndroidApiAccessKey('AAAA_Gf9Xig:APA91bEf9bcQGtgjzVOx8LkE2_JgWGxopUbuTsIWqh5AlZdv0O_l4VjKTIhJ1bl7R1kcRJLRn-BQpbJLRD2Q-c_RL_wktWBC1L2NxfBwxKscvcgmLFGAltzvqFxQB9wiHdBCWFtQ5R19');

	    	$deviceTokenToCustomer = StaticPushNotify::notify();

    	}
		// Send push notification to driver from here for admin...


		$list_data = view('admin.ajaxedit',['order'=>$order,'type'=>'update','view'=>'list_data','prefix'=>$prefix]);
		$view_data = view('admin.ajaxedit',['order'=>$order,'type'=>'update','prefix'=>$prefix]);

		echo json_encode(['list_data'=>utf8_encode($list_data),'view_data'=>utf8_encode($view_data)]); exit();

	}
	/*=======area manager =============*/
	public function manager_index(Request $request)
	{
		
		//clear session first time	
		if ($request->isMethod('get') && count($request->all()) == 0){	
			Session::forget('filter');			
		}		
		/* filter 			
		*/	
		if($request->has('sorting')){
			$filter = ['sort'=>$request->input('sorting'),'field'=>$request->input('field')];
		}else{
			$filter = ['sort'=>'desc','field'=>'order_number'];
		}
		$filter['paginate_sort'] = $filter['sort'];
		
		if($request->has('paginate_limit')){
			session(['filter.paginate_limit' => $request->input('paginate_limit')]);
			session(['filter.restaurant' => $request->input('restaurant')]);			
			session(['filter.search' => trim($request->input('search'))]);
		}elseif(!Session::has('filter.paginate_limit')){
			session(['filter.paginate_limit' => 10]);
		}		
		/*
		* fetch data
		*/	
		$data = [];
		
		$data['restaurant_data']  = Restaurent::where('status',1)
									->whereIn('id',\Session::get('access.restaurant_ids'))
									->get();		
		$data['total_restaurant'] = count($data['restaurant_data']);

		$data['customer_data'] = User::where('status',1)->where('role_id',\Config::get('constants.user.customer'))->get();
		$data['total_customer'] = count($data['customer_data']);
		
		$data['driver_data'] = User::where('status',1)->where('role_id',\Config::get('constants.user.driver'))->get();
		$data['total_driver'] = count($data['driver_data']);

		$data['order_pending'] = Order::where(['status'=>\Config::get('constants.order.pending')])
								->whereIn('restaurant_id',\Session::get('access.restaurant_ids'))
								->get()->count();
		//chart info
		$data['orderamount_monthly_by'] = Order::select(DB::raw('extract(month from date) as month,sum(amount) as amount'))->where('status',\Config::get('constants.order.delivered'))->groupBy(DB::raw('extract(month from date)'))->get();		
		$data['ordergroupbystatus'] = Order::select(DB::raw('count(id) as numberOfOrders,status'))->groupBy('status')->get();				
		//prd($data['order_group_by_status']);
		//end of chart
		$order_q = Order::where('order.status','!=',\Config::get('constants.order.noconfirm'))
		->whereIn('order.restaurant_id',\Session::get('access.restaurant_ids'))
		->leftjoin('restaurant', 'order.restaurant_id', '=', 'restaurant.id')
		->leftjoin('user as driver', 'order.driver_id', '=', 'driver.id')
		->leftjoin('user', 'order.user_id', '=', 'user.id')	
		
		->select('order.*','restaurant.name as restaurant_name',DB::raw('CONCAT(driver.first_name, " ", driver.last_name) AS driver_name'),'user.first_name as customer_first_name','user.last_name as customer_last_name')
		->orderBy($filter['field'], $filter['sort']);
		if($filter['field'] == 'date'){
			$order_q = $order_q->orderBy('time', $filter['sort']);
		}
		
		if(Session::has('filter.restaurant') && session('filter.restaurant') !=''){
			$order_q->where('restaurant.id', session('filter.restaurant'));
		}
		if(Session::has('filter.search') && session('filter.search') !=''){
			$order_q->where('order.order_number', session('filter.search'));
		}			

		$data['orders_data'] = $order_q->paginate(session('filter.paginate_limit'));

		
		//---------------------End of fetch data--------------------------
		if($filter['sort'] == 'asc'){
			$filter['sort'] = 'desc';
		}else{
			$filter['sort'] = 'asc';
		}		
		return view('admin.dashboard',['data'=>$data,'filter'=>$filter]);
	}
	/*=======restaurant dashboard =============*/
	public function restaurant_index(Request $request)
	{		
		//clear session first time	
		if ($request->isMethod('get') && count($request->all()) == 0){	
			Session::forget('filter');			
		}		
		/* filter 			
		*/	
		if($request->has('sorting')){
			$filter = ['sort'=>$request->input('sorting'),'field'=>$request->input('field')];
		}else{
			$filter = ['sort'=>'desc','field'=>'order_number'];
		}
		$filter['paginate_sort'] = $filter['sort'];
		
		if($request->has('paginate_limit')){
			session(['filter.paginate_limit' => $request->input('paginate_limit')]);
			session(['filter.restaurant' => $request->input('restaurant')]);			
			session(['filter.search' => trim($request->input('search'))]);
		}elseif(!Session::has('filter.paginate_limit')){
			session(['filter.paginate_limit' => 10]);
		}		
		/*
		* fetch data
		*/	
		$data = [];

		$data['order_pending'] = Order::where(['status'=>\Config::get('constants.order.pending')])
								->whereIn('restaurant_id',\Session::get('access.restaurant_ids'))
								->get()->count();
		//chart info
		$data['orderamount_monthly_by'] = Order::select(DB::raw('extract(month from date) as month,sum(amount) as amount'))->where('status',\Config::get('constants.order.delivered'))->groupBy(DB::raw('extract(month from date)'))->get();		
		$data['ordergroupbystatus'] = Order::select(DB::raw('count(id) as numberOfOrders,status'))->groupBy('status')->get();				
		//prd($data['order_group_by_status']);
		//end of chart
		$order_q = Order::where('order.status','!=',\Config::get('constants.order.noconfirm'))
		->whereIn('order.restaurant_id',\Session::get('access.restaurant_ids'))
		->leftjoin('restaurant', 'order.restaurant_id', '=', 'restaurant.id')
		->leftjoin('user as driver', 'order.driver_id', '=', 'driver.id')
		->leftjoin('user', 'order.user_id', '=', 'user.id')	
		
		->select('order.*','restaurant.name as restaurant_name',DB::raw('CONCAT(driver.first_name, " ", driver.last_name) AS driver_name'),'user.first_name as customer_first_name','user.last_name as customer_last_name')
		->orderBy($filter['field'], $filter['sort']);
		if($filter['field'] == 'date'){
			$order_q = $order_q->orderBy('time', $filter['sort']);
		}
		
		if(Session::has('filter.restaurant') && session('filter.restaurant') !=''){
			$order_q->where('restaurant.id', session('filter.restaurant'));
		}
		if(Session::has('filter.search') && session('filter.search') !=''){
			$order_q->where('order.order_number', session('filter.search'));
		}			

		$data['orders_data'] = $order_q->paginate(session('filter.paginate_limit'));

		
		//---------------------End of fetch data--------------------------
		if($filter['sort'] == 'asc'){
			$filter['sort'] = 'desc';
		}else{
			$filter['sort'] = 'asc';
		}	
	
		return view('admin.dashboard',['data'=>$data,'filter'=>$filter]);
	}	
	public function delete($id, Request $request){		
		$order = Order::findOrFail($id);				
		$order->delete();		
		Session::flash('flash_message', trans('admin.successfully.deleted'));
		return redirect($request->segment(2).'/dashboard');
	}	
}
