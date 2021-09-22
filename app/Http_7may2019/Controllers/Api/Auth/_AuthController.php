<?php 
namespace App\Http\Controllers\Api\Auth;

use \Auth;
use Illuminate\Http\Request;
use \Validator;
use App\User;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Contracts\Auth\Registrar;

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
  
	public function login(Request $request)
	{
    
    $validator = Validator::make(
        $request->all(),
        [
            'email' => 'email',
            'password' => 'min:6'
        ]
    );    
   
    
    if($validator->fails())
      return \Response::json($validator->messages(), 400);
    
    //try logging in the user
    if($this->auth->attempt($request->only(['email', 'password']), $request->input('remember'))) {
      
      $user = Auth::user();
      $token = md5(uniqid($user->email, true));
      $user->token = $token;
      $user->save();
      return $this->apiResponse(['message' => 'Successfully Autheticated','data'=>$user, 'token' => $token]);
      //return \Response::json(['message' => 'Successfully Autheticated', 'token' => $token]);      
    }
    return $this->apiResponse(['message' => 'Invalid login credentials'],true);
    
	}


	public function register(Request $request) 
	{
		try
		{
			$rules  = User::$rulesApi; 

			$params = $this->splitName($request);

			$validator = Validator::make($params, $rules, [
				'last_name.required' => 'Fullname is required. Please enter your last name.'
			]);

			if($validator->fails()) {
				return $this->apiResponse(['error' => $validator->messages()->first()],true);
			}

			$this->registrar->create($request->all());

			$id = \DB::getPdo()->lastInsertId();
			$user = User::findOrFail($id);

			$user->status = 1;
			$user->save();

			return $this->apiResponse(['message' => 'Successfully Registered','data' => $user]);

		} catch(\Exception $e) {
			return $this->apiResponse(['error'=>$e->getMessage()],true);
		}
	}


	public function splitName(Request $request)
	{
		if ($request->has('full_name')) {

			$tempName = explode(' ', $request->get('full_name'));

			if (isset($tempName[0]) && $tempName[0]) $request->merge(['first_name' => $tempName[0]]);
			if (isset($tempName[1]) && $tempName[1]) $request->merge(['last_name' => $tempName[1]]);

		}

		return $request->except('full_name');
	}


}
