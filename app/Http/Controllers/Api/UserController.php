<?php 
namespace App\Http\Controllers\Api;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use \Auth;
use \Validator;
use Hash;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Contracts\Auth\Registrar;
use DB, Config, Redirect, Exception;
use App\Restaurent;
use App\helpers;
use App\Product, App\Coupon, App\City, App\User, App\Menu;
use App\Setting;
use App\Order;
use App\Image;
use App\UserAddress;
use App\DriverDecline;
use App\OtpVerification;
use Illuminate\Http\Request;
use Illuminate\Pagination\Paginator;
use Session, View;
use App\OrderStatus;
use App\Traits\StaticPushNotify;


class UserController extends \App\Http\Controllers\Controller {

	public function changePassword(Request $request) 
    {
        try
        {
            $inputs = $request->all();
            $validatedData = Validator::make(
            $request->all(),[
                'id' => 'required',
                'old_password' => 'required|min:6|max:20',
                'new_password' => 'required|min:6|max:20',
                ]
            );
            $rules = [
                'id' => 'required',
                'old_password' => 'required|min:6|max:20',
                'new_password' => 'required|min:6|max:20'
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
                return $this->apiResponse(['error' => $var ,'message' => $var], true);
            }
            $user = User::where('id','=',$inputs['id'])->first();
            if($user->role_id == 3)
            {
                $hashedPassword = $user['password'];
            	if ( Hash::check($request->old_password, $hashedPassword) ){
	                // $user->password = Hash::make($request->new_password);
	                $user->password = bcrypt($request->new_password);
	                $user->save();
	                return $this->apiResponse(['success' => 'true','message' => 'Successfully change password', 'data'=>$user]);
            	}
            	return $this->apiResponse(['message' => 'Old password doesnot match'], true);
            }
            return $this->apiResponse(['message' => 'User is not customer'], true);

        } catch(\Exception $e) {
            return $this->apiResponse(['error' => $e->getMessage()],true);
        }
    }

	public function logout(Request $request){
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

	public function deleteAddress(Request $request)
	{
		try{
				$input = $request->all();
				if(!isset($input['user_id'])  || empty($input['user_id']))
				{
					return $this->apiResponse(['message' => 'user id  not be null'], true);
				}
				if(!isset($input['address_id'])  || empty($input['address_id']))
				{
					return $this->apiResponse(['message' => 'address id  not be null'], true);
				}
				$deleteMessage = UserAddress::where('id', $input['address_id'])->where('user_id', $input['user_id'])
											->delete();
				if($deleteMessage){
					return $this->apiResponse(['success' => 'true' ,'message' => 'successfull deleted address']);
				}else{
					return $this->apiResponse(['message' => 'address not deleted'], true);
				}
			}
			catch(Exception $e){
					return $this->apiResponse(["error"=>$e->getMessage()],true);
			}
	}

 	public function update( Request $request)
	{		
		$input = $request->all();
		if(isset($input['id'])){
			try{
				$validatedData =  Validator::make($request->all(),[
		        		'email' => 'required|email',
		        		'id'     => 'required',
			            'first_name' => 'required',
			            'last_name' => 'required',         
			            'countrycode' => 'required',
		    		]);
				$rules = ['email' => 'required|email',
	        		'id'     => 'required',
		            'first_name' => 'required',
		            'last_name' => 'required',         
		            'countrycode' => 'required',
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
			    $user = User::where('id','=',$input['id'])->first();
			    
			    if($user == null){
			    	return $this->apiResponse(["error"=>'user Id is wrong!'],true);
			    }
			    //check conatct number is exist
			    $data = User::where('contact_number','=',$request->contact_number)->first();
		        if($data)
		        {
		        	if($data->id != $request->id)
		            return $this->apiResponse(['message' => 'Contact number is already exist'], true);
		        }

			    if($request->file('image')){            
		            $type = \Config::get('constants.image.for.user');
		            Image::deleteImage($request->input('old_image'),$type);
		            $input['profile_image'] = Image::imageUpload($request->file('image'),$type);        
		        }
			    $user->fill($input)->save();
				//$user1 = User::select('id','role_id','first_name','last_name','status')->where('id','=',$input['id'])->first();
				$user1 = User::where('id','=',$input['id'])->first();
				return $this->apiResponse(["message"=>"Successfully updated","data"=>$user1]);
			}catch(Exception $e){
				return $this->apiResponse(["error"=>$e->getMessage()],true);
			}
		}else{
			return $this->apiResponse(["error"=>trans('Undefined: user_id')],true);
		}
	}
			
					
}
