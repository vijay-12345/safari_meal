<?php 

namespace App\Http\Controllers\Api;

use \Auth,\Hash;
use Illuminate\Http\Request;
use Validator;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Contracts\Auth\Registrar;
use App\User;
use App\Driver;
use App\Order,App\Page,App\UserAddress,App\Table;
use \Mail;
use Illuminate\Pagination\Paginator;
use Exception;

class CustomerController extends \App\Http\Controllers\Controller 
{ 
	/**
	 * Create a new controller instance.
	 *
	 * @return void
	 */
	public function __construct() {    
		//$this->middleware('auth.api', ['except' => ['login']]);
	}

	/*
	* url : /customer 
	* verb : get	
	*/
	public function index() {						
		$q = User::where(['status'=>1,'deleted_at'=>Null]);		
		$data = $q->select('first_name','last_name','email')->get();
		return $data->toJson();
	}

	/*
	* url : /customer/create 
	* verb : get	
	*/	
	public function create() {

		return ['status'=>'create...'];
	}

	/*
	* user update
	* url : /customer
	* verb : post	
	*{ "first_name":"testwqewew123","last_name":"tester","contact_number":"3543545454","id":12}
	*/		
	public function store(Request $request) {

		$input = $request->all();
		try {
			//prd($input['id']);			
			$roleArr = [\Config::get('constants.user.customer'),\Config::get('constants.user.driver')];
			$user = User::whereIn('role_id',$roleArr)->findOrFail($input['id']);
			if(isset($input['image'])) {
				//working...
				return $this->apiResponse(["data"=>$input],true);
			} else {
				$rules = User::rulesUpdateApi($request->input('id'));		   
			  	$v = Validator::make($input, $rules);
			  	if(isset($input['password'])) {
			  		$input['password']= Hash::make($input['password']);
			  	}			  	
			    $user->fill($input)->save();	      	    
				if ($v->fails()) {
				   $out_error = [];
			       foreach($rules as $key => $value) {
			        	$errors = $v->errors();
			            if($errors->has($key)) { 
			                $out_error[] = $errors->first($key); 
			            }
			        }
			        return $this->apiResponse(["error"=>$out_error],true);				 
				}			
			}		
			return $this->apiResponse(['message' => trans('admin.successfully.updated')]);		  

		} catch(\Exception $e) {
			return $this->apiResponse(["error"=>$e->getMessage()],true);
		}	

	}

	/*
	* url : /customer/{customer}
	* verb : get	
	*/		
	public function show() {
		return ['status'=>'store...'];
	}

	/*
	* url : /customer/{customer}/edit
	* verb : get	
	*/	
	public function edit() {
		return ['status'=>'edit...'];
	}

	/*
	* url : /customer/{customer}
	* verb : PUT/PATCH	
	*/	
	public function update($id,Request $request) {		
		return ['status'=>'update...'];
	}

	/*
	* url : /customer/{customer}
	* verb : DELETE	
	*/	
	public function destroy() {
		return ['status'=>'destroy...'];
	}

