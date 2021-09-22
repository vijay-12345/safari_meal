<?php

namespace App\Http\Controllers\Admin;
use DB;
use Auth;
use Form;
use \Mail;
use Session;
use App\User;
use App\Coupon;
use \Exception;
use App\Restaurent;
use App\Traits\PushNotify;
use Illuminate\Http\Request;
use Illuminate\Pagination\Paginator;
use Illuminate\Contracts\Auth\Registrar;

class CouponController extends \App\Http\Controllers\Controller 
{
	use PushNotify;

	/*
	|--------------------------------------------------------------------------
	| Coupon Controller
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

		/* filter 			
		*/
		if($request->has('sorting')) {
			$filter = ['sort'=>$request->input('sorting'),'field'=>$request->input('field')];
		} else {
			$filter = ['sort'=>'asc','field'=>'coupon_code'];
		}
		$filter['paginate_sort'] = $filter['sort'];
		
		if($request->has('paginate_limit')) {
			session(['filter.paginate_limit' => $request->input('paginate_limit')]);						
			session(['filter.search' => trim($request->input('search'))]);
			//handle when blank/space inpute in search
			if($request->input('search'))
				if(session('filter.search')==null)
				{
					$data = null;
					return view('admin.coupon.index',['data'=>$data,'filter'=>$filter]);
				}
		} elseif(!Session::has('filter.paginate_limit')){
			session(['filter.paginate_limit' => 10]);
		}
		// echo $request->has('restaurant_id');die;
		if($request->has('restaurant_id')) {
			session(['filter.restaurant_id' => $request->input('restaurant_id')]);	
		} elseif(!Session::has('filter.restaurant_id')) {
			session(['filter.restaurant_id' => 'All']);
		}

		$data = [];		
		$q = Coupon::select('coupon.*','restaurant.name as restaurant_name','restaurant.is_home_cooked')
					->join('restaurant','restaurant.id','=','coupon.restaurant_id')
					->where('restaurant.is_home_cooked','=','0')
					->orderBy($filter['field'], $filter['sort']);

		if(Session::has('filter.search') && session('filter.search') !=''){
			$q->where('coupon.coupon_code','like', '%'.session('filter.search').'%');
		}
		if(Session::has('filter.restaurant_id') && session('filter.restaurant_id') !='All'){
			$q->where('coupon.restaurant_id','=', session('filter.restaurant_id'));
		}

		if(Session::has('access.role') && Session::get('access.role') !='admin'){			
			$q->whereIn('coupon.restaurant_id', Session::get('access.restaurant_ids'));
		}
		$data['data'] = $q->paginate(session('filter.paginate_limit'));

		//---------------------End of fetch data--------------------------	
		if($filter['sort'] == 'asc') {
			$filter['sort'] = 'desc';
		} else {
			$filter['sort'] = 'asc';
		}

