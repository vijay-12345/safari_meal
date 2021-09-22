<?php 
namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Contracts\Auth\PasswordBroker;
use Illuminate\Foundation\Auth\ResetsPasswords;
use Illuminate\Http\Request;
use Validator,Session;

class PasswordController extends Controller {

	/*
	|--------------------------------------------------------------------------
	| Password Reset Controller
	|--------------------------------------------------------------------------
	|
	| This controller is responsible for handling password reset requests
	| and uses a simple trait to include this behavior. You're free to
	| explore this trait and override any methods you wish to tweak.
	|
	*/
	use ResetsPasswords;

	/**
	 * Create a new password controller instance.
	 *
	 * @param  \Illuminate\Contracts\Auth\Guard  $auth
	 * @param  \Illuminate\Contracts\Auth\PasswordBroker  $passwords
	 * @return void
	 */
	public function __construct(Guard $auth, PasswordBroker $passwords)
	{

		$this->auth = $auth;
		$this->passwords = $passwords;
		$this->middleware('guest');

	}
	public function postEmail(Request $request)
	{	
		
		$validator = Validator::make(
			$request->all(),
			[
				'email' => 'email|required',
			]
		);
		if($validator->fails())	{
			return $this->apiResponse(['error'=>$validator->messages()],true);				
		}else{				
			$response = $this->passwords->sendResetLink($request->only('email'), function($m){
				$m->subject($this->getEmailSubject());
			});			
		}
		switch ($response)
		{
			case PasswordBroker::RESET_LINK_SENT:
				return $this->apiResponse(['message'=>'Please check your email for reset password']);				
			case PasswordBroker::INVALID_USER:
				return $this->apiResponse(['error' => trans('We can\'t find a User with that e-mail address.')],true);				
											
		}							
		
	}	
}