	/*
	* list/edit/add/delete address
	*/
	public function address(Request $request)
	{
		$inputs = $request->all(); 
		
		try {	
			if($inputs['type'] == 'list') {

				$data = UserAddress::where('user_id',$inputs['user_id'])->get();
				
				foreach($data as $key => $address)
				{
					if(isset($address->address_json) && !empty($address->address_json)) {
						$data[$key]->address_json = json_decode($address->address_json);
					}
				}
				return $this->apiResponse(['data' => $data ]);		  
			}

			if($inputs['type'] == 'edit')
			{
				$address = UserAddress::findOrFail($inputs['id']);
				$inputs = $this->convertAddressToJson($inputs);
				$addressRes = $address->fill($inputs)->save();
				
				if(isset($addressRes->address_json) && !empty($addressRes->address_json)) {
					$addressRes->address_json = json_decode($addressRes->address_json);
				}

				return $this->apiResponse(['message' => trans('admin.successfully.updated'),'data'=>$addressRes ]);
			}

			// Add new address
			if($inputs['type'] == 'add')
			{
				//$latlong = getLatLong($inputs['area_name'].' '.$inputs['city'].' '.$inputs['state_id']);
				$inputs = $this->convertAddressToJson($inputs);
				// $inputs['latitude'] = $latlong['lat'];
				// $inputs['longitude'] = $latlong['long'];
				$addressRes = UserAddress::create($inputs);
				if(isset($addressRes->address_json) && !empty($addressRes->address_json)) {
					$addressRes->address_json = json_decode($addressRes->address_json);
				}
				return $this->apiResponse(['message' => trans('admin.successfully.added'),'data'=>$addressRes]);
			}

			if($inputs['type'] == 'delete') {
				$address = UserAddress::findOrFail($inputs['id']);
				$address->delete();
				return $this->apiResponse(['message' => trans('admin.successfully.deleted') ]);
			}

		} catch(\Exception $e) {
			return $this->apiResponse(["error"=>$e->getMessage()],true);
		}
	}

	/* Provide static page*/
	public function page($slug,Request $request) {		
		try {

			if($request->isMethod('post')) {
			    $this->validate($request, [
			        'fname' => 'required',
			        'lname' => 'required',
			        'email' => 'required',
			        'message'=>'required'
			    ]);
			    $input = $request->all();
			   
				Mail::send('emails.contact', ['data'=> $input], function($message)  use ($input) {
					$message->from($input['email'], 'Food Fox');
					$message->to(\Config::get('constants.administrator.email'), $input['fname'])->subject('Contact Us');
				});
				return $this->apiResponse(["message"=>'Successfully Sent!']);				
			}

			$pages = Page::lang()->where(['status'=>1,'page_urlalias'=>$slug])->first();
			if(count($pages) > 0) {
				echo view('home.apipage',['data'=>$pages]);die;					
				//return $this->apiResponse(["data"=>$pages]);
			} else {
				return $this->apiResponse(["message"=>trans('admin.not.found')]);	
			}
			
		} catch(\Exception $e) {
			return $this->apiResponse(["error"=>$e->getMessage()],true);
		}
	}

	/*
	* Written By sandeep singh
	* Date : 23-5-17
	* Order Listing with Pagination
	*/	
	public function orderList(Request $request) {
		$inputs = $request->all();			
		try {
			if(!empty($inputs['orderlimit'])) {			
				$orderlimit=$inputs['orderlimit'];
			} else {
				$orderlimit=5;
			}
			if(!isset($inputs['user_id'])) {
				return $this->apiResponse(["error"=>'Please provide user_id'],true);
			}

			$order = Order::where(['user_id'=>$inputs['user_id']])
							->where('status','!=',\Config::get('constants.order.noconfirm'))
							->with([
								'items',
								'customer',
								'driver',
								'restaurant'
							])
							->orderby('id','desc')
							//->orderby('time','desc')
							->paginate($orderlimit);

			//$order = Order::where(['user_id'=>$inputs['user_id']])->where('status','!=',\Config::get('constants.order.noconfirm'))->with('items')->with('customer')->with('driver')->with('restaurant')->orderby('date','desc')->orderby('time','desc')->paginate($orderlimit);

			//$order = Order::where(['user_id'=>$inputs['user_id']])->where('status','!=',\Config::get('constants.order.noconfirm'))->with('items')->with('customer')->with('driver')->with('restaurant')->orderby('date','desc')->get();
			
			if(!$order->isEmpty()) {
				$order = $order->toArray();
				$data = array_merge(str_replace('/?', '?', $order),['order_status_type'=>\Config::get('constants.order_status_label.weborapp')]);
			} else {
				$data = null;
			}
			return $this->apiResponse(["data"=>$data]);

		} catch(\Exception $e) {
			return $this->apiResponse(["error"=>$e->getMessage()],true);
		}			
	}



