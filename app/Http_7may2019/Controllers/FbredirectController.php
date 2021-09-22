<?php namespace App\Http\Controllers;
use Redirect;
use Socialize;
use App\User, Auth;
use App\Services\Registrar;
use Illuminate\Contracts\Auth\Guard;
//--------
use Mail;
use Hash;
use Socialite;
use Illuminate\Http\Request;
use App\Http\Requests;

//use Illuminate\Contracts\Auth\Registrar;
class FbredirectController extends Controller {

	/*
	|--------------------------------------------------------------------------
	| Home Controller
	|--------------------------------------------------------------------------
	|
	| This controller renders your application's "dashboard" for users that
	| are authenticated. Of course, you are free to change or remove the
	| controller as you wish. It is just here to get your app started!
	|
	*/

	/**
	 * Create a new controller instance.
	 *
	 * @return void
	 */

	/**
	 * Show the application dashboard to the user.
	 *
	 * @return Response
 	*/
 	
	
	public function facebook()
	{
		return Socialize::with('facebook')->redirect();		
	}
 	
	
	public function index(Guard $auth)
	{
		try {
			$fbuser = Socialize::with('facebook')->user();
			$fbuser = $fbuser->user;
			
			$user = User::finduser($fbuser);
			if( !$user )
			{
				$newuser=new User;
				$name	=	explode(" ",$fbuser['name']);
				$newuser->first_name		= $name[0];
				if(!empty($name[1]))
					$newuser->last_name	= $name[1];
				$newuser->email			= $fbuser['id'];
				$newuser->password		= bcrypt($fbuser['id']);
				$newuser->status		= $fbuser['verified'];
				$newuser->login_type	="facebook";
				
				if($newuser->save())
				{
					$auth->login($newuser);
					return redirect('temp');
					/*if($fbuser['verified']	==	1)
					{
						$auth->login($newuser);
						return redirect('temp');
					}
					return redirect('home/registration_successful');*/
				}
				else
					exit("DataBase error");
			}
			if( $user->status == 0 )
			{
				$user->status = 1;
				$user->save();
			}
			
			// Login user
			$auth->login($user);
			
			return redirect('/');
			
		} catch(\Exception $e) {

			return redirect('/');
		}
	}


	public function temp()
	{
		return view('auth.temp');
	}
	
}
