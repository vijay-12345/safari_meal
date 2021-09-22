<?php 
namespace App\Http\Controllers\Api;
use \Auth;
use \Validator;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Contracts\Auth\Registrar;
use Illuminate\Http\Request;
use App\Menu;
use App\User;
use App\Product;
use App\Restaurent;
use Illuminate\Pagination\Paginator;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Mail;

class LoginController extends \App\Http\Controllers\Controller {

	public function DeviceTokenUpdateFunction(Request $request){
		try{
			$inputs = $request->all();
			$validatedData =  Validator::make($request->all(),
						[
			        		'id'     => 'required',
				            'device_token' => 'required',
			    		]);
			$rules = [
					'id'     => 'required',
		            'device_token' => 'required',
				];
			if ($validatedData->fails()){
			   $out_error = [];
		       foreach($rules as $key => $value) {
		        	$errors = $validatedData->errors();
		            if($errors->has($key)) { 
		                $out_error[]= $errors->first($key);
		                $var = $errors->first($key);
		                break;
		            }
		        }			
				//return response()->json(['message' => $out_error,'status'=>'error']);
				return $this->apiResponse(['error' => $var ,'message' => $var], true);
			}	
			$data = User::where('id','=',$inputs['id'])->first();
			if( !$data )
		 	{
		 		return $this->apiResponse(['message' => 'Invalid id credentials'], true);
		 	}
		 	else
		 	{
		 		$data->device_token = $inputs['device_token'];
		 		$data->save();
		 		return $this->apiResponse(['success' => 'true' ,'message' => 'successfull update device token']);
		 	}	
		}
		catch(\Exception $e) {
			return $this->apiResponse(['error' => $e->getMessage()], true);
		}
	}
		
	public function forgetpasswordfunction(Request $request){
		try{
			$inputs = $request->all();
			  $validator = Validator::make(
            	$request->all(),[
                	'email' => 'required|email'
            		]
        		);
		       	if($validator->fails()) {
		            return $this->apiResponse(['error' => $validator->messages()->first()], true);
		        }
			if(!isset($inputs['email'])  || empty($inputs['email']))
			{
				return $this->apiResponse(['message' => 'email  not be null'], true);
			}
			else
			{
				$data = User::where('email','=',$inputs['email'])->first();
				if( !$data )
			 	{
			 		return $this->apiResponse(['message' => 'Invalid user_id  credentials'], true);
			 	}
			 	else
			 	{
				   $data1 = $data->toArray();
				   $data2 =[];
				   $data2['user'] = $data1;
				    // Mail::send('emails.hello', $data2, function($message) use ($data2) {
				    //     $message->to('vijay8101995@gmail.com');
				    //     $message->subject('E-Mail Example');
				    // });
				    Mail::send('emails.password', $data2, function($message) use ($data2) {
				        $message->to($data2['user']['email']);
				        $message->subject('E-Mail Example');
				    });
	   				return $this->apiResponse(['success' => 'true' ,'message' => 'successfull send the link on this email']);
			 	}
			}
		}
		catch(\Exception $e) {
			return $this->apiResponse(['error' => $e->getMessage()], true);
		}
	}

	public function logoutfunction(Request $request){
		try{
			$inputs = $request->all();
			if(!isset($inputs['user_id'])  || empty($inputs['user_id']))
			{
				return $this->apiResponse(['message' => 'user id  not be null'], true);
			}
			else
			{
				$data = User::where('id','=',$inputs['user_id'])->first();
				if( !$data )
			 	{
			 		return $this->apiResponse(['message' => 'Invalid user_id  credentials'], true);

			 	}
			 	else
			 	{
			 		$data->token = null;
			 		$data->device_token = null;
			 		$data->save();
			 		return $this->apiResponse(['success' => 'true' ,'message' => 'successfull logout']);
			 	}
			}
		}
		catch(\Exception $e) {
			return $this->apiResponse(['error' => $e->getMessage()], true);
		}
	}