	public function tablebookList(Request $request) {
		$inputs = $request->all();			
		try {
			if(!empty($inputs['orderlimit'])) {			
				$orderlimit=$inputs['orderlimit'];
			} else {
				$orderlimit=5;
			}
			if(!isset($inputs['user_id'])) {
				return $this->apiResponse(["error"=>'Please provide user_id'],true);
			}

			$order = Table::where(['user_id'=>$inputs['user_id']])
							->where('status','!=',\Config::get('constants.order.noconfirm'))
							->with([
								'customer',
								'restaurant'
							])
							->orderby('id','desc')
							//->orderby('time','desc')
							->paginate($orderlimit);
			
			foreach($order as $od){
			    if($od->book_date<date('Y-m-d')){
			        $od->status = 2;
			    }
			}  
			
			if(!$order->isEmpty()) {
				$order = $order->toArray();
				$data = str_replace('/?', '?', $order);
			} else {
				$data = null;
			}
			return $this->apiResponse(["data"=>$data]);

		} catch(\Exception $e) {
			return $this->apiResponse(["error"=>$e->getMessage()],true);
		}			
	}


    public function updateTableStatus(Request $request) {
		try {
			$params = $request->all();

			// Check if user exists
			if(!isset($params['user_id'])) {
				return $this->apiResponse(["error"=>'Please provide user id'],true);
			}
            
            if(!isset($params['order_id'])) {
				return $this->apiResponse(["error"=>'Please provide order id'],true);
			}
			
			// Check if order exists
			$table = Table::where('order_number', $params['order_id'])->first();
			if(count($table) < 1) {
				return $this->apiResponse(["message"=>'Booking does not exist']);	
			}
			
			// Check if user is owner of order
			if( $table->user_id != $params['user_id'] ) {
				throw new Exception('You are not permittted to cancel this order.', 1);
			}

			// Check if order is already cancelled
			if( $table->status == 6 ) {
				throw new Exception('Booking is already cancelled..', 1);
			}

			// Finally, Cancel order
			$table->status = 6;
			$table->save();

			// Send success response
			return $this->apiResponse(["message"=>'Booking cancel sucessfully']);

		} catch(Exception $e) {
			return $this->apiResponse(['error' => $e->getMessage()], true);	
		}
	}

	/*
	* Written by sandeep singh
	* Date : 24-05-17
	* return order detail using order number
	*/	
	public function orderByOrderNumber(Request $request)
	{
		$inputs = $request->all();
		try {
			if(!isset($inputs['order_number'])) throw new Exception('Please provide order number', 1);
			// Check if order exists?
			
			$order = Order::with([
                            'items',
                            'customer',
                            'driver',
                            'restaurant',
                        ])->where([
                        	'order_number' => $inputs['order_number'] 
                        ])->first();
            
			if( !empty($order) ) $order = $order->toArray();
			else throw new Exception('Order number is not valid', 1);

			$data = array_merge($order, ['order_status_type' => \Config::get('constants.order_status_label.weborapp')]);
			
			return $this->apiResponse(["data"=>$data]);

		} catch(\Exception $e) {
			return $this->apiResponse(['error' => $e->getMessage()], true);
		}
	}


	public function updateDeviceToken(Request $request)
	{
		try {

			if (! $request->has('device_token') || ! $request->has('id')) 
				throw new Exception("Both, user id and device token are required.", 1);

			$user = User::find($request->get('id'));

			if (! $user) throw new Exception("No user was found with given id.", 1);

			$user->device_token = $request->get('device_token');
			$user->save();

			// dd($user);

			return $this->apiResponse(["data" => $user]);
			
		} catch (\Exception $e) {
			return $this->apiResponse(['error' => $e->getMessage()], true);
		}
	}

}