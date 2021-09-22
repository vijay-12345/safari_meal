<?php 

namespace App\Http\Controllers\Api\Auth;

use \Auth;
use Illuminate\Http\Request;
use \Validator;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Contracts\Auth\Registrar;
use App\User;
use Exception;
use App\OtpVerification;

class AuthController extends \App\Http\Controllers\Controller {

  	protected $auth;
  	protected $registrar;
  
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
	public function __construct(Guard $auth, Registrar $registrar)
	{
		$this->auth = $auth;    
		$this->registrar = $registrar;
    
		//$this->middleware('auth.api', ['except' => ['login']]);
	}

	/**
	 * Show the application welcome screen to the user.
	 *
	 * @return Response
	 */

	public function index()
	{
		return \Response::json(['name'=>Auth::user()->name, 'testing' => Auth::user()->email]);
	}

	public function checkMobileNumber(Request $request)
	{
	    $validator = Validator::make(
	        $request->all(),[
	            'contact_number' => 'required|numeric|digits_between:10,12',
	        ]
	    );
    	
	    if($validator->fails()) return $this->apiResponse(['error' => $validator->messages()->first()], true);

		$user = User::where('contact_number', $request->contact_number)->first();

		if( empty($user) ) return $this->apiResponse(['message' => 'Mobile Number Not Exist.', 'number_exist' => false]);
		
		else return $this->apiResponse(['message' => 'Mobile Number Already Exist.', 'number_exist' => true]);
	}
	
	
	public function sendOtpBeforeRegister(Request $request)
	{
		try {

		    $rules  = User::$rulesApi;

			$params = $this->splitName($request);

			$validator = Validator::make($params, $rules, [
				'first_name.required' => 'Fullname is required. Please enter your first name.',
				'last_name.required' => 'Fullname is required. Please enter your last name.',
				'countrycode.required' => 'Please enter country code.'
			]);
			
			if($validator->fails()) return $this->apiResponse(['error' => $validator->messages()->first()], true);
			
			$OtpVerification = new OtpVerification();

			$response = $OtpVerification->sendOtp($request);

			return $this->apiResponse(['message' => 'Otp sent Successfully.']);

		} catch(\Exception $e) {
			return $this->apiResponse([ 'error'=> $e->getMessage()], true);
		}
	}
	
	
	public function login(Request $request)
	{
		try {
		    $validator = Validator::make(
		        $request->all(),[
		            'email' => 'email',
		            'password' => 'min:6',
		            'mobile' => 'numeric|digits_between:9,10'
		        ]
		    );
			
		    if($validator->fails()) return $this->apiResponse(['error' => $validator->messages()->first()], true);
			// return \Response::json($validator->messages(), 400);
		    
		    $model = User::where('contact_number', $request->mobile)->orWhere('email', $request->email)->first();
		    
		    if( !$model ) return $this->apiResponse(['message' => 'Invalid login  credentials'], true);
		    
		    $params = $this->mergeRequest($request);

		    //try logging in the user
		    if($model->role_id == 3 && $this->auth->attempt($request->only(['email', 'password']), $request->input('remember'))) {
		      	$user = Auth::user();
		      	$token = md5(uniqid($user->email, true));
		      	$user->token = $token;
		      	$user->save();
		      	// echo  "good ";
		       //  die;
		      	return $this->apiResponse(['message' => 'Successfully Authenticated', 'data'=>$user, 'token' => $token]);
		    }
		    
		    return $this->apiResponse(['message' => 'Invalid login credentials'], true);
		    
		} catch(\Exception $e) {
			
			return $this->apiResponse(['message' => 'Invalid login credentials'], true);
		}
	}


	public function mergeRequest(Request $request)
	{

		if ($request->has('mobile')) {

			$model = User::where('contact_number', $request->mobile)->first();

			$request->merge(['email' => $model->email]);

		} else {

			$model = User::Where('email', $request->email)->first();

			$request->merge(['mobile' => $model->contact_number]);
		}

		return $request;
	}


