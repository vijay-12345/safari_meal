<?php 
namespace App\Http\Controllers\Api;

use \Auth;
use Request;
use Validator;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Contracts\Auth\Registrar;
use App\Menu;
use App\Product;
use Illuminate\Pagination\Paginator;

class MenuController extends \App\Http\Controllers\Controller {

 
	/**
	 * Create a new controller instance.
	 *
	 * @return void
	 */
	public function __construct(){    
		//$this->middleware('auth.api', ['except' => ['login']]);

	}

	public function menuList(){

		$inputs = Request::all();
		if(isset($inputs['restaurant_id'])){
			\DB::enableQueryLog();
			$data = Product::lang('product')
			->join('menu','product.menu_id','=','menu.id')
			->where('product.restaurant_id','=',$inputs['restaurant_id'])		
			->select('menu.name as menu_name','menu.id as menu_id')->get();				
			return $this->apiResponse(['data'=>$data]);
		}else{
			return $this->apiResponse(['message'=>'Please provide restaurant_id'],true);
		}						

	}
	
					
}
