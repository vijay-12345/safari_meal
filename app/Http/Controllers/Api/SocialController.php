<?php 
namespace App\Http\Controllers\Api;

use \Auth;
use Mail;
use Hash;
use App\User;
use Socialite;
use Illuminate\Http\Request;
use App\Http\Requests;

class SocialController extends \App\Http\Controllers\Controller {

 
	/**
	 * Create a new controller instance.
	 *
	 * @return void
	 */
	public function __construct(){    
		$this->middleware('auth.api', ['except' => ['getSocialLogin']]);
	}
	public function getSocialLogin()
	{	

		$arguments = \Request::all();	

		if( isset( $arguments['id'] ) &&  $arguments['type'] == 'facebook' )
			$arguments['fb_id'] = $arguments['id'];
		elseif( isset( $arguments['id'] ) &&  $arguments['type'] == 'twitter' )
			$arguments['twitter_id'] = $arguments['id'];
		elseif( isset( $data['id'] ) &&  $data['type'] == 'google' )
			$arguments['google_id'] = $arguments['id'];
		elseif( isset( $arguments['id'] ) &&  $arguments['type'] == 'linkedin' )
			$arguments['linked_id'] = $arguments['id'];

		if( !empty( $arguments ) ){			
			if( isset( $arguments['email']) ){
				$userDbObj = User::whereEmail($arguments['email'])->first();

				if(!$userDbObj){	
					//register user
					$newuser=new User;
					$name	=	explode(" ",$arguments['name']);
					$newuser->first_name		= $name[0];
					if(!empty($name[1]))
						$newuser->last_name	= $name[1];	
					$newuser->email			= 	$arguments['email'];
					if(isset($arguments['password'])){
						$newuser->password		= 	bcrypt($arguments['password']);
					}
					if(isset($arguments['status'])){
						$newuser->status	= 	$arguments['status'];
					}else{
						$newuser->status	= 1;
					}					
					$newuser->login_type	=	"facebook";
					$userDbObj = $newuser->save();
					$userDbObj = User::whereEmail($arguments['email'])->first();
				}
			   //try logging in the user
			    //if(Auth::attempt(['email'=>$arguments['email'],'password'=>$arguments['password']])) {
			      
			      //$user = Auth::user();
			      $token = md5(uniqid($userDbObj->email, true));
			      $userDbObj->token = $token;
			      $userDbObj->save();
			      return $this->apiResponse(['message' => 'Successfully Autheticated','data'=>$userDbObj, 'token' => $token]);
	
			    //}
			    return $this->apiResponse(['message' => 'Invalid login credentials'],true);
			}
			return $this->apiResponse(['message' => 'Invalid login credentials'],true);
		}
		return $this->apiResponse(['message' => 'Invalid login credentials'],true);
	}		
					
}
