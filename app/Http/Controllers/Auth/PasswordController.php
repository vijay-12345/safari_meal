<?php namespace App\Http\Controllers\Auth;

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

	protected $redirectTo;

	public function __construct(Guard $auth, PasswordBroker $passwords)
	{
		$this->redirectTo = '/';
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
			return redirect(url().'/password/email')->with(['errors'=> $validator->messages(),'email'=>$request->input('email')]); 
		}else{				
			$response = $this->passwords->sendResetLink($request->only('email'), function($m){
				$m->subject($this->getEmailSubject());
			});			
		}	
		switch ($response)
		{	
			case PasswordBroker::RESET_LINK_SENT:								
				return redirect(url().'/password/email')->withErrors(['email'=>$request->input('email'),'status' => trans($response)]);

			case PasswordBroker::INVALID_USER:
				return redirect(url().'/password/email')->withErrors(['email'=>$request->input('email'),'status' => trans($response)]);								
		}
	}	

/**
	 * Get the e-mail subject line to be used for the reset link email.
	 *
	 * @return string
	 */
	protected function getEmailSubject()
	{
		return isset($this->subject) ? $this->subject : trans('email.password_reset_link');
	}

}
