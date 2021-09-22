<?php

namespace App\Http\Controllers\Admin; //admin add

use Auth,App\User,App\UserRestaurant,App\Restaurent;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller; // using controller class

class LoginController extends Controller {

    /**
     * User model instance
     * @var User
     */
    protected $user;

    /**
     * For Guard
     *
     * @var Authenticator
     */
    protected $auth;


    /* Login get post methods */
    protected function getLogin() {
        
    		if (Auth::viaRemember()) {
  		      return redirect(\Request::segment(2).'/dashboard');
    		} else {
            return \View::make('admin.login'); 
        }

    }


    protected function postLogin(Request $request) {
   
        $authRoleArr = [
          
                \Config::get('constants.user.super_admin'),
                \Config::get('constants.user.admin'),
                \Config::get('constants.user.area_manager'),
                \Config::get('constants.user.restaurant')
           
       ];
       $user = User::where('email', $request->only('email'))->whereIn('role_id',$authRoleArr)->first();
  
       $input = array_merge(['status'=>1],$request->only('email', 'password'));
        
       if ($user && Auth::attempt($input,$request->has('remember_me')))
        {  	
            
            if(Auth::user()->role_id == \Config::get('constants.user.area_manager')){              
              $accessRestaurantIdsArr = UserRestaurant::getRestaurantIds(Auth::user()->id);             
              $childrenIds = Restaurent::getChildrenRestaurantIds($accessRestaurantIdsArr); 
              if(!empty($childrenIds)){
                  $accessRestaurantIdsArr = array_unique(array_merge($childrenIds, $accessRestaurantIdsArr));
              }                        
               \Session::put('access',['role'=>'manager','restaurant_ids'=>$accessRestaurantIdsArr]);
              return redirect('manager/dashboard');
            }elseif(Auth::user()->role_id == \Config::get('constants.user.restaurant')){
              $restauantOwner = Restaurent::select('id','owner_id')->where('owner_id',Auth::user()->id)->first();
              $childrenIds = Restaurent::getChildrenRestaurantIds([$restauantOwner->id]); 
              $accessRestaurantIdsArr = [$restauantOwner->id];
              if(!empty($childrenIds)){
                  $accessRestaurantIdsArr = array_unique(array_merge($childrenIds, $accessRestaurantIdsArr));
              }       
             
              \Session::put('access',['role'=>'restaurant','restaurant_ids'=>$accessRestaurantIdsArr]);              
              return redirect('restaurant/dashboard');
            }else{
                
              \Session::put('access',['role'=>'admin']);
              return redirect('admin/dashboard');
            }            
            
        }
 
 	    return redirect('admin')->withErrors([
	        'email' =>  trans('validation.login'),
	    ]);     		

    } 

    /**
    * Log the user out of the application.
    *
    * @return Response
    */
    protected function getLogout()
    {
        Auth::logout();
        return redirect('/'.\Request::segment(2));
    }

}
