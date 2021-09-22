<?php namespace App\Http\Middleware;

use Closure;
use Illuminate\Contracts\Auth\Guard;
use App\User;

class ApiAuthentication {

  protected $auth;
  
	public function __construct(Guard $auth)
	{
		$this->auth = $auth;
	}  
  
	/**
	 * Handle an incoming request.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @param  \Closure  $next
	 * @return mixed
	 */
	public function handle($request, Closure $next)
	{
    
    $authToken = $request->input('auth-token');
    
    if(empty($authToken))
      return \Response::json(['message' => 'You are not authorized. Please login again.'], 401);
    
    $user = User::where('token', '=', $authToken)->first();
    
    if(!$user)
      return \Response::json(['message' => 'You are not authorized. Please login again.'], 401);
    
    //set the verified user
    $this->auth->setUser($user);
    
		return $next($request);
	}
}
