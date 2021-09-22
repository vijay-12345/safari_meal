<?php 

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\User;
use Session;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Contracts\Auth\Registrar;
use Illuminate\Foundation\Auth\AuthenticatesAndRegistersUsers;
use Illuminate\Http\Request;
use \Mail;
use \Exception;
use Form; use Socialite;
use Validator;

class AuthController extends Controller {

	/*
	|--------------------------------------------------------------------------
	| Registration & Login Controller
	|--------------------------------------------------------------------------
	|
	| This controller handles the registration of new users, as well as the
	| authentication of existing users. By default, this controller uses
	| a simple trait to add these behaviors. Why don't you explore it?
	|
	*/

	use AuthenticatesAndRegistersUsers;
	/**
	 * Create a new authentication controller instance.
	 *
	 * @param  \Illuminate\Contracts\Auth\Guard  $auth
	 * @param  \Illuminate\Contracts\Auth\Registrar  $registrar
	 * @return void
	 */

	public function __construct(Guard $auth, Registrar $registrar)
	{		
		$this->auth = $auth;
		$this->registrar = $registrar;
		$this->middleware('guest', ['except' => 'getLogout']);
	}

	public function postRegister(Request $request)
	{
		$credentials = $request->only('email');
		$user = User::finduser($credentials);		
		$validator = $this->registrar->validator($request->all());				
		if ($validator->fails()) {
			return view('auth.register')->with('errors', $validator->errors());				
		}
		$this->registrar->create($request->all());
		$user = User::finduser($credentials);		
		$user->remember_token = str_random(60);
		$user->save();	
		$subject = trans('email.verify_email');
		try {
			Mail::send('emails.verify', ['lastname'=> $user['last_name'],'firstname'=> $user['first_name'],'remembertoken'=> $user->remember_token], function($m)  use ($user, $subject) {
				$admin_email = \Config::get('constants.administrator.email');
				$m->from($admin_email, \Config::get('constants.site_name'));
				$m->to($user->email, $user->first_name)->subject( $subject );
			});
		} catch(\Exception $e) {
			print_r($e->getMessage());exit;
		}
		return view('auth.registration_successful');	
	}
	

	public function confirm()
    {
		$remembertoken = $_GET['remembertoken'];
        if( ! $remembertoken) {
            throw new InvalidConfirmationCodeException;
        }
		$user = User::finduser(array('remember_token'=>$remembertoken));
        if ( ! $user) {
        	Session::put("token-invalid-message", 'Token code is not valid.');
        	return redirect('/');
        }
        $user->status = 1;
        $user->save();
        Session::put("verified-message", 'You have successfully verified your account. Please Login to continue....');	
        return redirect('/');
	}
	
	public function verifyNumber(Request $request)
	{
	    $validator = Validator::make(
	        $request->all(),[
	            'mobile' => 'required|numeric|digits_between:10,12',
	        ]
	    );
	    if($validator->fails()) {
	    	$html = view('auth.login')->with(['errors'=>$validator->errors(), 'showLogin'=>false])->render();
	    	return json_encode(array('success'=>false, 'html'=> $html));
	    }
		$user = User::where('contact_number', $request->mobile)->first();

		Session::put("loginNumber", $request->mobile);
		if ( !$user ) {
	    	return json_encode(array('success'=>true, 'number_exist'=> false));
        } else {
        	$html = view('auth.login')->with(['showLogin'=>true])->render();
	    	return json_encode(array('success'=>false,'html'=> $html,'number_exist'=> true));
        }
	}


	public function postLogin(Request $request) {
		$validator = $this->registrar->loginvalidator($request->all());		
		if ($validator->fails()) {
			http_response_code(400);
			$html = view('auth.login')->with(['errors'=>$validator->errors(), 'showLogin'=>true])->render();
			return json_encode(array('success'=>false,'html'=> $html));
		}
		// $this->validate($request, [
		// 	'email' => 'required|email', 'password' => 'required',
		// ]);
		$credentials = $request->only('email', 'password');

		$user = User::finduser($request);
		if(!$user) {
			$validator->getMessageBag()->add('email', 'These credentials do not match our records.');
			$html = view('auth.login')->with(['errors'=>$validator->errors(), 'showLogin'=>true])->render();
			return json_encode(array('success'=>false,'html'=> $html));
		}

		if($user && $user->status != '1') {
			$html = view('auth.login')->with(['notverified'=>"Please Verify Your Account Before Login", 'showLogin'=>true])->render();
			return json_encode(array('success'=>false,'html'=> $html));
		}

		if($user && $user->role_id != \Config::get('constants.user.customer')) {
			$html = view('auth.login')->with(['notverified'=>"Only customer can login from here", 'showLogin'=>true])->render();
			return json_encode(array('success'=>false,'html'=> $html));
		}

		$credentials = array_merge($credentials, ['email' => $user->email]);
		if ($user && $this->auth->attempt($credentials, $request->has('remember'))) {
			return json_encode(array('success'=>true));
			//return redirect('temp');
		} else {
			$validator->getMessageBag()->add('email', 'These credentials do not match our records.');
			$html = view('auth.login')->with(['errors'=>$validator->errors(), 'showLogin'=>true])->render();
			return json_encode(array('success'=>false,'html'=> $html));
		}
		// http_response_code(400);
		// return redirect('auth/login')
		// 	->withInput($request->only('email', 'remember'))
		// 	->withErrors([
		// 		'email' => $this->getFailedLoginMessage(),
		// ]);
	}
	

	/**
	 * Get the failed login message.
	 *
	 * @return string
	 */
	protected function getFailedLoginMessage()
	{
		return trans('auth.failed');
	}

}