	public function loginfunction(Request $request){
		try{
			$inputs = $request->all();
			$validatedData =  Validator::make($request->all(),[
		        'email' => 'required|email',
		        'password' => 'required|min:6',
		    ]);
			if($validatedData->fails())
			{
			 	return $this->apiResponse(['error' => $validator->messages()->first()], true);
			}
			$store = User::where('email','=',$inputs['email'])->first();
			if( !$store )
			{
			 	return $this->apiResponse(['message' => 'Invalid login  credentials'], true);
			}
			if($store->role_id == 6 && Auth::attempt($request->only(['email', 'password']), $request->input('remember'))) 
		    {
			$user = Auth::user();
			$token = md5(uniqid($user->email, true));
			$user->token = $token;
			$user->save();
			$final = [];
			$data1 = [];

			$restaurent = Restaurent::where('owner_id','=',$store['id'])->first();

			$data = [];
			$data['id'] = $store['id'];
			$data['role_id'] = $store['role_id'];
			$data['profile_image'] = $store['profile_image'];
			$data['first_name'] = $store['first_name'];
			$data['last_name'] = $store['last_name'];
			$data['email'] = $store['email'];
			$data['contact_number'] = $store['contact_number'];
			$data['verified'] = $store['verified'];
			$data['status'] = $store['status'];
			$data['token'] = $store['token'];
			$data['device_token'] = $store['device_token'];
			$data['created_at'] = $store['created_at'];
			$data['updated_at'] = $store['updated_at'];
			$data['deleted_at'] = $store['deleted_at'];
			$data['login_type'] = $store['login_type'];
			$data['countrycode'] = $store['countrycode'];
			$data['newsletter'] = $store['newsletter'];
			$data['last_lat'] = $store['last_lat'];
			$data['last_long'] = $store['last_long'];
			$final['user_details'] = $data;
			$data1['restaurent_id'] = $restaurent['id'];
			$data1['is_home_cooked'] = $restaurent['is_home_cooked'];
			$data1['parent_id'] = $restaurent['parent_id'];
			$data1['name'] = $restaurent['name'];
			$data1['phone'] = $restaurent['phone'];
			$data1['owner_id'] = $restaurent['owner_id'];
			$data1['area_id'] = $restaurent['area_id'];
			$data1['rating'] = $restaurent['rating'];
			$data1['cgst'] = $restaurent['cgst'];
			$data1['sgst'] = $restaurent['sgst'];
			$data1['packaging_fees'] = $restaurent['packaging_fees'];
			$data1['delivery_charge'] = $restaurent['delivery_charge'];
			$data1['delivery_applicable'] = $restaurent['delivery_applicable'];
			$data1['delivery_time'] = $restaurent['delivery_time'];
			$data1['flat_delivery_time'] = $restaurent['flat_delivery_time'];
			$data1['confirm_order'] = $restaurent['confirm_order'];
			$data1['longitude'] = $restaurent['longitude'];
			$data1['latitude'] = $restaurent['latitude'];
			$data1['tax_per_order'] = $restaurent['tax_per_order'];
			$data1['payment_method'] = $restaurent['payment_method'];
			$data1['is_veg'] = $restaurent['is_veg'];
			$data1['is_nonveg'] = $restaurent['is_nonveg'];
			$data1['floor'] = $restaurent['floor'];
			$data1['street'] = $restaurent['street'];
			$data1['company'] = $restaurent['company'];
			$data1['country_id'] = $restaurent['country_id'];
			$data1['state_id'] = $restaurent['state_id'];
			$data1['city_id'] = $restaurent['city_id'];
			$data1['status'] = $restaurent['status'];
			$data1['created_at'] = $restaurent['created_at'];
			$data1['updated_at'] = $restaurent['updated_at'];
			$data1['deleted_at'] = $restaurent['deleted_at'];
			$data1['lang_id'] = $restaurent['lang_id'];
			$data1['deals'] = $restaurent['deals'];
			$data1['restaurent_urlalias'] = $restaurent['restaurent_urlalias'];
			$data1['admin_commission'] = $restaurent['admin_commission'];
			$data1['featured'] = $restaurent['featured'];
			$final['restaurent_details'] = $data1;
			return $this->apiResponse(["success"=>"true" , "message"=>"Successfull Autheticated" ,"data"=>$final , "token"=>$store['token'] ]);

		  }
		  else
		  {
		  	return $this->apiResponse(['message' => 'Invalid login  credentials'], true);
		  }
		}
		catch(\Exception $e) {
			return $this->apiResponse(['error' => $e->getMessage()], true);
		}
	}


}