	public function restaurantSignIn(Request $request)
	{
	    $validator = Validator::make(
	        $request->all(), [
	            'email' => 'required|email',
	            'password' => 'required|min:6'
	        ]
	    );
    	
	    if($validator->fails()) return $this->apiResponse(['error' => $validator->messages()->first()], true);

	    //try logging in the user
	    if($this->auth->attempt($request->only(['email', 'password']), $request->input('remember'))) {
	    	
			$user = Auth::user();
			
			if ($user->role_id != \Config::get('constants.user.restaurant')) {
				return $this->apiResponse(['error' => 'User is not a restaurant owner.', 'token' => null, 'success' => false]);
			}
			$token = md5(uniqid($user->email, true));
			$user->token = $token;
			$user->save();

			return $this->apiResponse(['message' => 'Successfully Autheticated','data'=>$user, 'token' => $token]);
	    }

	    return $this->apiResponse(['message' => 'Invalid login credentials'], true);	    
	}

	
	public function driverSignIn(Request $request)
	{
	    $validator = Validator::make(
	        $request->all(), [
	            'email' => 'email',
	            'password' => 'min:6'
	        ]
	    );    

	    if($validator->fails()) return \Response::json($validator->messages(), 400);


	    //try logging in the user
	    if($this->auth->attempt($request->only(['email', 'password']), $request->input('remember'))) {

			$user = Auth::user();

			if ($user->role_id != 4) {
				return $this->apiResponse(['message' => 'User is not a driver.', 'data'=>null, 'token' => null, 'success' => false]);
			}
			if($user->verified == 1){

			$token = md5(uniqid($user->email, true));
			$user->token = $token;
			$user->save();

			return $this->apiResponse(['message' => 'Successfully Autheticated','data'=>$user, 'token' => $token]);
			}
			else
			{
				return $this->apiResponse(['message' => 'User is not a verified driver.','data'=>null, 'token' => null, 'success' => false]);

			}
	    }

	    return $this->apiResponse(['message' => 'Invalid login credentials'],true);	    
	}


	public function register(Request $request) 
	{
		try
		{
			$rules  = User::$registerRulesApi; 

			$params = $this->splitName($request);

			$validator = Validator::make($params, $rules, [
				'first_name.required' => 'Fullname is required. Please enter your first name.',
				'last_name.required' => 'Fullname is required. Please enter your last name.',
				'countrycode.required' => 'Please enter country code.'
			]);

			if($validator->fails()) return $this->apiResponse(['error' => $validator->messages()->first()],true);
			//check contact number is exist
			$data = User::where('contact_number','=',$request->contact_number)->first();
            if($data)
                return $this->apiResponse(['message' => 'Contact number is already exist'], true);
			$OtpVerification = new OtpVerification();

			$response = $OtpVerification->verifyOTP($request);

			$this->registrar->create($request->all());

			$id = \DB::getPdo()->lastInsertId();
			$user = User::findOrFail($id);
			$user->status = 1;
			$user->save();

			//try logging in the user
		    if($this->auth->attempt($request->only(['email', 'password']), $request->input('remember'))) {
		      	$user = Auth::user();
		      	$token = md5(uniqid($user->email, true));
		      	$user->token = $token;
		      	$user->save();
		      	return $this->apiResponse(['message' => 'Successfully Autheticated', 'data'=>$user, 'token' => $token]);
		    }

			return $this->apiResponse(['message' => 'Successfully Registered','data' => $user]);

		} catch(\Exception $e) {
			return $this->apiResponse(['error' => $e->getMessage()],true);
		}
	}


	public function splitName(Request $request)
	{
		if ($request->has('full_name')) {

			$tempName = explode(' ', $request->get('full_name'), 2);

			if (isset($tempName[0]) && $tempName[0]) $request->merge(['first_name' => $tempName[0]]);

			if (isset($tempName[1]) && $tempName[1]) $request->merge(['last_name' => $tempName[1]]);

			else $request->merge(['last_name' => '']);

		}

		return $request->except('full_name');
	}

}