		return view('admin.coupon.index',['data'=>$data,'filter'=>$filter]);
	}


	public function add(Request $request)
	{
		$customers = User::where(['status'=>1,'role_id'=>\Config::get('constants.user.customer')])->lists('email','id');

		if($request->isMethod('post')) {
			$rules = Coupon::$rules['create'];
			$input = $request->all();

			$rules['coupon_code'] = 'required|unique:coupon,coupon_code,NULL,id,restaurant_id,'.$input['restaurant_id'];
		   	
		    $this->validate($request, $rules, [
				        	'restaurant_id.required' => 'The restaurant field is required.',
				        ]);
		    
		    if(!empty($input['combo_offer']) && !empty($input['products'])) {
		    	$input['product_can_use'] = implode(",",$input['products']);
		    	$input['combo_offer'] = 1;
		    } else {
		    	$input['combo_offer'] = 0;
		    	$input['product_can_use'] = '';
		    }
		    
		    //if(!empty($input['customers']) && count($input['customers']) != count($customers)) {
	    	if(!empty($input['customers'])) {
		    	$input['coupon_can_use'] = implode(",", $input['customers']);
		    } else {
		    	$input['coupon_can_use'] = '';
		    }
		    
		    Coupon::dateFormat($input);		    
		    Coupon::create($input);

		    // Send push notification to users from here for api...
		    if ($request->has('customers') && count($request->get('customers') > 0)) 
		    	$this->sendCouponNotification($request->get('customers'), $input);

		    Session::flash('flash_message',  trans('admin.successfully.added'));
		    	
		    return redirect()->back();	        
	        
		}
		return view('admin.coupon.add',['customers'=>$customers]);
	}


	public function edit($id) {
		
		try {
			if(Session::get('access.role') == 'restaurant') {
				$coupon = Coupon::whereIn('restaurant_id', Session::get('access.restaurant_ids'))->findOrFail($id);
			} else {
				$coupon = Coupon::findOrFail($id);
			}
			
			if(!$coupon) throw new Exception("No record was found.", 404);
			
			$customers = User::where(['status'=>1,'role_id'=>\Config::get('constants.user.customer')])->lists('email','id');
			
	    	Coupon::dateFormat($coupon, 'd-m-Y');
	    	
	    	return view('admin.coupon.edit',['customers'=>$customers,'coupon'=>$coupon]);
	    	
    	} catch (\Exception $e) {
            return redirect()->back();
        }
		
	}


	public function update($id, Request $request)
	{
		if(Session::get('access.role') != 'admin'){
			$coupon = Coupon::whereIn('restaurant_id',Session::get('access.restaurant_ids'))->findOrFail($id);
		}else{
			$coupon = Coupon::findOrFail($id);
		}
		$rules = Coupon::$rules['edit'];
		$input = $request->all();
		
		// $rules['coupon_code'] = 'required|unique:coupon,coupon_code,NULL,id,restaurant_id,'.$input['restaurant_id'].',id,'.$id;
		$rules['coupon_code'] = 'required|unique:coupon,coupon_code,' . $id;
		
	    $this->validate($request, $rules, [
				        	'restaurant_id.required' => 'The restaurant field is required.',
				        ]);
	    
	    if(!empty($input['combo_offer']) && !empty($input['products'])) {
	    	$input['product_can_use'] = implode(",",$input['products']);
	    	$input['combo_offer'] = 1;
	    } else {
	    	$input['combo_offer'] = 0;
	    	$input['product_can_use'] = '';
	    }
	    
	    if(!empty($input['customers'])) {
	    	$input['coupon_can_use'] = implode(",", $input['customers']);
	    } else {
	    	$input['coupon_can_use'] = '';
	    }
	    Coupon::dateFormat($input);
    	
		// prd($coupon->toArray());
	    $coupon->fill($input)->save();
	    
	    // Send push notification to users from here for api...
	    if ($request->has('customers') && count($request->get('customers') > 0)) 
	    	$this->sendCouponNotification($request->get('customers'), $input);
	    
	    Session::flash('flash_message',  trans('admin.successfully.updated'));

	    return redirect()->back();
	}


	public function delete($id,Request $request) {
		
		if(Session::get('access.role') != 'admin'){
			$coupon = Coupon::whereIn('restaurant_id',Session::get('access.restaurant_ids'))->findOrFail($id);
		} else {
			$coupon = Coupon::findOrFail($id);
		}
		$coupon->delete();		
		Session::flash('flash_message',  trans('admin.successfully.deleted'));
		return redirect($request->segment(2).'/coupon');
	}	


	public function sendCouponNotification(Array $customers, $input)
	{
    	$deviceTokens = User::whereIn('id', $customers)
    						  ->where(['status' => 1, 'role_id' => \Config::get('constants.user.customer')])
    						  ->lists('device_token');

    	$this->setAndroidApiAccessKey('AAAA_Gf9Xig:APA91bEf9bcQGtgjzVOx8LkE2_JgWGxopUbuTsIWqh5AlZdv0O_l4VjKTIhJ1bl7R1kcRJLRn-BQpbJLRD2Q-c_RL_wktWBC1L2NxfBwxKscvcgmLFGAltzvqFxQB9wiHdBCWFtQ5R19');

    	$this->setReceivers($deviceTokens);

    	$message = '';

    	if ($input['type'] == 0) {
    		$message = 'Use coupon code ' . $input['coupon_code'] . ' to get $' . $input['coupon_value'] . ' off on your order.';
    	} else if ($input['type'] == 1) {
    		$message = 'Use coupon code ' . $input['coupon_code'] . ' to get ' . $input['coupon_value'] . '% off on your order.';
    	}

		$this->message = [
			'title'				=> 'New deal available.',
			'message' 			=> $message,
			'subtitle'			=> $input['description'] ? $input['description'] : '',
			'tickerText'		=> 'Ticker text here...Ticker text here...Ticker text here',
			'vibrate'			=> 1,
			'sound'				=> 1,
			'largeIcon'			=> 'large_icon',
			'smallIcon'			=> 'small_icon',
			'type'				=> 'coupon',
			'notificationID'	=> uniqid()
		];

    	$notificationResponse = $this->notify();
	}


}